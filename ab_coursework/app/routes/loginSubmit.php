<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/loginSubmit', function (Request $request, Response $response) use ($app) {

    $validator = $this->get('Validator');

    $userInput = $request->getParsedBody();
    $username = $userInput['SiteUsername'] ?? '' ;
    $password = $userInput['SitePassword'] ?? '' ;

    $usernameValidator = $validator->validateString('SiteUsername',$username);

    $passwordValidated = '';
    $passwordError = '';

    if (empty($password)) {
        $passwordError = 'Please enter a password';
    } elseif (strlen($password) > 100) {
        $passwordError = 'Password must be less than or equal to 100 characters';
    } else {
        $passwordValidated = $password;
    }


    if(!$validator->areAllValid()){
        $validatorError = $validator->getErrors();

        $usernameError = $validatorError['SiteUsername'] ?? '';
    }

    if($validator->areAllValid() && empty($confirmedPasswordError) && empty($passwordError)){
        $response = $response->withStatus(303);
        return $response->withHeader('Location', 'menu');
    }

    return $this->view->render($response,
        'loginform.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'page_title' => APP_NAME,
            'action' => '',
            'method' => 'post',
            'additional_info' => 'Created by Jared, Charlie and Jordan',
            'page_heading_1' => 'Team AB Coursework',
            'page_heading_2' => 'Login Form',
            'page_text' => 'Please Enter Your User Info',
            'password_error' => $passwordError,
            'username_error' => $usernameError,

        ]);

})->setName('loginSubmit');