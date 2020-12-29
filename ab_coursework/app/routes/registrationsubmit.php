<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/registrationsubmit', function (Request $request, Response $response) use ($app) {

    $validator = $this->get('Validator');

    $taintedParameters = $request->getParsedBody();
    $cleanedParameters = cleanupParameters($app, $taintedParameters);

    $userEmailError = '';
    $usernameError = '';
    $phoneNumberError = '';
    $passwordError = '';
    $confirmPasswordError = '';
    $existingUsernameError = '';
    $existingEmailError = '';
    $existingPhoneError = '';

    $passwordValidated = '';

    if (empty($cleanedParameters['cleanedSitePassword'])) {
        $passwordError = 'This field is required!';
    } elseif (strlen($cleanedParameters['cleanedSitePassword']) > 100) {
        $passwordError = 'password must be less than or equal to 100 characters';
    } else {
        $passwordValidated = $cleanedParameters['cleanedSitePassword'];
    }

    $confirmedPasswordValidated = '';

    if (empty($cleanedParameters['cleanedConfirmPassword'])) {
        $confirmPasswordError = 'This field is required!';
    } elseif ($cleanedParameters['cleanedConfirmPassword'] !== $cleanedParameters['cleanedSitePassword']) {
        $confirmPasswordError = 'password does not match';
    } else {
        $confirmedPasswordValidated = $cleanedParameters['cleanedConfirmPassword'];
    }

    $validatedPassword = $passwordValidated;
    $validatedConfirmPassword = $confirmedPasswordValidated;

    if ($cleanedParameters['cleanedConfirmPassword'] === $cleanedParameters['cleanedSitePassword']) {
        $hashedPassword = hashPassword($app, $cleanedParameters['cleanedSitePassword']);
    }

    if (!$validator->areAllValid()) {
        $validatorError = $validator->getErrors();
        $userEmailError = $validatorError['UserEmail'] ?? '';
        $usernameError = $validatorError['SiteUsername'] ?? '';
        $phoneNumberError = $validatorError['PhoneNumber'] ?? '';
    }

    if ($validator->areAllValid() && empty($confirmPasswordError) && empty($passwordError)) {

        $cleanedParameters['cleanedPhoneNumber'] = (int)substr($cleanedParameters['cleanedPhoneNumber'], 1);

        $data = $this->get('SqlQueries');

        $dataExist = $data->checkUserDetailsExist($cleanedParameters);
        if($dataExist['username'] === $cleanedParameters['cleanedSiteUsername']){
            $existingUsernameError = 'Username Already Exists';
        }
        if($dataExist['email'] === $cleanedParameters['cleanedUserEmail']){
            $existingEmailError = 'Email Already In Use';
        }
        if((int)$dataExist['phone'] === $cleanedParameters['cleanedPhoneNumber']){
            $existingPhoneError = 'Phone Number Already In Use';
        }

        if(empty($existingUsernameError) && empty($existingEmailError) && empty($existingPhoneError)){
            $dataStored = $data->storeUserData($cleanedParameters, $hashedPassword);
            $response = $response->withStatus(303);
            return $response->withHeader('Location', 'menu');
        }
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
            'phoneNumber_error' => $phoneNumberError,
            'existingEmailError' => $existingEmailError,
            'existingUsernameError' => $existingUsernameError,
            'existingPhoneError' => $existingPhoneError
        ]);

})->setName('registered');

function cleanupParameters($app, $taintedParameters)
{
    $cleanedParameters = [];
    $validator = $app->getContainer()->get('Validator');

    $taintedEmail = $taintedParameters['UserEmail'];
    $taintedUsername = $taintedParameters['SiteUsername'];
    $taintedPassword = $taintedParameters['SitePassword'];
    $taintedConfirmedPassword = $taintedParameters['ConfirmPassword'];
    $taintedPhoneNumber = $taintedParameters['PhoneNumber'];

    $cleanedParameters['cleanedUserEmail'] = $validator->validateEmail('UserEmail',$taintedEmail);
    $cleanedParameters['cleanedSiteUsername'] = $validator->ValidateString('SiteUsername', $taintedUsername);
    $cleanedParameters['cleanedSitePassword'] = $taintedParameters['SitePassword'];
    $cleanedParameters['cleanedConfirmPassword'] = $taintedParameters['ConfirmPassword'];
    $cleanedParameters['cleanedPhoneNumber'] = $validator->validatePhoneNumber('PhoneNumber', $taintedPhoneNumber);

    return $cleanedParameters;
}

function hashPassword($app, $passwordForHashing): String
{
    $bcryptWrapper = $app->getContainer()->get('BcryptWrapper');
    $hashedPassword = $bcryptWrapper->hash($passwordForHashing);
    return $hashedPassword;
}



