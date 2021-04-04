Dropday Order Automation module
===============

## How to install the extension?

* Download the .zip or tar.gz file from gitlab repository.
* Unzip the file and follow the instructions.
* Navigate to `Magento` `[Magento]/app/code/` either through `SFTP` or `SSH`.
* Upload `Dropday/OrderAutomation` directory with the extension.
* Open the terminal and navigate to Magento root directory.

* Run the following command to enable Dropday extension:
```php bin/magento module:enable Dropday_OrderAutomation```
* Run the `Magento` setup upgrade:
```php bin/magento setup:upgrade```
* Run the `Magento` Dependencies Injection Compile:
```php bin/magento setup:di:compile```
* Run the `Magento` Static Content deployment:
```php bin/magento setup:static-content:deploy```
* Login to `Magento` Admin and navigate to `System > Cache Management`.
* Flush the cache storage by selecting `Flush Cache Storage`.

## Configuration

Stores &rarr; Configuration &rarr; Dropday &rarr; Order Automation

- General Settings 
   - Enabled - Enable Order Automation
   - Test Mode - Enable Test mode
   - Account ID - Account ID from Dropday Dashboard
   - API Key - Secret Key be provided by Dropday Dashboard 
