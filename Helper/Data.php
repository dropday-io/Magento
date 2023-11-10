<?php

namespace Dropday\OrderAutomation\Helper;

use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Sales\Model\Order;
use Magento\Framework\HTTP\Client\CurlFactory;

class Data extends AbstractHelper
{
    const XML_PATH_ENABLED = 'dropday/order_automation/enabled';
    const XML_PATH_TEST = 'dropday/order_automation/test_mode';
    const XML_PATH_ACCOUNT_ID = 'dropday/order_automation/account_id';
    const XML_PATH_API_KEY = 'dropday/order_automation/api_key';

    const BASE_URL = 'https://dropday.io/api/v1';

    /**
     * @var Product
     */
    private $productHelper;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var CurlFactory
     */
    private $curlFactory;

    /**
     * @param Context $context
     * @param Curl $curl
     * @param Product $productHelper
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        Context $context,
        Product $productHelper,
        CategoryRepository $categoryRepository,
        CurlFactory $curlFactory
    ) {
        parent::__construct($context);
        $this->productHelper = $productHelper;
        $this->categoryRepository = $categoryRepository;
        $this->curlFactory = $curlFactory;
    }

    /**
     * Initialize Magento Curl Client with headers for authentication
     *
     * @return \Magento\Framework\HTTP\Client\Curl
     */
    public function getClient()
    {
        $client = $this->curlFactory->create();
        $client->addHeader("Content-Type", "application/json");
        $client->addHeader("Accept", "application/json");
        $client->addHeader("account-id", $this->getAccountId());
        $client->addHeader("api-key", $this->getApiKey());
        return $client;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return self::BASE_URL;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLED);
    }

    /**
     * @return bool
     */
    public function isTestMode()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TEST);
    }

    /**
     * @return string
     */
    public function getAccountId()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ACCOUNT_ID);
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_API_KEY);
    }

    /**
     * @param Order $order
     * @return array
     * @throws NoSuchEntityException
     */
    public function getOrderRequestData(Order $order)
    {
        $address = $order->getShippingAddress();
        $params = [
            'external_id' => $order->getIncrementId(),
            'source' => $this->getStoreName(),
            'test' => $this->isTestMode(),
            'total' => $order->getGrandTotal(),
            'shipping_cost' => $order->getShippingAmount(),
            'email' => $order->getCustomerEmail(),
            'shipping_address' => [
                'first_name' => $address->getFirstname(),
                'last_name' => $address->getLastname(),
                'company_name' => $address->getCompany(),
                'address1' => $address->getStreetLine(1),
                'address2' => $address->getStreetLine(2),
                'postcode' => $address->getPostcode(),
                'state' => $address->getRegion(),
                'city' => $address->getCity(),
                'country' => $address->getCountryId(),
                'phone' => $address->getTelephone()
            ],
            'products' => []
        ];
        $skus = [];
        foreach ($order->getAllVisibleItems() as $item) {
            if (!in_array($item->getSku(), $skus)) {
                $skus[] = $item->getSku();
                $params['products'][] = [
                    'external_id' => $item->getProduct()->getId(),
                    'name' => $item->getProduct()->getName(),
                    'reference' => $item->getSku(),
                    'quantity' => (int)$item->getQtyOrdered(),
                    'price' => (float)$item->getPrice(),
                    'image_url' => $this->productHelper->getImageUrl($item->getProduct()),
                    'category' => $this->getCategoryName($item->getProduct()),
                ];
            }
        }
        return $params;
    }

    /**
     * @param $product
     * @return string|null
     * @throws NoSuchEntityException
     */
    protected function getCategoryName($product)
    {
        $ids = $product->getCategoryIds();
        foreach ($ids as $id) {
            return $this->categoryRepository->get($id)->getName();
        }
    }

    /**
     * @return bool
     */
    public function getStoreName()
    {
        return $this->scopeConfig->getValue('general/store_information/name')
            ? $this->scopeConfig->getValue('general/store_information/name'): 'Magento';
    }
}
