DOCKER_COMPOSE_CMD=docker compose --progress plain --env-file ./.env --env-file ./.env.local -f docker-compose.yaml
DOCKER_COMPOSE_EXEC_R_CMD=${DOCKER_COMPOSE_CMD} exec minecraft-r
DOCKER_COMPOSE_EXEC_P_CMD=${DOCKER_COMPOSE_CMD} exec minecraft-p

build:
	${DOCKER_COMPOSE_CMD} build

up:
	${DOCKER_COMPOSE_CMD} up -d

down:
	${DOCKER_COMPOSE_CMD} down
