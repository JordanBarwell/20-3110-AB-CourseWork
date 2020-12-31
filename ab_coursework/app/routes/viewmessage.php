<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/viewmessages', function (Request $request, Response $response) use ($app) {

    $query = $this->get('SqlQueries');

    $viewMsg = $query->viewMessages(25);

    return $this->view->render($response,
        'viewmessages.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'page_title' => APP_NAME,
            'additional_info' => 'Created by Jared, Charlie and Jordan',
            'page_heading_1' => 'Messages',
            'messages' => $viewMsg
        ]);

})->setName('viewmessages');
