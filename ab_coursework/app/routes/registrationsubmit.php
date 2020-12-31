<?php

use ABCoursework\SessionManagerInterface;
use ABCoursework\SessionWrapperInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/registrationsubmit', function (Request $request, Response $response) use ($app) {

    $userEmailError = $usernameError = $phoneNumberError = $passwordError = $confirmPasswordError = '';

    $validator = $this->get('Validator');
    $taintedParameters = $request->getParsedBody();
    $cleanedParameters = cleanupParameters($validator, $taintedParameters);

    if (empty($cleanedParameters['cleanedSitePassword'])) {
        $passwordError = 'This field is required!';
    } elseif (strlen($cleanedParameters['cleanedSitePassword']) > 100) {
        $passwordError = 'Password must be less than or equal to 100 characters!';
    }

    if (empty($cleanedParameters['cleanedConfirmPassword'])) {
        $confirmPasswordError = 'This field is required!';
    } elseif ($cleanedParameters['cleanedConfirmPassword'] !== $cleanedParameters['cleanedSitePassword']) {
        $confirmPasswordError = 'Passwords do not match!';
    }

    if (!$validator->areAllValid()) {
        $validatorErrors = $validator->getErrors();
        $userEmailError = $validatorErrors['UserEmail'];
        $usernameError = $validatorErrors['SiteUsername'];
        $phoneNumberError = $validatorErrors['PhoneNumber'];
    }

    if ($validator->areAllValid() && empty($confirmPasswordError) && empty($passwordError)) {

        $hashedPassword = hashPassword($app, $cleanedParameters['cleanedSitePassword']);
        $cleanedParameters['cleanedPhoneNumber'] = (int)substr($cleanedParameters['cleanedPhoneNumber'], 1);

        $data = $this->get('SqlQueries');

        $dataExist = $data->checkUserDetailsExist($cleanedParameters);
        if (!empty($dataExist)) {
            if ($dataExist['username'] === $cleanedParameters['cleanedSiteUsername']) {
                $usernameError = 'Username Already Exists!';
            }
            if ($dataExist['email'] === $cleanedParameters['cleanedUserEmail']) {
                $userEmailError = 'Email Already In Use!';
            }
            if ((int)$dataExist['phone'] === $cleanedParameters['cleanedPhoneNumber']) {
                $phoneNumberError = 'Phone Number Already In Use!';
            }
        }

        if(empty($usernameError) && empty($userEmailError) && empty($phoneNumberError)) {
            $userId = $data->storeUserData($cleanedParameters, $hashedPassword);
            if ($userId) {
                $sessionWrapper = $this->get(SessionWrapperInterface::class);
                $sessionWrapper->set('userId', $userId);
                $this->get(SessionManagerInterface::class)::regenerate($sessionWrapper);
                $response = $response->withStatus(303);
                return $response->withHeader('Location', 'menu');
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
            'password_error' => $passwordError,
            'confirmPassword_error' => $confirmPasswordError,
            'userEmail_error' => $userEmailError,
            'username_error' => $usernameError,
            'phoneNumber_error' => $phoneNumberError
        ]);

})->setName('registrationsubmit');

function cleanupParameters($validator, $taintedParameters)
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

function hashPassword($app, $passwordForHashing)
{
    $bcryptWrapper = $app->getContainer()->get('BcryptWrapper');
    return $bcryptWrapper->hash($passwordForHashing);
}
