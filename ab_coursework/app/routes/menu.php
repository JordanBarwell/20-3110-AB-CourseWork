<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/menu', function (Request $request, Response $response) use ($app) {

    return $this->view->render($response,
        'menu.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'page_title' => APP_NAME,
            'additional_info' => 'Created by Jared, Charlie and Jordan',
            'page_heading_1' => 'Menu',
            'page_heading_2' => 'Welcome User!',
        ]);

})->setName('menu');
