#!/bin/bash

# Define the path to your docker-compose.yml
COMPOSE_FILE="./docker-compose.yml"
TEMP_FILE="$COMPOSE_FILE.temp"

# Function to check if web container port is open
wait_for_port() {
    local name="$1"
    local port="$2"
    local attempt=0
    local max_attempts=30
    local wait_time=10

    echo "Waiting for $name to be ready..."
    while ! nc -z localhost $port && [ $attempt -lt $max_attempts ]; do
        attempt=$((attempt+1))
        echo "Attempt $attempt of $max_attempts"
        sleep $wait_time
    done

    if [ $attempt -lt $max_attempts ]; then
        echo "$name is ready!"
    else
        echo "Failed to connect to $name on port $port. Please check your container logs."
        exit 1
    fi
}

# Function to toggle comments for the web service commands
toggle_commands() {
    awk '
    /web:/ {
        in_web_section = 1
    }
    in_web_section && /command: npm start/ {
        if ($1 == "#") {
            sub(/^#/, "", $0)
        } else {
            $0 = "#    " $0
        }
    }
    in_web_section && /command: tail -f \/dev\/null/ {
        if ($1 == "#") {
            sub(/^#/, "", $0)
        } else {
            $0 = "#    " $0
        }
    }
    /ports:/ && in_web_section {
        in_web_section = 0
    }
    { print }
    ' "$COMPOSE_FILE" > "$TEMP_FILE" && mv "$TEMP_FILE" "$COMPOSE_FILE"
}

# Backup the original docker-compose file
cp "$COMPOSE_FILE" "$COMPOSE_FILE.bak"

# Toggle comments to disable "npm start" and enable "tail -f /dev/null"
toggle_commands

# Run docker-compose up -d
docker-compose up -d

# Wait for the web container to be ready
wait_for_port "apiPlatform_web" 3000

mv "$COMPOSE_FILE.bak" "$COMPOSE_FILE"

# After the web service is up, run npm install and start
docker exec apiPlatform_web npm install

docker exec apiPlatform_api composer install

docker compose up -d

echo -e "Environment ready, access http://localhost:8080/ for BE and http://localhost:3000/ for FE"
