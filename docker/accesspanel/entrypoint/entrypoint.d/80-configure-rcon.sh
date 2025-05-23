#!/bin/bash

RCON_HOSTNAMES=(${CONFIGURE_MINECRAFT_HOSTS})
RCON_PORTS=(${CONFIGURE_MINECRAFT_RCON_PORTS})
RCON_PASSWORDS=(${CONFIGURE_MINECRAFT_RCON_PASSWORDS})

RCON_CONF_FILE=/etc/rcon.conf

echo '' > $RCON_CONF_FILE

for ARR_KEY in "${!RCON_HOSTNAMES[@]}"; do
    echo "[${RCON_HOSTNAMES[$ARR_KEY]}]" >> $RCON_CONF_FILE
    echo "host = ${RCON_HOSTNAMES[$ARR_KEY]}" >> $RCON_CONF_FILE
    echo "port = ${RCON_PORTS[$ARR_KEY]}" >> $RCON_CONF_FILE
    echo "passwd = ${RCON_PASSWORDS[$ARR_KEY]}" >> $RCON_CONF_FILE
    echo "" >> $RCON_CONF_FILE
done

echo "" >> $RCON_CONF_FILE