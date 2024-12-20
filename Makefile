.PHONY: validate install update deps phpcs phpcbf php82compatibility php83compatibility phpstan analyze tests testdox ci clean patchcoverage build

COMPOSER_BIN := composer
define header =
    @if [ -t 1 ]; then printf "\n\e[37m\e[100m  \e[104m $(1) \e[0m\n"; else printf "\n### $(1)\n"; fi
endef

#~ Composer dependency
validate:
	$(call header,Composer Validation)
	@${COMPOSER_BIN} validate

install:
	$(call header,Composer Install)
	@${COMPOSER_BIN} install

update:
	$(call header,Composer Update)
	@${COMPOSER_BIN} update
	@${COMPOSER_BIN} bump --dev-only

composer.lock: install

#~ Vendor binaries dependencies
vendor/bin/php-cs-fixer:
vendor/bin/phpstan:
vendor/bin/phpunit:
vendor/bin/phpcov:

#~ Report directories dependencies
build/reports/phpunit:
	@mkdir -p build/reports/phpunit

build/reports/phpcs:
	@mkdir -p build/reports/cs

build/reports/phpstan:
	@mkdir -p build/reports/phpstan

#~ main commands
deps: composer.json # jenkins + manual
	$(call header,Checking Dependencies)
	@XDEBUG_MODE=off ./vendor/bin/composer-dependency-analyser --config ./ci/composer-dependency-analyser.php # for shadow, unused required dependencies and ext-* missing dependencies

phpcs: vendor/bin/php-cs-fixer build/reports/phpcs # jenkins + manual
	$(call header,Checking Code Style)
	@./vendor/bin/php-cs-fixer check -v --diff

phpcbf: vendor/bin/php-cs-fixer # manual
	$(call header,Fixing Code Style)
	@./vendor/bin/php-cs-fixer fix -v

php-min-compatibility: vendor/bin/phpstan build/reports/phpstan
	$(call header,Checking PHP 8.3 compatibility)
	@XDEBUG_MODE=off ./vendor/bin/phpstan analyse --configuration=./ci/php-min-compatibility.neon --error-format=checkstyle > ./build/reports/phpstan/php-min-compatibility.xml

php-max-compatibility: vendor/bin/phpstan build/reports/phpstan
	$(call header,Checking PHP 8.4 compatibility)
	@XDEBUG_MODE=off ./vendor/bin/phpstan analyse --configuration=./ci/php-max-compatibility.neon --error-format=checkstyle > ./build/reports/phpstan/php-max-compatibility.xml

phpstan: vendor/bin/phpstan build/reports/phpstan
	$(call header,Running Static Analyze)
	@XDEBUG_MODE=off ./vendor/bin/phpstan analyse --error-format=checkstyle > ./build/reports/phpstan/phpstan.xml

analyze: vendor/bin/phpstan build/reports/phpstan
	$(call header,Running Static Analyze - Pretty tty format)
	@XDEBUG_MODE=off ./vendor/bin/phpstan analyse --error-format=table

tests: vendor/bin/phpunit build/reports/phpunit #ci
	$(call header,Running Unit Tests)
	@XDEBUG_MODE=coverage php -dzend_extension=xdebug.so ./vendor/bin/phpunit --testsuite=unit --coverage-clover=./build/reports/phpunit/clover.xml --log-junit=./build/reports/phpunit/unit.xml --coverage-php=./build/reports/phpunit/unit.cov --coverage-html=./build/reports/coverage/ --fail-on-warning

integration: vendor/bin/phpunit build/reports/phpunit #manual
	$(call header,Running Integration Tests)
	@XDEBUG_MODE=coverage php -dzend_extension=xdebug.so ./vendor/bin/phpunit --testsuite=integration --fail-on-warning

testdox: vendor/bin/phpunit #manual
	$(call header,Running Unit Tests (Pretty format))
	@XDEBUG_MODE=coverage php -dzend_extension=xdebug.so ./vendor/bin/phpunit --testsuite=unit --fail-on-warning --testdox

patchcoverage: vendor/bin/phpcov build/reports/phpunit/unit.cov
	$(call header,Computing PR coverage)
	@git diff origin/$(TARGET_BRANCH)...HEAD > /tmp/pr.patch
	@XDEBUG_MODE=off ./vendor/bin/phpcov patch-coverage --path-prefix $(PWD) build/reports/phpunit/unit.cov /tmp/pr.patch

clean:
	$(call header,Cleaning previous build)
	@if [ "$(shell ls -A ./build)" ]; then rm -rf ./build/*; fi; echo " done"

build:
	$(call header,Build Docker image)
	docker build --tag adventofcode:2023 --file Dockerfile .

run:
	$(call header,Run Docker image)
	docker run --name adventofcode -t adventofcode:2023
ci: clean validate deps install phpcs tests integration php-min-compatibility php-max-compatibility analyze
