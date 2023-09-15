Magento Extension for Dropday
===============

Dropday.io is a service to automate your webshop orders. With Dropday.io you can forward orders automatically to your suppliers via different methods, for example by placing a XML file on FTP, sending an email or letting a Dropday employee place a manual order on another webshop. This solution is ideal for dropshipping, fulfillment or other types or supplier automation.

This extension will connect to the API and forward your orders to Dropday.io. To connect to the API make an account at [Dropday.io](https://dropday.io/register).

## How to install the extension?

### 1. Installion via Composer

```
composer require dropday-io/module-orderautomation
```

### 2. Enable

```
php bin/magento module:enable Dropday_OrderAutomation
php bin/magento setup:upgrade
php bin/magento cache:clean
```

### 3. Configure

Stores &rarr; Configuration &rarr; Dropday &rarr; Order Automation

- General Settings
   - Enabled - Enable Order Automation
   - Test Mode - Enable Test mode
   - Account ID - Account ID from Dropday Dashboard
   - API Key - Secret Key be provided by Dropday Dashboard
