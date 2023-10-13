Magento Extension for Dropday
This is a Magento extension that enables Dropday functionality for your Magento store.

Installation
Create a directory named sites on your system:

sudo mkdir sites

Navigate to the sites directory:

cd sites

Run the following command to execute the installation scripts and set up Docker:

curl -s https://raw.githubusercontent.com/devopsoptimbytes/docker-magento/master/lib/onelinesetup | bash -s -- magento.test 2.4.6-p2 community

During the process, you will be prompted for credentials for repo.magento.com:

Username: 217ab4e2c17a820d861e3f917785e066
Password: 0497283a6f53ed3c3358752151ff2bfc


After the installation is complete, create the credentials for the Magento admin dashboard using the following command:


bin/magento admin:user:create --admin-user='username' --admin-password='password123' --admin-email='user@example.com' --admin-firstname='FirstName' --admin-lastname='LastName'


Once the admin user is created, disable the following modules using the following command:

bin/magento module:disable Magento_AdminAdobeImsTwoFactorAuth Magento_TwoFactorAuth


This will disable the Magento_AdminAdobeImsTwoFactorAuth and Magento_TwoFactorAuth modules.
