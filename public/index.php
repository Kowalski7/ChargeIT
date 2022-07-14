<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
//    return new Kernel('prod', false);   // uncomment to switch app to the production environment
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
