<?php

/**
 * This file is for the registrationsubmit route, once a user submits the registration form, the input will be validated
 * and sanitised, if it is valid, the user will be registered and logged in to their new account, if it isn't, the registration
 * form will be displayed again with appropriate errors.
 */

use ABCoursework\SessionManagerInterface;
use ABCoursework\SessionWrapperInterface;
use ABCoursework\Validator;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/registrationsubmit', function (Request $request, Response $response) use ($app) {

    $formName = 'registrationform.html.twig';

    $validator = $this->get('Validator');
    $sessionWrapper = $this->get(SessionWrapperInterface::class);
    $sessionManager = $this->get(SessionManagerInterface::class);
    $model = $this->get('UserModel');

    $taintedParameters = $request->getParsedBody();
    $cleanedParameters = cleanupRegistrationParameters($validator, $taintedParameters);
    $errors = getRegistrationValidationErrors($validator, $sessionWrapper, $formName, $cleanedParameters);

    if (empty($errors)) {
        $cleanedParameters['password'] = $this->get('BcryptWrapper')->hash($cleanedParameters['password']);
        $cleanedParameters['phoneNumber'] = (int)substr($cleanedParameters['phoneNumber'], 1);

        if (
            !$model->checkExists($cleanedParameters)
            && $model->register($cleanedParameters, $sessionWrapper, $sessionManager)
        ) {
            $response = $response->withStatus(303);
            return $response->withHeader('Location', 'menu');
        } else {
            $errors = array_merge($errors, $model->getErrors());
        }
    }

    return $this->view->render($response, $formName, [
        'css_path' => CSS_PATH,
        'landing_page' => LANDING_PAGE,
        'page_title' => APP_NAME,
        'action' => '',
        'method' => 'post',
        'additional_info' => 'Created by Jared, Charlie and Jordan',
        'page_heading_1' => 'Team AB Coursework',
        'page_heading_2' => 'Registration Form',
        'page_text' => 'Please Create Your New User Info',
        'database_error' => $errors['database'] ?? '',
        'csrf_error' => $errors['csrfToken'] ?? '',
        'email_error' => $errors['email'] ?? '',
        'username_error' => $errors['username'] ?? '',
        'password_error' => $errors['password'] ?? '',
        'confirmPassword_error' => $errors['confirmPassword'] ?? '',
        'phoneNumber_error' => $errors['phoneNumber'] ?? '',
        'csrf_token' => $this->get(SessionWrapperInterface::class)->getCsrfToken($formName)
    ]);

})->setName('registrationsubmit');

/**
 * Validates and sanitises user input, to check it suits the requirements of the form and the application.
 * @param Validator $validator Validator used to sanitise and validate user input.
 * @param array $taintedParameters Tainted user input to be sanitised and validated.
 * @return array Cleaned user input to be used to register the user.
 */
function cleanupRegistrationParameters(Validator $validator, array $taintedParameters): array
{
    $taintedEmail = $taintedParameters['email'] ?? '';
    $taintedUsername = $taintedParameters['username'] ?? '';
    $taintedPhoneNumber = $taintedParameters['phoneNumber'] ?? '';
    $taintedPassword = $taintedParameters['password'] ?? '';
    $taintedConfirmPassword = $taintedParameters['confirmPassword'] ?? '';

    return [
        'csrfToken' => $taintedParameters['csrfToken'] ?? '',
        'email' => $validator->validateEmail('email', $taintedEmail),
        'username' => $validator->ValidateString('username', $taintedUsername, 1, 26),
        'phoneNumber' => $validator->validatePhoneNumber('phoneNumber', $taintedPhoneNumber),
        'password' => $validator->validatePassword('password', $taintedPassword),
        'confirmPassword' => $validator->validateConfirmPassword('confirmPassword', $taintedConfirmPassword, $taintedPassword)
    ];
}

/**
 * Gets errors from the validator and also checks to see if the CSRF token input matches the stored session value.
 * @param Validator $validator Validator to retrieve errors.
 * @param SessionWrapperInterface $sessionWrapper Session wrapper used to verify CSRF token.
 * @param string $formName Name of Twig template used to verify CSRF token.
 * @param array $cleanedParameters Cleaned parameters for retrieving user input CSRF token.
 * @return array All validation and CSRF errors.
 */
function getRegistrationValidationErrors (
    Validator $validator,
    SessionWrapperInterface $sessionWrapper,
    string $formName,
    array $cleanedParameters
): array
{
    $errors = $validator->getErrors();

    if (!$sessionWrapper->verifyCsrfToken($cleanedParameters['csrfToken'], $formName)) {
        $errors['csrfToken'] = 'CSRF Error, please try again';
    }

    return $errors;
}