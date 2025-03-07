#!/bin/bash

if [ ! -f ./.env ]
then
cp initial.env .env
fi

if [ ! -f ./www/.htaccess ]
then
cp initial.htaccess ./www/.htaccess
fi

if [ ! -f ./package-lock.json ]
then
npm install
fi

docker compose up -d

sudo apt-get -y update && sudo apt-get -y --no-install-recommends install default-mysql-client

clear

echo "Finally! We're good to go. Have at it. 🔰"