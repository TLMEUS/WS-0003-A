<?php
/**
 * This file contains the Middleware/CheckPermissions.php file for project WS-0003
 *
 * File Information:
 * Project Name: WS-0003
 * Module Name: App
 * Section Name: Middleware
 * File Name: CheckPermissions.php
 * Author: Troy L. Marker
 * Language: PHP 8.2
 *
 * File Copyright: 05/2023
 */
declare(strict_types=1);

namespace App\Middleware;

use Framework\Request;
use Framework\Response;
use Framework\RequestHandlerInterface;
use Framework\MiddlewareInterface;

readonly class CheckPermissions implements MiddlewareInterface {

    public function __construct(private Response $response){
    }

    public function process(Request $request, RequestHandlerInterface $next): Response {
        if ($_ENV["colAccess"] != 2) {
            return $next->handle($request);
        }
        $this->response->redirect(url: "https://userman.tlme.us/denyaccess");
        return $this->response;
    }
}