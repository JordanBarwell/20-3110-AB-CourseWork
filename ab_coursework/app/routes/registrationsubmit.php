<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/registrationsubmit', function (Request $request, Response $response) use ($app) {

    $validator = $this->get('Validator');

    $userInput = $request->getParsedBody();
    $userEmail = $userInput['UserEmail'] ?? '';
    $username = $userInput['SiteUsername'] ?? '';
    $password = $userInput['SitePassword'] ?? '';
    $confirmedPassword = $userInput['ConfirmPassword'] ?? '';
    $phoneNumber = $userInput['PhoneNumber'] ?? '';

    $passwordValidated = '';
    $passwordError = '';

    if (empty($password)) {
        $passwordError = 'please enter a password';
    } elseif (strlen($password) > 100) {
        $passwordError = 'password must be less than or equal to 100 characters';
    } else {
        $passwordValidated = $password;
    }

    $confirmedPasswordValidated = '';
    $confirmPasswordError = '';

    if (empty($confirmedPassword)) {
        $confirmPasswordError = 'password must be confirmed';
    } elseif ($confirmedPassword !== $password) {
        $confirmPasswordError = 'password does not match';
    } else{
        $confirmedPasswordValidated = $confirmedPassword;
    }

    $validateEmail = $validator->validateEmail('UserEmail', $userEmail);
    $validatedUsername = $validator->validateString('SiteUsername', $username);
    $validatedPassword = $passwordValidated;
    $validateConfirmPassword = $confirmedPasswordValidated;
    $validatePhoneNumber = $validator->validatePhoneNumber('PhoneNumber', $phoneNumber);

    $userEmailError = '';
    $usernameError = '';
    $phoneNumberError = '';

    if(!$validator->areAllValid()){
        $validatorError = $validator->getErrors();
        $userEmailError = $validatorError['UserEmail'] ?? '';
        $usernameError = $validatorError['SiteUsername'] ?? '';
        $phoneNumberError = $validatorError['PhoneNumber'] ?? '';
    }

    if($validator->areAllValid() && empty($confirmedPasswordError) && empty($passwordError)){
        $response = $response->withStatus(303);
       return $response->withHeader('Location', 'menu');
    }

    return $this->view->render($response,
        'registrationform.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'page_title' => APP_NAME,
            'action' => '',
            'method' => 'post',
            'additional_info' => 'Created by Jared, Charlie and Jordan',
            'page_heading_1' => 'Team AB Coursework',
            'page_heading_2' => 'Registration Form',
            'page_text' => 'Please Create Your New User Info',
            'password_error' => $passwordError,
            'confirmPassword_error' => $confirmPasswordError,
            'userEmail_error' => $userEmailError,
            'username_error' => $usernameError,
            'phoneNumber_error' => $phoneNumberError
        ]);

})->setName('registered');
