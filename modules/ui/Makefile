SHELL := /bin/bash

ifndef VERBOSE
.SILENT:
endif

SERVICE=manager_ui

install_and_start: prepare install start

-include .env
export $(shell if test -f ".env"; then (sed 's/=.*//' .env) fi;)

dev:
	make -s prepare
	cd app && npm install && npm run dev

prepare:
	cp ./.env ./app/.env

start:
	$(eval HOSTNAME=$(shell hostname -i))
	echo "Starting [${SERVICE}] ..."
	docker-compose up -d
	printf "Starting [${SERVICE}]"; printf ${DONE}
	echo "[${SERVICE}] ===> Running Nginx at http://${HOSTNAME}:${NGINX_EXT_PORT}"

install_dev: install

connect:
	docker-compose run --rm nginx bash

install:
	echo "Installing ${SERVICE} ..."
	echo "-----------------------------------------------------------"
	docker-compose build
	echo "-----------------------------------------------------------"

stop:
	echo "Stopping [${SERVICE}] ..."
	docker-compose down --remove-orphans --volumes
	printf "Stopping [${SERVICE}]"; printf ${DONE}

DONE := " ... \033[32mdone\033[0m\n" # done green text
MSG = "$$(tput setaf 153)----------------> %s$$(tput sgr0)\n" # light blue msg
MSG_RED := "\033[31m%s\033[0m" # red msg