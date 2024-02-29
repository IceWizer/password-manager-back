#!/usr/bin/env bash

if [ "$1" != "" ]; then
	exec "$@"

		echo "Hey, I'm here!";

	exit 0
fi

#php bin/console d:m:m --no-interaction
#php bin/console d:f:l --no-interaction

# symfony server:start --port=80 --allow-http

php-fpm