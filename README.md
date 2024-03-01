# ApiPlatform + React

This docker environment sets up the following containers :
- apiPlatform_api
  - PHP v8.3.3 
  - Xdebug v3.3.0
  - ApiPlatform v3.2.13 built on Symfony v6.4.3
- apiPlatform_nginx
  - Nginx v1.25.4
  - Nginx's configuration for the ApiPlatform
- apiPlatform_mysql
  - MySQL v8.3.0
- apiPlatform_redis
  - Redis v7.2.4 (configured for ApiPlatform's cache; not configured for sessions yet)
- apiPlatform_web
  - Node v20.11.1
  - Npm v10.2.4
  - React.js v18.2.0
  - Redux toolkit v2.2.1

## Prerequisites

- Up-to-date Docker engine. 
- Docker compose version 2. You can check your version with `docker compose version`, docker compose version 1 is deprecated. 

## Installation

Run `docker compose up -d` to build and start the docker containers. <br>
Run `docker exec apiPlatform_api composer install` to install ApiPlatform dependencies. <br>
Run `docker exec apiPlatform_web npm install` to install React dependencies.

The backend app is now accessible on http://localhost:8080/ and the frontend app is accessible on http://localhost:3000/.

###### XDebug

In order to debug the backend with XDebug, there are a few configurations to be done in the IDE: <br>

Settings -> PHP : CLI Interpreter -> ... -> Add -> From Docker [...]
- select Docker in the top radio input
- Server: select the docker connection, add new connection if empty
- Image name: apiplatform-api:latest
- PHP interpreter path: php
<br>

Settings -> PHP -> Servers -> Add
- Name: localhost
- Host: localhost
- Port: 8080
- Debugger: Xdebug
- Use path mappings: Check
- add `/var/www/apiPlatform` as the Absolute path on the server corresponding to the File/Directory record of Project files -> path-to-project-on-local-machine -> api