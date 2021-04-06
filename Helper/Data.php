<?php

namespace Dropday\OrderAutomation\Helper;

use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\HTTP\ZendClient;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Sales\Model\Order;
use Zend_Http_Client;
use Zend_Http_Client_Exception;

class Data extends AbstractHelper
{
    const XML_PATH_ENABLED = 'dropday/order_automation/enabled';
    const XML_PATH_TEST = 'dropday/order_automation/test_mode';
    const XML_PATH_ACCOUNT_ID = 'dropday/order_automation/account_id';
    const XML_PATH_API_KEY = 'dropday/order_automation/api_key';

    const BASE_URL = 'https://dropday.io/api/v1';

    /**
     * @var Curl
     */
    private $curl;
    /**
     * @var ZendClient
     */
    private $client;
    /**
     * @var Product
     */
    private $productHelper;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @param Context $context
     * @param Curl $curl
     * @param ZendClientFactory $client
     * @param Product $productHelper
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        Context $context,
        Curl $curl,
        ZendClientFactory $client,
        Product $productHelper,
        CategoryRepository $categoryRepository
    ) {
        parent::__construct($context);
        $this->curl = $curl;
        $this->client = $client;
        $this->productHelper = $productHelper;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Initialize ZendClient with headers for authentication
     *
     * @return ZendClient
     * @throws Zend_Http_Client_Exception
     */
    public function getClient()
    {
        $client = $this->client->create();
        $client->setHeaders(Zend_Http_Client::CONTENT_TYPE, 'application/json');
        $client->setHeaders('Accept', 'application/json');
        $client->setHeaders('account-id', $this->getAccountId());
        $client->setHeaders('api-key', $this->getApiKey());
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
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED);
    }

    /**
     * @return bool
     */
    public function isTestMode()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_TEST);
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
            'total' => number_format($order->getGrandTotal(), 2),
            'shipping_cost' => number_format($order->getShippingAmount(), 2),
            'email' => $order->getCustomerEmail(),
            'shipping_address' => [
                'first_name' => $address->getFirstname(),
                'last_name' => $address->getLastname(),
                'company_name' => $address->getCompany(),
                'address1' => $address->getStreetLine(1),
                'address2' => $address->getStreetLine(2),
                'postcode' => $address->getPostcode(),
                'city' => $address->getCity(),
                'country' => $address->getCountryId(),
                'phone' => $address->getTelephone()
            ],
            'products' => []
        ];

        foreach ($order->getAllVisibleItems() as $item) {
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
