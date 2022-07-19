SHELL := /bin/bash

ifndef VERBOSE
.SILENT:
endif

MODULE=Manager

-include .env
export $(shell if test -f ".env"; then (sed 's/=.*//' .env) fi;)

dev: prepare install start
install: build env_switch
start: run_mysql init_lumen_db_connection run_lumen

install_dev: install

prepare:
	# nothing here

build:
	echo "[${MODULE}] Building ...";
	docker-compose build
	if [ "$${ENV,,}" = "dev" ] ; then \
		docker-compose run --rm lumen composer update; \
	fi;

init_lumen_db_connection:
	docker-compose run --rm lumen php artisan db:init

run_mysql:
	echo "[${MODULE}] Starting MySQL Database ...";
	docker-compose up -d mysql

stop_mysql:
	echo "[${MODULE}] Stopping MySQL Database ...";
	docker-compose stop mysql

run_lumen:
	$(eval HOSTNAME=$(shell hostname -i))
	echo "[${MODULE}] Starting Lumen ...";
	docker-compose up -d lumen
	echo "[${MODULE}] ===> Running Lumen at http://${HOSTNAME}:${LUMEN_PORT}"

migrate_db:
	echo "[${MODULE}] Migrating DB ...";
	docker-compose run --rm lumen php artisan migrate:refresh

seed_db:
	echo "[${MODULE}] Seeding DB ...";
	docker-compose run --rm lumen php artisan db:seed

remove_stopped_containers:
	docker-compose rm --force

reset_db: stop_mysql remove_stopped_containers run_mysql init_lumen_db_connection seed_db

docs:
	docker-compose run --rm lumen php artisan swagger-lume:publish
	docker-compose run --rm lumen php artisan swagger-lume:generate

test:
	docker-compose run --rm lumen ./vendor/bin/phpunit

connect:
	docker-compose run --rm lumen bash

env_switch:
	if [ "$${ENV,,}" = "prod" ] ; then \
		make -s env_prod; \
	elif [ "$${ENV,,}" = "qa" ] ; then \
		make -s env_qa; \
	elif [ "$${ENV,,}" = "test" ] ; then \
		make -s env_test; \
	else \
		make -s env_dev; \
	fi;

env_test:
	make -s create_env_file TRG_ENV=test MSG_COLOR=33 # green

env_qa:
	make -s create_env_file TRG_ENV=qa MSG_COLOR=34 # blue

env_dev:
	make -s create_env_file TRG_ENV=dev MSG_COLOR=29 # white

env_prod:
	make -s create_env_file TRG_ENV=prod MSG_COLOR=31 # red

create_env_file: # TRG_ENV=DEV MSG_COLOR=34 (example args)
	printf "[${MODULE}] Changing environment to " ; printf "\033[${MSG_COLOR}m%s\033[0m\n" [${TRG_ENV}]
	# docker-compose run --rm lumen php artisan env:config ${TRG_ENV}

get_repo_config: # name=game-core var=repo
	echo $$(grep -A3 "${name}:" repo-config.yml | grep "${var}:" | awk -F ": " '{print $$2}')

clone_repo: # name=game-core folder=./
	$(eval repo=$(shell make -s get_repo_config name=${name} var=repo))
	$(eval branch=$(shell make -s get_repo_config name=${name} var=branch))
	$(eval commit=$(shell make -s get_repo_config name=${name} var=commit))
	printf ${MSG} "Cloning [${name}]"
	echo "repo: ${repo}"
	echo "branch: ${branch}"
	echo "commit: ${commit}"
	echo "------------------"
	rm -rf ${folder}${name}
	git clone --branch ${branch} ${repo} ${folder}${name}
	if [ "$$(echo '${commit}' | grep latest)" = "" ]; then \
		echo "==> Checking Out Specific Commit Hash Version"; \
		cd ${folder}${name} && git reset --hard $(commit); \
    fi

clone__interface:
	make -s clone_repo name=game-interface folder=./
	cd game-interface && composer update

##### helpers

model_migration: #model=ClientModel
	$(eval CURRENT_OWNER=$(shell stat -c "%U" Makefile))
	docker-compose run --rm lumen php artisan migration:create ${model}
	sudo chown -R ${CURRENT_OWNER}: ./database;