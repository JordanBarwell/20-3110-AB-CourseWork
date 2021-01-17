<?php

/**
 * This file is for the sendmessage route, this route displays a form where the user can choose and submit settings to the
 * circuit board so the board can update its settings.
 */

use ABCoursework\SessionWrapperInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/sendmessage', function (Request $request, Response $response) use ($app) {

    $formName = 'sendmessage.html.twig';

    return $this->view->render($response, $formName, [
        'css_path' => CSS_PATH,
        'landing_page' => LANDING_PAGE,
        'page_title' => APP_NAME,
        'additional_info' => 'Created by Jared, Charlie and Jordan',
        'page_heading_1' => 'Send A Message',
        'action' => 'sendmessagesubmit',
        'method' => 'post',
        'csrf_token' => $this->get(SessionWrapperInterface::class)->getCsrfToken($formName)
    ]);

})->setName('sendmessage');
