Dropday Order Automation module
===============

## How to install the extension?

* Download the .zip or tar.gz file from gitlab repository.
* Unzip the file and follow the instructions.
* Navigate to `Magento` `[Magento]/app/code/` either through `SFTP` or `SSH`
* Create a folder ```mkdir -p Dropday/OrderAutomation``` and execute the following commands:

    ```cd Dropday/OrderAutomation```

    ```git clone https://github.com/dropday-io/Magento.git .```

* Navigate to `Magento` root directory
* Run the following command to enable Dropday extension:
```php bin/magento module:enable Dropday_OrderAutomation```
* Run the `Magento` setup upgrade:
```php bin/magento setup:upgrade```
* Run the `Magento` Dependencies Injection Compile:
```php bin/magento setup:di:compile```
* Run the `Magento` cache clean commands:

    ```php bin/magento cache:clean```

    ```php bin/magento cache:flush```

If your Magento is running in production mode then just run the following command:

```php bin/magento deploy:mode:set production```

## Configuration

Stores &rarr; Configuration &rarr; Dropday &rarr; Order Automation

- General Settings
   - Enabled - Enable Order Automation
   - Test Mode - Enable Test mode
   - Account ID - Account ID from Dropday Dashboard
   - API Key - Secret Key be provided by Dropday Dashboard
