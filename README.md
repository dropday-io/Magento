Magento Extension for Dropday
===============

## How to install the extension?
Dropday.io is a service for automates orders. With Dropday.io forward orders automatically to your suppliers with different methods, for example, place an XML file on FTP, send an email or let a Dropday employee place a manual order on another webshop. This solution is ideal for dropshipping, fulfillment or other types or supplier automation.

* Execute the following commands using `SSH`:

    ```cd app/code```

    ```mkdir Dropday```

    ```cd Dropday```

    ```git clone https://github.com/dropday-io/Magento.git OrderAutomation```

    ```cd ../../..```

    ```php bin/magento setup:upgrade```

    ```php bin/magento setup:di:compile```

    ```php bin/magento setup:static-content:deploy```

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
This extension will connect to the API and forward your orders to Dropday.io. To connect to the API make an account at [Dropday.io](https://dropday.io/register).
