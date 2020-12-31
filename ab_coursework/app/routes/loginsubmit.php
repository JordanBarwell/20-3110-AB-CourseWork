<?php

use ABCoursework\SessionManagerInterface;
use ABCoursework\SessionWrapperInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/loginsubmit', function (Request $request, Response $response) use ($app) {

    $usernameError = $passwordError = '';

    $validator = $this->get('Validator');

    $userInput = $request->getParsedBody();
    $username = $userInput['SiteUsername'] ?? '' ;
    $cleanedPassword = $userInput['SitePassword'] ?? '' ;
    $cleanedUsername = $validator->validateString('SiteUsername', $username, 1, 26);

    if (empty($cleanedPassword)) {
        $passwordError = 'This field is required!';
    } elseif (strlen($cleanedPassword) > 100) {
        $passwordError = 'Password must be less than or equal to 100 characters';
    }

    if (!$validator->isValid('SiteUsername')) {
        $usernameError = $validator->getError('SiteUsername');
    }

    if (empty($usernameError) && empty($passwordError)) {
        $queries = $this->get('SqlQueries');
        $userData = $queries->getUserData($cleanedUsername);

        if ($userData) {
            $bcryptWrapper = $this->get('BcryptWrapper');
            $passwordVerified = $bcryptWrapper->verify($cleanedPassword, $userData['password']);
            if ($userData['username'] === $cleanedUsername && $passwordVerified) {
                $sessionWrapper = $this->get(SessionWrapperInterface::class);
                $sessionWrapper->set('userId', $userData['id']);
                $this->get(SessionManagerInterface::class)::regenerate($sessionWrapper);
                $response = $response->withStatus(303);
                return $response->withHeader('Location', 'menu');
            }
        }

        $usernameError = $passwordError = 'Username or Password is incorrect!';
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

})->setName('loginsubmit');