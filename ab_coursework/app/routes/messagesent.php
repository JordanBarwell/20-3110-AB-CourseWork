<?php

/**
 * This file is for the messagesent route, once a message has been successfully sent from the sendmessage route, this page
 * will prompt the user that the message has been sent with an option to return to the menu.
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/messagesent', function (Request $request, Response $response) use ($app) {

    return $this->view->render($response, 'messagesent.html.twig', [
        'css_path' => CSS_PATH,
        'landing_page' => LANDING_PAGE,
        'page_title' => APP_NAME,
        'additional_info' => 'Created by Jared, Charlie and Jordan',
        'page_heading_1' => 'Message Sent!'
    ]);

})->setName('messagesent');
