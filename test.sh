#!/bin/bash
#A shell script to install all required packages set the environment for testing
#migrate db and then set back to localdev again when finished

echo "Setting env to testing"
cp .env.testing .env
echo "Creating local test db"
touch database/testing.sqlite
echo "Migrate and seed db"
php artisan migrate:refresh --seed
echo "Running Tests"
vendor/bin/phpunit --verbose
echo "Setting env back to localdev"
cp .env.localdev .env
