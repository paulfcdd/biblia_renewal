install:
	@bash ./.make/install.sh
start:
	docker-compose up -d
stop:
	docker-compose down
restart:
	docker-compose restart
bash:
	docker-compose exec sf_app bash
clear-cache:
	docker-compose exec sf_app bin/console cache:clear
run-migrations:
	docker-compose exec sf_app bin/console doctrine:migrations:migrate
update-schema:
	docker-compose exec sf_app bin/console doctrine:schema:update --force
ps:
	docker-compose ps

