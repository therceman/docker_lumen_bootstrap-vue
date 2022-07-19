#!/usr/bin/env sh
set -eu

envsubst '${API_ROUTE} ${API_ORIGIN}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

exec "$@"