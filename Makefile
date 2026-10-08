.PHONY: test lint fix ci

test:
	composer test

lint:
	composer lint

fix:
	composer lint:fix

ci: lint
