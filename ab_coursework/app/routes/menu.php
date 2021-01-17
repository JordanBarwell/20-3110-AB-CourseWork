<?php

/**
 * This file is for the menu route, once a user logs in or registers, the menu gives them four options, or five if they're
 * the administrator, these options being to logout, to send a message to the circuit board, to download new messages from
 * the SOAP server and view messages stored in the database. Optionally, if it is the administrator user, they have an option
 * to access the admin menu.
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/menu', function (Request $request, Response $response) use ($app) {

    $admin = false;
    if ($this->get(\ABCoursework\SessionWrapperInterface::class)->get('username') === 'admin') {
        $admin = true;
    }

    return $this->view->render($response, 'menu.html.twig', [
        'css_path' => CSS_PATH,
        'landing_page' => LANDING_PAGE,
        'page_title' => APP_NAME,
        'additional_info' => 'Created by Jared, Charlie and Jordan',
        'page_heading_1' => 'Menu',
        'admin' => $admin,
    ]);

})->setName('menu');
