<?php

/**
 * This file is for the viewallusers route, only accessible to admins, showing a table containing every user registered
 * to the application, with an option to return to the admin menu.
 */

use ABCoursework\SessionWrapperInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/viewallusers', function (Request $request, Response $response) use ($app) {

    if ($this->get(SessionWrapperInterface::class)->get('username') !== 'admin') {
        return $response->withHeader('Location', 'menu');
    }

    $model = $this->get('UserModel');
    $users = $model->getAllUsers();

    return $this->view->render($response, 'viewallusers.html.twig', [
        'css_path' => CSS_PATH,
        'landing_page' => LANDING_PAGE,
        'page_title' => APP_NAME,
        'additional_info' => 'Created by Jared, Charlie and Jordan',
        'page_heading_1' => 'All Users',
        'database_error' => $model->getErrors()['database'] ?? '',
        'users' => $users
    ]);

})->setName('viewallusers');
