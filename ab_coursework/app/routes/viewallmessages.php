<?php

/**
 * This file is for the viewallmessages route, only accessible to admins, showing a table containing every message stored
 * in the database, with an option to return to the admin menu.
 */

use ABCoursework\SessionWrapperInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/viewallmessages', function (Request $request, Response $response) use ($app) {

    if ($this->get(SessionWrapperInterface::class)->get('username') !== 'admin') {
        return $response->withHeader('Location', 'menu');
    }

    $model = $this->get('MessageModel');
    $messages = $model->getAllMessages();

    return $this->view->render($response, 'viewallmessages.html.twig', [
        'css_path' => CSS_PATH,
        'landing_page' => LANDING_PAGE,
        'page_title' => APP_NAME,
        'additional_info' => 'Created by Jared, Charlie and Jordan',
        'page_heading_1' => 'All Messages',
        'database_error' => $model->getErrors()['database'] ?? '',
        'messages' => $messages
    ]);

})->setName('viewallmessages');
