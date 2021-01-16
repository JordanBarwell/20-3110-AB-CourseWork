<?php

use ABCoursework\SessionManagerInterface;
use ABCoursework\SessionWrapperInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/registrationsubmit', function (Request $request, Response $response) use ($app) {

    $validator = $this->get('Validator');
    $taintedParameters = $request->getParsedBody();
    $cleanedParameters = cleanupRegistrationParameters($validator, $taintedParameters);
    $errors = getRegistrationValidationErrors($validator, $cleanedParameters);

    if (empty($errors)) {
        $cleanedParameters['cleanedSitePassword'] = hashPassword($app, $cleanedParameters['cleanedSitePassword']);
        $cleanedParameters['cleanedPhoneNumber'] = (int)substr($cleanedParameters['cleanedPhoneNumber'], 1);

        $model = $this->get('UserModel');

        if ($model->checkUserExists($cleanedParameters)) {
            $errors = array_merge($errors, $model->getErrors());
        }

        if(empty($errors)) {
            $sessionWrapper = $this->get(SessionWrapperInterface::class);
            $sessionManager = $this->get(SessionManagerInterface::class);
            if ($model->registerUser($cleanedParameters, $sessionWrapper, $sessionManager)) {
                $response = $response->withStatus(303);
                return $response->withHeader('Location', 'menu');
            } else {
                $errors = array_merge($errors, $model->getErrors());
            }
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
            'database_error' => $errors['Database'] ?? '',
            'userEmail_error' => $errors['UserEmail'] ?? '',
            'username_error' => $errors['SiteUsername'] ?? '',
            'password_error' => $errors['SitePassword'] ?? '',
            'confirmPassword_error' => $errors['ConfirmPassword'] ?? '',
            'phoneNumber_error' => $errors['PhoneNumber'] ?? ''
        ]);

})->setName('registrationsubmit');

function cleanupRegistrationParameters($validator, $taintedParameters): array
{
    $cleanedParameters = [];

    $taintedEmail = $taintedParameters['UserEmail'] ?? '';
    $taintedUsername = $taintedParameters['SiteUsername'] ?? '';
    $taintedPhoneNumber = $taintedParameters['PhoneNumber'] ?? '';

    $cleanedParameters['cleanedUserEmail'] = $validator->validateEmail('UserEmail',$taintedEmail);
    $cleanedParameters['cleanedSiteUsername'] = $validator->ValidateString('SiteUsername', $taintedUsername, 1, 26);
    $cleanedParameters['cleanedSitePassword'] = $taintedParameters['SitePassword'] ?? '';
    $cleanedParameters['cleanedConfirmPassword'] = $taintedParameters['ConfirmPassword'] ?? '';
    $cleanedParameters['cleanedPhoneNumber'] = $validator->validatePhoneNumber('PhoneNumber', $taintedPhoneNumber);

    return $cleanedParameters;
}

function getRegistrationValidationErrors($validator, $cleanedParameters): array
{
    $errors = [];

    if (empty($cleanedParameters['cleanedSitePassword'])) {
        $errors['SitePassword'] = 'This field is required!';
    } elseif (strlen($cleanedParameters['cleanedSitePassword']) > 100) {
        $errors['SitePassword'] = 'Password must be less than or equal to 100 characters!';
    }

    if (empty($cleanedParameters['cleanedConfirmPassword'])) {
        $errors['ConfirmPassword'] = 'This field is required!';
    } elseif ($cleanedParameters['cleanedConfirmPassword'] !== $cleanedParameters['cleanedSitePassword']) {
        $errors['ConfirmPassword'] = 'Passwords do not match!';
    }

    if (!$validator->areAllValid()) {
        $validatorErrors = $validator->getErrors();
        $errors = array_merge($errors, $validatorErrors);
    }

    return $errors;
}

function hashPassword($app, $passwordForHashing): string
{
    $bcryptWrapper = $app->getContainer()->get('BcryptWrapper');
    return $bcryptWrapper->hash($passwordForHashing);
}