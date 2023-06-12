# Install Magento
RUN php bin/magento setup:install --admin-firstname=John --admin-lastname=Doe --admin-email=johndoe@example.com --admin-user=admin --admin-password='SomePassword123' --base-url=http://localhost --base-url-secure=https://localhost --backend-frontname=admin --db-host=db --db-name=magento2 --db-user=magento2 --db-password=magento2 --use-rewrites=1 --language=en_US --currency=USD --timezone=America/New_York --use-secure-admin=0 --admin-use-security-key=1 --session-save=files --use-sample-data \
    && bin/magento cache:flush \
    && chown -R www-data:www-data .