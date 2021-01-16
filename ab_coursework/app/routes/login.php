<?php

use ABCoursework\SessionWrapperInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/login', function (Request $request, Response $response) use ($app) {

    $formName = 'loginform.html.twig';

    return $this->view->render($response,
        $formName,
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'page_title' => APP_NAME,
            'additional_info' => 'Created by Jared, Charlie and Jordan',
            'page_heading_1' => 'Team AB Coursework',
            'page_heading_2' => 'Login Form',
            'page_text' => 'Please Enter Your User Info',
            'action' => 'loginsubmit',
            'method' => 'post',
            'csrf_token' => $this->get(SessionWrapperInterface::class)->getCsrfToken($formName)
        ]);

})->setName('login');