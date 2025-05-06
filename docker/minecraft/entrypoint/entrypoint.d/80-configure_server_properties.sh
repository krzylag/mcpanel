#!/bin/bash

SERVER_PROPERTIES_REPLACEMENTS=(
"s/enable-rcon=false/enable-rcon=true/"
"s/rcon.port=.*/rcon.port=${CONFIGURE_RCON_PORT}/"
"s/rcon.password=.*/rcon.password=${CONFIGURE_RCON_PASSWORD}/"
);

FILE_LOCATION=/minecraft/server.properties

for STR in ${SERVER_PROPERTIES_REPLACEMENTS[@]}; do
  sed -i $STR $FILE_LOCATION
done
