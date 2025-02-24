#!/bin/bash

HOST_NAMES=("accesspanel" "database" "minecraft-r" "minecraft-p")

TEMP_HOST_FILE=/tmp/hostfile_$(date +%y-%m-%d_%H-%M-%S)
cat /etc/hosts > "$TEMP_HOST_FILE"
cat "$TEMP_HOST_FILE"

for HOST_NAME in "${HOST_NAMES[@]}"; do
    HOST_ENTRY=$(getent hosts "$HOST_NAME")
    sed -i'' "/$HOST_NAME/d" "$TEMP_HOST_FILE"
    echo "$HOST_ENTRY" >> "$TEMP_HOST_FILE"
done

cat "$TEMP_HOST_FILE" > /etc/hosts
rm -rf TEMP_HOST_FILE
