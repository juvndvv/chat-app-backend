# Makefile

# Define the Sail command path
SAIL_CMD = ./vendor/bin/sail

# Default target to bring up the Sail containers in detached mode
.PHONY: start
up:
	$(SAIL_CMD) up -d

# Default target to stop the Sail containers
.PHONY: stop
down:
	$(SAIL_CMD) down

# Target to restart the Sail containers
.PHONY: restart
restart: down up

# Default target to run the tests
.PHONY: test
test:
	@echo "Running test..."
	$(SAIL_CMD) test

# Add any other targets you need
.PHONY: clean
clean:
	@echo "Cleaning up..."
	# Add your cleanup commands here, if any

# Example of a target for updating dependencies
.PHONY: update
update:
	sail composer install

# Example of a target for updating docs
.PHONY: update-doc
update-doc:
	./phpDocumentor.phar -d app -t docs/code
