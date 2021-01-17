<?php

/**
 * This file is for the view messages route, which retrieves the 30 most recent messages from the database to display them
 * to the user in a table.
 */

use ABCoursework\SessionWrapperInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/viewmessages', function (Request $request, Response $response) use ($app) {

    $model = $this->get('MessageModel');
    $messages = $model->getMessages($this->get(SessionWrapperInterface::class)->get('username'), 30);

    return $this->view->render($response, 'viewmessages.html.twig', [
        'css_path' => CSS_PATH,
        'landing_page' => LANDING_PAGE,
        'page_title' => APP_NAME,
        'additional_info' => 'Created by Jared, Charlie and Jordan',
        'page_heading_1' => 'Messages',
        'database_error' => $model->getErrors()['database'] ?? '',
        'messages' => $messages
    ]);

})->setName('viewmessages');
