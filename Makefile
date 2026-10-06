.PHONY: test quality

test quality:
	docker compose exec mysql prepare-test-database
	docker compose exec php composer $@
