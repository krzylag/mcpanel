FROM 05jchambers/legendary-minecraft-purpur-geyser:latest AS build-base

RUN apt-get update && apt-get install -y yq

RUN mkdir /entrypoint.d
COPY ./docker/minecraft/entrypoint/entrypoint.d/* /entrypoint.d/
COPY ./docker/minecraft/entrypoint/entrypoint.sh /
RUN chmod -R +x /entrypoint.d/*
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/scripts/start.sh"]

FROM build-base AS build-dev


FROM build-base AS build-prod