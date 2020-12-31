<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/downloadmessage', function (Request $request, Response $response) use ($app) {

    $soapWrapper = $this->get('SoapWrapper');

    $userID = $this->get(\ABCoursework\SessionWrapperInterface::class)->get('userId');

    $userData = $this->get('settings')['soap']['login'];

    $params = [
        'username' => $userData['username'],
        'password' => $userData['password'],
        'count' => 25,
        'deviceMsisdn' => '',
        'countryCode' => '44'
    ];

    $message = $soapWrapper->performSoapFunction($userID, 'peekMessages', $params);

    return $this->view->render($response,
        'messagedownloaded.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'page_title' => APP_NAME,
            'additional_info' => 'Created by Jared, Charlie and Jordan',
            'page_heading_1' => 'Downloaded!',
        ]);

})->setName('downloadmessage');

