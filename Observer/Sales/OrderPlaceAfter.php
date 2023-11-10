<?php

namespace Dropday\OrderAutomation\Observer\Sales;

use Dropday\OrderAutomation\Helper\Data;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

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
        LoggerInterface $logger,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->helper = $helper;
        $this->json = $json;
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        try {
            if (!$this->helper->isEnabled()) {
                return;
            }

            // Additional checks for account ID and API key
            if (!$this->helper->getAccountId() || !$this->helper->getApiKey()) {
                $this->logger->warning('Dropday Automation: Missing Account ID or API Key in system configuration!');
                return;
            }

            $client = $this->helper->getClient();
            $postData = $this->helper->getOrderRequestData($order);

            // POST request to the API
            $client->post($this->helper->getBaseUrl() . '/orders', json_encode($postData));

            // Logging for test mode
            if ($this->helper->isTestMode()) {
                $this->logger->info('Dropday Request: ' . json_encode($postData));
            }

            // Handling the response
            $responseBody = $client->getBody();
            $response = $this->json->unserialize($responseBody);
            $statusCode = $client->getStatus();

            if ($statusCode == 200 && isset($response['reference'])) {
                // Update order data and save
                $order->setData('dropday_order_id', $response['reference']);
                $this->orderRepository->save($order);

                // Add order comment
                $order->addCommentToStatusHistory('Dropday API Success (' . $statusCode . ') ' . $response['message']);
            } else {
                // Add error comment to order
                $order->addCommentToStatusHistory('Dropday API Error: (' . $statusCode . ') ' . json_encode($response));
            }
        } catch (\Exception $e) {
            // Log critical errors
            $this->logger->critical('Dropday Automation: ' . $e->getMessage());
        }
    }
}
