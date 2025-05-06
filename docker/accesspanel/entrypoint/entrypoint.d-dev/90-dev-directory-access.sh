#!/bin/bash

set -e

chown -R www-data:www-data /var/www/html
chmod -R u+r,u+w,g+r,g+w,o+r,o+w /var/www/html
