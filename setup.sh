#!/bin/bash

cp .env.example .env

composer install
npm install

php artisan key:generate

docker compose up -d