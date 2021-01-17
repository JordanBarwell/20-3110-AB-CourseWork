<?php

/**
 * This file is for the homepage route, which is the starting route for this application, it offers two paths, one to log in
 * and one to register, this is also the page any logged out user will be redirected to if they try to use any other route.
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/', function (Request $request, Response $response) use ($app) {

    return $this->view->render($response, 'homepage.html.twig', [
        'css_path' => CSS_PATH,
        'landing_page' => LANDING_PAGE,
        'page_title' => APP_NAME,
        'additional_info' => 'Created by Jared, Charlie and Jordan',
        'page_heading_1' => 'Home',
        'page_heading_2' => 'Welcome User!',
    ]);

})->setName('homepage');

