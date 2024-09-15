<?php
/**
 * This file contains the config/middleware.php for project WS-0003.
 *
 * File information:
 * Project Name: WS-0003
 * Module Name: config
 * File Name: middleware.php
 * File Author: Troy L Marker
 * Language: PHP 8.3
 *
 * File Copyright: 05/2024
 */
declare(strict_types=1);

return [
    "access" => \App\Middleware\CheckPermissions::class
];