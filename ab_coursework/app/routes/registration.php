<?php

use ABCoursework\SessionWrapperInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/registration', function (Request $request, Response $response) use ($app) {

    $formName = 'registrationform.html.twig';

    return $this->view->render($response,
        $formName,
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'page_title' => APP_NAME,
            'action' => 'registrationsubmit',
            'method' => 'post',
            'additional_info' => 'Created by Jared, Charlie and Jordan',
            'page_heading_1' => 'Team AB Coursework',
            'page_heading_2' => 'Registration Form',
            'page_text' => 'Please Create Your New User Info',
            'csrf_token' => $this->get(SessionWrapperInterface::class)->getCsrfToken($formName)
        ]);

})->setName('registration');