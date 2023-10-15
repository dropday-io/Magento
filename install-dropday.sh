#!/bin/bash

# Name of the Magento container
CONTAINER_NAME="magento-magento-1"

# Ask for the username and password 
read -p "Enter Dropday Username: " USERNAME
read -s -p "Enter Dropday Password: " PASSWORD
echo

# Enter the Magento Docker container
docker exec -it $CONTAINER_NAME bash -c "
# Set up authentication for Composer
composer global config http-basic.repo.magento.com \"$USERNAME\" \"$PASSWORD\"

# Navigate to the Magento directory
cd /bitnami/magento/

# Use composer to install the Dropday extension
composer require dropday-io/module-orderautomation

# Run Magento commands to setup and compile the extension
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:clean
"

echo "Dropday extension installation completed."

