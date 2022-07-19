## Name
API Module

## Description
...

## Requirements

1. Docker (https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-on-ubuntu-20-04)
2. Docker-Composer (https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-compose-on-ubuntu-20-04)

## Start Development

```shell
make dev
```

## Swagger URL
See documentation here:
http://localhost:8081/api/documentation

## Run Tests

```shell
make test
```

## Connect To Service Container (via bash)

```shell
make connect
```

## Lumen Helpers

1. Create Migration (for UserModel as example)
```shell
make model_migration model=UserModel
```