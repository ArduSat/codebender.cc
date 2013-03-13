#!/bin/bash
CACHE_DIR=Symfony/app/cache
LOG_DIR=Symfony/app/logs
echo date.timezone = America/Los_Angeles >> /etc/php5/apache2/php.ini
echo date.timezone = America/Los_Angeles >> /etc/php5/cli/php.ini
echo extension=mongo.so >> /etc/php5/cli/php.ini
echo extension=mongo.so >> /etc/php5/apache/php.ini
apt-get install apache2 php5-sqlite php-apc php5-intl php-pear php5-dev
if [ ! -d $CACHE_DIR ]; then
	mkdir -p $CACHE_DIR
	chown $SUDO_USER:$SUDO_USER $CACHE_DIR
fi
chmod 777 -R $CACHE_DIR

if [ ! -d $LOG_DIR ]; then
	mkdir -p $LOG_DIR
	chown $SUDO_USER:$SUDO_USER $LOG_DIR
fi
chmod 777 -R $LOG_DIR

pecl install mongo-1.2.12

cd Symfony && php bin/vendors install --reinstall && \
	chown -R $SUDO_USER:$SUDO_USER vendor && cd -
