.PHONY: phpstan tests prepare database fixtures

phpstan:
	php vendor/bin/phpstan analyse -c phpstan.neon src --no-progress

tests:
	php bin/phpunit --testdox

fixtures:
	php bin/console doctrine:fixtures:load -n --env=$(env)

database:
	php bin/console doctrine:database:drop --if-exists --force --env=$(env)
	php bin/console doctrine:database:create --env=$(env)
	php bin/console doctrine:schema:update --force --env=$(env)

prepare:
	make database env=$(env)
	make fixtures env=$(env)
