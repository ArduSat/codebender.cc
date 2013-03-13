#!/bin/bash
CACHE_DIR=Symfony/app/cache
LOG_DIR=Symfony/app/logs
# echo date.timezone = America/Los_Angeles >> /etc/php5/apache2/php.ini
# echo date.timezone = America/Los_Angeles >> /etc/php5/cli/php.ini
apt-get install apache2 php5-sqlite php-apc php5-intl
if [ ! -d $CACHE_DIR ]; then
	mkdir -p $CACHE_DIR
	chown $USER:$USER $CACHE_DIR
fi
chmod 777 -R $CACHE_DIR

if [ ! -d $LOG_DIR ]; then
	mkdir -p $LOG_DIR
	chown $USER:$USER $LOG_DIR
fi
chmod 777 -R $LOG_DIR
