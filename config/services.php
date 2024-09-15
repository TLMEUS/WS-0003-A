<?php /** @noinspection ALL */
/** @noinspection PhpUndefinedNamespaceInspection */
/**
 * This file contains the config/services.php for project WS-0003.
 *
 * File information:
 * Project Name: WS-0003
 * Module Name: config
 * File Name: services.php
 * File Author: Troy L Marker
 * Language: PHP 8.3
 *
 * File Copyright: 05/2024
 */

$container = new Framework\Container;

$container->set(App\Database::class, function() {
    return new App\Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
});

$container->set(Framework\TemplateViewerInterface::class, function() {
    return new Framework\MVCTemplateViewer;
});

return $container;