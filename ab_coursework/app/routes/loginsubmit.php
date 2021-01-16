<?php

use ABCoursework\BcryptWrapper;
use ABCoursework\SessionManagerInterface;
use ABCoursework\SessionWrapperInterface;
use ABCoursework\Validator;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/loginsubmit', function (Request $request, Response $response) use ($app) {

    $validator = $this->get('Validator');
    $taintedParameters = $request->getParsedBody();
    $cleanedParameters = cleanupLoginParameters($validator, $taintedParameters);
    $errors = getLoginValidationErrors($validator, $cleanedParameters);

    if (empty($errors)) {
        $model = $this->get('UserModel');
        $loginData = $model->getLoginDetails($cleanedParameters['cleanedSiteUsername']);

        if ($loginData) {
            $bcryptWrapper = $this->get('BcryptWrapper');
            if (checkLoginDetailsMatch($bcryptWrapper, $loginData, $cleanedParameters)) {
                $sessionWrapper = $this->get(SessionWrapperInterface::class);
                $sessionManager = $this->get(SessionManagerInterface::class);
                if ($model->loginUser($loginData['username'], $sessionWrapper, $sessionManager)) {
                    $response = $response->withStatus(303);
                    return $response->withHeader('Location', 'menu');
                }
            } else {
                $errors['SiteUsername'] = $errors['SitePassword'] = 'Your username or password is incorrect.';
            }
        } else {
            $errors = array_merge($errors, $model->getErrors());
            $errors['SiteUsername'] = $errors['SitePassword'] = 'Your username or password is incorrect.';
        }
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
            'database_error' => $errors['Database'] ?? '',
            'username_error' => $errors['SiteUsername'] ?? '',
            'password_error' => $errors['SitePassword'] ?? ''
        ]);

})->setName('loginsubmit');

function cleanupLoginParameters(Validator $validator, $taintedParameters): array
{
    $cleanedParameters = [];

    $taintedUsername = $taintedParameters['SiteUsername'] ?? '' ;

    $cleanedParameters['cleanedSiteUsername'] = $validator->validateString('SiteUsername', $taintedUsername, 1, 26);
    $cleanedParameters['cleanedSitePassword'] = $taintedParameters['SitePassword'] ?? '' ;

    return $cleanedParameters;
}

function getLoginValidationErrors(Validator $validator, $cleanedParameters): array
{
    $errors = [];

    if (empty($cleanedParameters['cleanedSitePassword'])) {
        $errors['SitePassword'] = 'This field is required!';
    } elseif (strlen($cleanedParameters['cleanedSitePassword']) > 100) {
        $errors['SitePassword'] = 'Password must be less than or equal to 100 characters';
    }

    if (!$validator->areAllValid()) {
        $validatorErrors = $validator->getErrors();
        $errors = array_merge($errors, $validatorErrors);
    }

    return $errors;
}

function checkLoginDetailsMatch(BcryptWrapper $bcryptWrapper,array $loginDetails, array $cleanedParameters)
{
    $result = false;

    if ($loginDetails['username'] === $cleanedParameters['cleanedSiteUsername']) {
        $result = $bcryptWrapper->verify($cleanedParameters['cleanedSitePassword'], $loginDetails['password']);
    }

    return $result;
}