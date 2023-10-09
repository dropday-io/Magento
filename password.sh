#!/bin/bash

# Name of the Magento container
CONTAINER_NAME="magento-magento-1"

# Admin user details
ADMIN_USER="admin-testing"
ADMIN_PASSWORD="NewPass123"
ADMIN_EMAIL="admin-testing@example.com"

# Enter the Magento Docker container, create the admin user, and then display the result.
docker exec -it $CONTAINER_NAME bash -c "
echo 'Creating admin user...'
magento admin:user:create --admin-user='$ADMIN_USER' --admin-password='$ADMIN_PASSWORD' --admin-email='$ADMIN_EMAIL' 

if [ \$? -eq 0 ]; then
    echo 'Admin user creation successful!'
else
    echo 'Failed to create the admin user.'
fi
"

echo "Script execution completed."

