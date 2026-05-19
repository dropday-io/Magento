install:
	docker compose down --volumes \
	&& docker compose up -d \
	&& bash magento-installation.sh