FROM node:18.3.0-alpine3.15 as build

# Needed for vite build
ENV NODE_OPTIONS='--max_old_space_size=1024'

# Fix errors about outdated npm version
RUN npm install --location=global npm@latest

# make the 'app' folder the current working directory
WORKDIR /app

# copy both 'package.json' and 'package-lock.json' (if available)
COPY app/package*.json ./

# install project dependencies
RUN npm install

# copy project files and folders to the current working directory (i.e. 'app' folder)
COPY app .

# fix error about outdated browserlist db
RUN npm install browserslist@latest
RUN npx --silent browserslist@latest --update-db

# build app for production with minification
RUN npm run build

######################################################################################

FROM nginx:1.21.6-alpine

RUN apk add bash

# Build Output Dir
ARG APP_BUILD_FOLDER=dist

# API Setup
ARG API_ROUTE=/api
ARG API_ORIGIN=lumen

COPY etc/nginx/nginx-default.conf.template /etc/nginx/conf.d/default.conf.template
COPY --from=build /app/${APP_BUILD_FOLDER} /usr/share/nginx/html
COPY scripts/docker-entrypoint.sh /

RUN ["chmod", "+x", "/docker-entrypoint.sh"]
ENTRYPOINT ["/docker-entrypoint.sh"]
CMD ["nginx", "-g", "daemon off;"]