#!make
include application/.env
include application/.env.local
export $(shell sed 's/=.*//' application/.env)
export $(shell sed 's/=.*//' application/.env.local)

DOCKER_COMPOSE_CMD_PROD=docker compose --progress plain --env-file application/.env --env-file application/.env.local -f docker-compose.prod.yaml
DOCKER_COMPOSE_CMD_DEV=docker compose --progress plain --env-file application/.env --env-file application/.env.local -f docker-compose.dev.yaml

ifeq ("$(APP_ENV)","prod")
   DOCKER_COMPOSE_CMD=${DOCKER_COMPOSE_CMD_PROD}
else
   DOCKER_COMPOSE_CMD=${DOCKER_COMPOSE_CMD_DEV}
endif

build:
	${DOCKER_COMPOSE_CMD} build
	#${DOCKER_COMPOSE_CMD} build --no-cache

up:
	${DOCKER_COMPOSE_CMD} up -d

down:
	${DOCKER_COMPOSE_CMD} down

cc:
	${DOCKER_COMPOSE_CMD} exec accesspanel bash -c "chown -R www-data:www-data /var/www/html/var"
	${DOCKER_COMPOSE_CMD} exec accesspanel bash -c "sudo -E -u www-data ./bin/console cache:clear"
	${DOCKER_COMPOSE_CMD} exec accesspanel bash -c "chmod -R g+w,o+w /var/www/html"
	${DOCKER_COMPOSE_CMD} exec accesspanel bash -c "rm -rf /var/www/html/var/log/*"

init-db:
	${DOCKER_COMPOSE_CMD} exec accesspanel bash -c "sudo -E -u www-data ./bin/console doctrine:database:drop --force"
	${DOCKER_COMPOSE_CMD} exec accesspanel bash -c "sudo -E -u www-data ./bin/console doctrine:database:create --no-interaction"
	${DOCKER_COMPOSE_CMD} exec accesspanel bash -c "sudo -E -u www-data ./bin/console doctrine:migrations:migrate --no-interaction"
	${DOCKER_COMPOSE_CMD} exec accesspanel bash -c "sudo -E -u www-data ./bin/console doctrine:fixtures:load --no-interaction"

npm-build:
	${DOCKER_COMPOSE_CMD} exec accesspanel bash -c "sudo -u www-data npm run build"

migrate:
	${DOCKER_COMPOSE_CMD} exec accesspanel bash -c "sudo -u www-data ./bin/console make:migration --no-interaction"

bash:
	${DOCKER_COMPOSE_CMD} exec accesspanel bash -c "sudo -u www-data bash"

stop-game:
	${DOCKER_COMPOSE_CMD} stop minecraft-r
	${DOCKER_COMPOSE_CMD} stop minecraft-p
	${DOCKER_COMPOSE_CMD} stop minecraft-k

start-game:
	${DOCKER_COMPOSE_CMD} start minecraft-r
	${DOCKER_COMPOSE_CMD} start minecraft-p
	${DOCKER_COMPOSE_CMD} start minecraft-k

install-plugins:
	${DOCKER_COMPOSE_CMD} cp ./plugins/* minecraft-k:/minecraft/plugins
	${DOCKER_COMPOSE_CMD} cp ./plugins/* minecraft-p:/minecraft/plugins
	${DOCKER_COMPOSE_CMD} cp ./plugins/* minecraft-r:/minecraft/plugins

traefik-restart:
	${DOCKER_COMPOSE_CMD} down traefik
	${DOCKER_COMPOSE_CMD} up -d

