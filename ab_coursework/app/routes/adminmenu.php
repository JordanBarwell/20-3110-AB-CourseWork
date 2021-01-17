<?php

/**
 * This file is for the adminmenu route, only accessible to admins, this menu gives the administrator the option to view
 * lists of all users and all stored messages, with an option to logout or to return to the main menu.
 */

use ABCoursework\SessionWrapperInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/adminmenu', function (Request $request, Response $response) use ($app) {

    if ($this->get(SessionWrapperInterface::class)->get('username') !== 'admin') {
        return $response->withHeader('Location', 'menu');
    }

    return $this->view->render($response, 'adminmenu.html.twig', [
        'css_path' => CSS_PATH,
        'landing_page' => LANDING_PAGE,
        'page_title' => APP_NAME,
        'additional_info' => 'Created by Jared, Charlie and Jordan',
        'page_heading_1' => 'Admin Menu',
    ]);

})->setName('adminmenu');
