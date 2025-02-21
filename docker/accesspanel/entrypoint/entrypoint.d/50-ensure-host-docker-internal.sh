#!/bin/bash

DOCKER_INTERNAL_HOST="host.docker.internal"
DOCKER_HOST="$(getent hosts $DOCKER_INTERNAL_HOST | cut -d' ' -f1)"
if [ ! $DOCKER_HOST ]; then
  DOCKER_INTERNAL_IP=$(ip -4 route show default | cut -d' ' -f3)
  echo "$DOCKER_INTERNAL_IP    $DOCKER_INTERNAL_HOST" | tee -a /etc/hosts > /dev/null
fi
