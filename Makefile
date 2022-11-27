install:
	composer install

lint:
	composer exec --verbose phpcs -- --standard=PSR12 routes/web.php
	composer exec --verbose phpstan -- --level=8 analyse routes/web.php

lint-fix:
	composer exec --verbose phpcbf -- --standard=PSR12 routes
