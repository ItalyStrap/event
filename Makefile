DOCKER_FOLDER = .docker
DOCKER_DIR = cd $(DOCKER_FOLDER) &&

default: help

# https://marmelab.com/blog/2016/02/29/auto-documented-makefile.html
# https://gist.github.com/prwhite/8168133?permalink_comment_id=4266839#gistcomment-4266839
.PHONY: help
help: ## Display this help screen
	@grep -hP '^\w.*?:.*##.*$$' $(MAKEFILE_LIST) | sort -u | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: files/permission
files/permission:	### Set the executable files in Docker folder permissions to 777
	@echo "Checking files permissions"
	@if [ -f "$(DOCKER_FOLDER)/codecept" ]; then chmod 777 $(DOCKER_FOLDER)/codecept; fi
	@if [ -f "$(DOCKER_FOLDER)/composer" ]; then chmod 777 $(DOCKER_FOLDER)/composer; fi
	@echo "Files permissions ok"

# Docker commands

.PHONY: build
build: files/permission	### Build the containers inside the ./docker folder
	@echo "Building the containers"
	$(DOCKER_DIR) docker-compose up -d --build --remove-orphans --force-recreate
	@echo "Containers built"

.PHONY: up
up: files/permission	### Start the containers inside the ./docker folder
	@echo "Starting the containers..."
	@$(DOCKER_DIR) docker-compose up -d --remove-orphans
	@echo "Containers started"

.PHONY: down
down:	### Stop the containers inside the ./docker folder
	@echo "Stopping the containers"
	@$(DOCKER_DIR) docker-compose down --remove-orphans --volumes
	@echo "Containers stopped"

# Composer commands

.PHONY: composer/install
composer/install: up	### Install the composer dependencies
	@echo "Installing the composer dependencies"
	@$(DOCKER_DIR) ./composer install

.PHONY: composer/update
composer/update: up	### Update the composer dependencies
	@echo "Updating the composer dependencies"
	@$(DOCKER_DIR) ./composer update

.PHONY: composer/dump
composer/dump: up	### Dump the composer autoload
	@echo "Dumping the composer autoload"
	@$(DOCKER_DIR) ./composer dump-autoload

# Codestyle commands

.PHONY: cs
cs: up	### Run the code sniffer
	@echo "Running the code sniffer"
	@$(DOCKER_DIR) ./composer cs

.PHONY: cs/fix
cs/fix: up	### Run the code sniffer and fix the errors
	@echo "Running the code sniffer and fix the errors"
	@$(DOCKER_DIR) ./composer cs:fix

# Psalm commands

.PHONY: psalm
psalm: up	### Run the psalm
	@echo "Running the psalm"
	@$(DOCKER_DIR) ./composer psalm

# Codeception commands

.PHONY: codecept/build
codecept/build:	up	### Build the codeception suites
	@echo "Building the codeception suites"
	@$(DOCKER_DIR) ./codecept build

.PHONY: clean
clean: up	### Clean the codeception suites
	@echo "Cleaning the codeception suites"
	@$(DOCKER_DIR) ./codecept clean

.PHONY: unit
unit: up	### Run the unit tests
	@echo "Running the unit tests"
	@$(DOCKER_DIR) ./codecept run unit

.PHONY: integration
integration: up	### Run the integration tests
	@echo "Running the integration tests"
	@$(DOCKER_DIR) ./codecept run integration

.PHONY: functional
functional: up	### Run the functional tests
	@echo "Running the functional tests"
	@$(DOCKER_DIR) ./codecept run functional --debug

.PHONY: acceptance
acceptance: up	### Run the acceptance tests
	@echo "Running the acceptance tests"
	@$(DOCKER_DIR) ./codecept run acceptance

.PHONY: qa
qa: cs psalm unit integration functional acceptance	### Run all the tests

# Infection commands

.PHONY: infection
infection: up	### Run the infection
	@echo "Running the infection"
	@$(DOCKER_DIR) ./composer infection

# Rector commands

.PHONY: rector
rector: up	### Run the rector
	@echo "Running the rector"
	@$(DOCKER_DIR) ./composer rector