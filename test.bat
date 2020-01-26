@ECHO OFF
vendor\bin\phpcbf --ignore=./tests/_support/* ./src/ ./tests/ && vendor\bin\phpcs --ignore=./tests/_support/* ./src/ ./tests/ && vendor\bin\phpstan analyze --level=4 && codecept run unit && codecept run wpunit
