#!/bin/bash

set -e

sudo -u www-data ./bin/console doctrine:database:create --if-not-exists --no-interaction
sudo -u www-data ./bin/console doctrine:migrations:migrate --no-interaction
