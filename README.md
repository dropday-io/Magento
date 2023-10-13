Magento Extension for Dropday
===============

Create a directory on your system

sudo mkdir sites

cd sites

Please run this Command to execute scripts and docker

curl -s https://raw.githubusercontent.com/devopsoptimbytes/docker-magento/master/lib/onelinesetup | bash -s -- magento.test 2.4.6-p2 community\n

In the process it will ask for credentails for repo.magento.com

username: 217ab4e2c17a820d861e3f917785e066
password:  0497283a6f53ed3c3358752151ff2bfc

After completion create credetials of magento dashboard with below command


bin/magento admin:user:create --admin-user='username' --admin-password='password123' --admin-email='user@example.com' --admin-firstname='FirstName'
--admin-lastname='LastName'\n

After creation user run below commadn so that disable module.


bin/magento module:disable Magento_AdminAdobeImsTwoFactorAuth Magento_TwoFactorAuth\n


you can acccess dashboard on browser magento.test/admin
