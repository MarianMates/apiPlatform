# Use the official Node.js base image
FROM node:20.11.1

# Set the working directory in the container
WORKDIR /app

# Copy package.json and package-lock.json (if available)
COPY ../../web/package*.json ./

# Install project dependencies
RUN npm install

# Command to run the app
CMD ["npm", "start"]