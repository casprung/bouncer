#!/bin/bash


####################################################################
###  Clients Configuration                                       ###
####################################################################
if [ -z "$RADIUS_CLIENTS" ]; then
  export RADIUS_CLIENTS=""
else
  while IFS=',' read -ra ADDR; do
      for i in "${ADDR[@]}"; do
          IFS='@' read SECRET IP <<<$i
          OUT+=$'client '$IP$' {\n  secret      = '${SECRET}$'\n  require_message_authenticator = no\n}\n\n'
      done
  done  <<< "$RADIUS_CLIENTS"
  export RADIUS_CLIENTS="$OUT"
fi
envsubst '${RADIUS_CLIENTS}
         ' < clients.conf.template > /etc/raddb/clients.conf


####################################################################
###    SQL Configuration                                         ###
####################################################################
if [ -z "$RADIUS_DB_HOST" ]; then
  export RADIUS_DB_HOST=localhost
fi
if [ -z "$RADIUS_DB_PORT" ]; then
  export RADIUS_DB_PORT=3306
fi
if [ -z "$RADIUS_DB_USER" ]; then
  export RADIUS_DB_USER=radius
fi
if [ -z "$RADIUS_DB_PASS" ]; then
  export RADIUS_DB_PASS=radpass
fi
if [ -z "$RADIUS_DB_NAME" ]; then
  export RADIUS_DB_NAME=radius
fi

envsubst '
         ${RADIUS_DB_HOST}
         ${RADIUS_DB_PORT}
         ${RADIUS_DB_USER}
         ${RADIUS_DB_PASS}
         ${RADIUS_DB_NAME}
         ' < sql.template > /etc/raddb/mods-available/sql

ln -s /etc/raddb/mods-available/sql /etc/raddb/mods-enabled/sql

exec "$@"
