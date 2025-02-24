#!/bin/bash

set -e

chown -R www-data:www-data /var/www/html
chmod -R +r /var/www/html
chmod -R +w /var/www/html
