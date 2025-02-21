#!/bin/bash

set -e

mkdir -p /var/www/html/var/log/
chown -R root:root /var/www/html
chown -R www-data:www-data /var/www/html/var
