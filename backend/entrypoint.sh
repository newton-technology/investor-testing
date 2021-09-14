#!/bin/bash
/usr/bin/rm -f /run/php-fpm.pid
/opt/remi/php74/root/usr/sbin/php-fpm --nodaemonize --pid /run/php-fpm.pid & /opt/remi/php74/root/bin/php-fpm-prometheus server --phpfpm.scrape-uri tcp://127.0.0.1:9000/status --web.listen-address ":9001";
