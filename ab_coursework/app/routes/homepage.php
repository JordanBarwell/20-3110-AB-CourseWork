<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/', function (Request $request, Response $response) use ($app) {

    return $this->view->render($response,
        'homepage.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'page_title' => APP_NAME,
            'page_heading_1' => 'Team AB Coursework',
            'page_heading_2' => 'Home',
        ]);

})->setName('homepage');

