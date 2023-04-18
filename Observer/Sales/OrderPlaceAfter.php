<?php

namespace Dropday\OrderAutomation\Observer\Sales;

use Dropday\OrderAutomation\Helper\Data;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class OrderPlaceAfter implements ObserverInterface
{
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var Json
     */
    private $json;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * OrderPlaceAfter constructor.
     * @param Data $helper
     * @param Json $json
     * @param LoggerInterface $logger
     */
    public function __construct(
        Data $helper,
        Json $json,
        LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->json = $json;
        $this->logger = $logger;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();
        
        // Check if the order is paid
        if($order->getState() !== Order::STATE_PROCESSING) {
            return;
        }

        try {
            if (!$this->helper->isEnabled()) {
                return;
            }
            if (!$this->helper->getAccountId() || !$this->helper->getApiKey()) {
                $this->logger->warning('Dropday Automation: Missing Account ID or API Key in system configuration!');
                return;
            }
            $client = $this->helper->getClient();
            $client->setUri($this->helper->getBaseUrl() . '/orders');
            $client->setMethod(\Zend_Http_Client::POST);
            $client->setParameterPost($this->helper->getOrderRequestData($order));
            if ($this->helper->isTestMode()) {
                $this->logger->info('Dropday Request: ' . print_r($this->helper->getOrderRequestData($order), true));
            }
            $response = $this->json->unserialize($client->request()->getBody());
            $statusCode = $client->request()->getStatus();
            if (isset($response['reference'])) {
                $order->setData('dropday_order_id', $response['reference']);
                $order->save();
                $order->addCommentToStatusHistory('Dropday API Success (' . $statusCode . ') ' . $response['message']);
            }
            if ($statusCode != 200) {
                $order->addCommentToStatusHistory('Dropday API Error: (' . $statusCode . ') ' . $response['message']);
            }
        } catch (\Exception $e) {
            $this->logger->critical('Dropday Automation: ' . $e->getMessage());
        }
    }
}
