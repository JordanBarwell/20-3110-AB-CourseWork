<?php

/**
 * This file is for the loginsubmit route, when a user submits their existing details on the login form, this script will
 * validate their information, if it is valid, they will be sent to the menu and logged in, if it isn't valid or the user
 * doesn't exist in the database, they will be presented with the login form again with the appropriate errors to try again.
 */

use ABCoursework\BcryptWrapper;
use ABCoursework\SessionManagerInterface;
use ABCoursework\SessionWrapperInterface;
use ABCoursework\Validator;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/loginsubmit', function (Request $request, Response $response) use ($app) {

    $formName = 'loginform.html.twig';

    $validator = $this->get('Validator');
    $sessionWrapper = $this->get(SessionWrapperInterface::class);
    $sessionManager = $this->get(SessionManagerInterface::class);
    $model = $this->get('UserModel');
    $bcryptWrapper = $this->get('BcryptWrapper');

    $taintedParameters = $request->getParsedBody();
    $cleanedParameters = cleanupLoginParameters($validator, $taintedParameters);
    $errors = getLoginValidationErrors($validator, $sessionWrapper, $formName, $cleanedParameters);

    if (empty($errors)) {
        $loginData = $model->getLoginDetails($cleanedParameters['username']);

        if ($loginData
            && checkLoginDetailsMatch($bcryptWrapper, $loginData, $cleanedParameters)
            && $model->login($loginData['username'], $sessionWrapper, $sessionManager)
        ) {
            $response = $response->withStatus(303);
            return $response->withHeader('Location', 'menu');
        } else {
            $errors = array_merge($errors, $model->getErrors());
            $errors['username'] = $errors['password'] = 'Your username or password is incorrect.';
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
        'page_heading_2' => 'Login Form',
        'page_text' => 'Please Enter Your User Info',
        'database_error' => $errors['database'] ?? '',
        'csrf_error' => $errors['csrfToken'] ?? '',
        'username_error' => $errors['username'] ?? '',
        'password_error' => $errors['password'] ?? '',
        'csrf_token' => $this->get(SessionWrapperInterface::class)->getCsrfToken($formName)
    ]);

})->setName('loginsubmit');

/**
 * Takes in tainted user input, sanitises and validates it and returns the cleaned input.
 * @param Validator $validator Validator used to sanitise and validate user input.
 * @param array $taintedParameters User input from the login form.
 * @return array Sanitised and Validated log in details.
 */
function cleanupLoginParameters(Validator $validator, array $taintedParameters): array
{
    $taintedUsername = $taintedParameters['username'] ?? '';
    $taintedPassword = $taintedParameters['password'] ?? '';

    return [
        'csrfToken' => $taintedParameters['csrfToken'] ?? '',
        'username' => $validator->validateString('username', $taintedUsername, 1, 26),
        'password' => $validator->validatePassword('password', $taintedPassword),
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
function getLoginValidationErrors(
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

/**
 * Checks if the user inputted login details match the stored values.
 * @param BcryptWrapper $bcryptWrapper BCrypt Wrapper used to verify password is equal to stored hash.
 * @param array $loginDetails Stored login details used for comparison.
 * @param array $cleanedParameters Sanitised/Validated user input to compare to stored values.
 * @return bool Whether or not the user input login details match the stored values.
 */
function checkLoginDetailsMatch(BcryptWrapper $bcryptWrapper, array $loginDetails, array $cleanedParameters)
{
    $result = false;

    if ($loginDetails['username'] === $cleanedParameters['username']) {
        $result = $bcryptWrapper->verify($cleanedParameters['password'], $loginDetails['password']);
    }

    return $result;
}