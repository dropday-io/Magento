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

$productFactory = $objectManager->create('\Magento\Catalog\Model\ProductFactory');
$product = $productFactory->create();
$product->setName('Sample Product');
$product->setSku('sample-product');
$product->setPrice(25.00);
$product->setTypeId('simple');
$product->setAttributeSetId(4); // 4 is for the default attribute set. It may vary based on your setup.
$product->setStatus(1);
$product->setVisibility(4);
$product->setWebsiteIds(array(1));
$product->setStockData(
    array(
        'use_config_manage_stock' => 0,
        'manage_stock' => 1,
        'is_in_stock' => 1,
        'qty' => 100
    )
);
$product->save();
echo "Product created successfully.";