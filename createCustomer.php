<?php
/**
*   Dropday
*
*   Do not copy, modify or distribute this document in any form.
*
*   @author     Matthijs <suport@dropday.io>
*   @copyright  Copyright (c) 2013-2023 Dropday (https://dropday.io)
*   @license    Proprietary Software
*
*/

require 'app/bootstrap.php';
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');

$customerFactory = $objectManager->get('\Magento\Customer\Model\CustomerFactory');
$websiteId = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getWebsite()->getWebsiteId();
$customer = $customerFactory->create();
$customer->setWebsiteId($websiteId);
$customer->setEmail('sample@customer.com');
$customer->setFirstname('Sample');
$customer->setLastname('Customer');
$customer->setPassword('samplePassword');
$customer->save();

echo "Customer created successfully.";
