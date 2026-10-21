# Docker and WordPress Example 🐳

## Basic Setup
1. Install Docker https://www.docker.com/get-started
2. `./docker-compose up`

## URLs
- `http://localhost:8080` – WordPress
- `http://localhost:8080/admin/` – WordPress admin (`admin`/`password`)
- `http://localhost:8000` – PHPMyAdmin (`root`/`root`)

## docker-compose
Run `docker-compose` command in the folder with `docker-compose.yml` only
- `docker-compose up` # Start containers
- `docker-compose up -d` # Start containers in detached mode (in background)
- `docker-compose stop` # Stop containers
- `docker-compose down` # Stop and remove containers
- `docker-compose ps` # List containers ("process status")
- `docker-compose rm` # Remove stopped containers
- `docker-compose build` # Build containers
- `docker-compose exec [CONTAINER] /bin/bash` # Access bash in [CONTAINER] container

## docker
Run `docker` command from everywhere
- `docker ps` # List running containers ("process status")
- `docker exec -it [CONTAINER] /bin/bash` # Access bash in [CONTAINER] container
- `docker stop [CONTAINER]` # Stop running [CONTAINER]
- `docker stop $(docker ps -a -q)` # Stop all running containers
- `docker container ls --all` # List all containers Docker registers in our system
- `docker container rm [CONTAINER]` # Remove container [CONTAINER]
- `docker images` # List all images
- `docker rmi [IMAGE]` # Remove [IMAGE]
