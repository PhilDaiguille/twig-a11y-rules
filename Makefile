PHPCS:=vendor/bin/php-cs-fixer
PHPUNIT:=vendor/bin/phpunit
REACTOR:=vendor/bin/rector

.PHONY: test lint fix ci

test:
	@echo "Running tests"
	composer test

lint:
	@echo "Running linters (php-cs-fixer, phpstan, rector dry-run)"
	composer lint

fix:
	@echo "Applying automatic fixes (rector + php-cs-fixer)"
	composer lint:fix

ci:
	@echo "Run full CI (lint + test)"
	composer lint && composer test
