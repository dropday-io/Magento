#!/bin/bash

# Wait for a container based on its image name
IMAGE_NAME="magento2"

echo "Waiting for the $IMAGE_NAME container to be ready..."

while true; do
    # Get the container ID based on the image name
    DB_CONTAINER=$(docker ps | grep '\-db' | awk '{print $1}')
    
    if [[ -z "$DB_CONTAINER" ]]; then
        echo "Container with image $IMAGE_NAME is not yet started. Waiting..."
        sleep 2
        continue
    fi

    # Check the container's status
    CONTAINER_STATUS=$(docker inspect --format '{{.State.Status}}' "$DB_CONTAINER")

    if [[ $CONTAINER_STATUS -eq "running" ]]; then
        echo "Container with image $IMAGE_NAME is running."

        # Wait for MySQL to be ready

        # echo "Checking database readiness..."
        for i in {1..150}; do
            # docker exec $DB_CONTAINER sh -c "mysqladmin ping -h'db' -u'magento' -p'magento'"

            if docker exec $DB_CONTAINER sh -c "mysqladmin ping -h'db' -u'magento' -p'magento'" &>/dev/null; then
                echo "Database is ready."
                break
            fi
            echo "Waiting for database to initialize.. $i"
            sleep 1
        done

        MAGENTO_CONTAINER=$(docker ps | grep '\-magento' | awk '{print $1}')

        # Execute your desired command here
        docker exec $MAGENTO_CONTAINER sh -c "php bin/magento setup:install \
            --admin-firstname=John \
            --admin-lastname=Doe \
            --admin-email=johndoe@example.com \
            --admin-user=admin \
            --admin-password='SomePassword123' \
            --base-url=http://localhost \
            --base-url-secure=https://localhost \
            --backend-frontname=admin \
            --db-host=db \
            --db-name=magento \
            --db-user=magento \
            --db-password=magento \
            --use-rewrites=1 \
            --language=en_US \
            --currency=USD \
            --timezone=America/New_York \
            --use-secure-admin=0 \
            --admin-use-security-key=1 \
            --session-save=files \
            --use-sample-data \
            && php bin/magento cache:flush \
            && chown -R www-data:www-data ."

        docker exec $MAGENTO_CONTAINER sh -c "php bin/magento config:set payment/checkmo/active 1"

        exit 0
    else
        echo "Waiting for container $DB_CONTAINER to start..."
        sleep 2
    fi
done
