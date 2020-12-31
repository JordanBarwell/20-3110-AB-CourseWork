<?php


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/sendmessagesubmit', function (Request $request, Response $response) use ($app) {

    $input = $request->getParsedBody();
    $msgToSend = $input['Message'];

    $soapWrapper = $this->get('SoapWrapper');

    $userID = $this->get(\ABCoursework\SessionWrapperInterface::class)->get('userId');

    $userData = $this->get('settings')['soap']['login'];

    $params = [
        'username' => $userData['username'],
        'password' => $userData['password'],
        'deviceMSISDN' => '+44',
        'message' => $msgToSend,
        'deliveryReport' => 0,
        'mtBearer' => 'SMS'
    ];

    $sendMsg = $soapWrapper->performSoapFunction($userID, 'sendMessage', $params);

    return $this->view->render($response,
        'messagesent.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'page_title' => APP_NAME,
            'page_heading_1' => 'Message Sent!',
        ]);

})->setName('sendmessagesubmit');
