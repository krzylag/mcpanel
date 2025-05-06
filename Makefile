#!make
include .env
include .env.local
export $(shell sed 's/=.*//' .env)
export $(shell sed 's/=.*//' .env.local)

DOCKER_COMPOSE_CMD_PROD=docker compose --progress plain --env-file ./.env --env-file ./.env.local -f docker-compose.yaml
DOCKER_COMPOSE_CMD_DEV=docker compose --progress plain --env-file ./.env --env-file ./.env.local -f docker-compose.dev.yaml

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
	${DOCKER_COMPOSE_CMD} exec accesspanel bash -c "./bin/console cache:clear"
	${DOCKER_COMPOSE_CMD} exec accesspanel bash -c "chmod -R g+w,o+w /var/www/html"

init-db:
	${DOCKER_COMPOSE_CMD} exec accesspanel bash -c "sudo -u www-data ./bin/console doctrine:database:drop --force"
	${DOCKER_COMPOSE_CMD} exec accesspanel bash -c "sudo -u www-data ./bin/console doctrine:database:create --no-interaction"
	${DOCKER_COMPOSE_CMD} exec accesspanel bash -c "sudo -u www-data ./bin/console doctrine:migrations:migrate --no-interaction"
	${DOCKER_COMPOSE_CMD} exec accesspanel bash -c "sudo -u www-data ./bin/console doctrine:fixtures:load --no-interaction"

npm-build:
	${DOCKER_COMPOSE_CMD} exec accesspanel bash -c "sudo -u www-data npm run build"

migration:
	${DOCKER_COMPOSE_CMD} exec accesspanel bash -c "sudo -u www-data ./bin/console make:migration --no-interaction"

bash:
	${DOCKER_COMPOSE_CMD} exec accesspanel bash -c "sudo -u www-data bash"

stop-game:
	${DOCKER_COMPOSE_CMD} stop minecraft-r
	${DOCKER_COMPOSE_CMD} stop minecraft-p

start-game:
	${DOCKER_COMPOSE_CMD} start minecraft-r
	${DOCKER_COMPOSE_CMD} start minecraft-p
