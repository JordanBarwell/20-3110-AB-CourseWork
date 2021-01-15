<?php


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/sendmessagesubmit', function (Request $request, Response $response) use ($app) {

    $input = $request->getParsedBody();
    $tempMsg = 'Temperature is: ' . $input['Temperature'];
    $fanDirectionMsg = '';
    $lastDigitMsg = 'Last Digit Entered: ' . $input['KeyPad'];
    $switchMsg1 = '';
    $switchMsg2 = '';
    $switchMsg3 = '';
    $switchMsg4 = '';

    if ($input['radioFan'] === "Forward"){
          $fanDirectionMsg = 'Fan direction: Forwards';
    } elseif ($input['radioFan'] === "Reverse"){
        $fanDirectionMsg = 'Fan direction: Reverse';
    }

    if ($input['radioSwitch1'] === "switchOneON"){
        $switchMsg1 = 'Switch One is: ON';
    } elseif ($input['radioSwitch1'] === "switchOneOFF"){
        $switchMsg1 = 'Switch One is: OFF';
    }

    if ($input['radioSwitch2'] === "switchTwoON"){
        $switchMsg2 = 'Switch Two is: ON';
    } elseif ($input['radioSwitch2'] === "switchTwoOFF"){
        $switchMsg2 = 'Switch Two is: OFF';
    }

    if ($input['radioSwitch3'] === "switchThreeON"){
        $switchMsg3 = 'Switch Three is: ON';
    } elseif ($input['radioSwitch3'] === "switchThreeOFF"){
        $switchMsg3 = 'Switch Three is: OFF';
    }

    if ($input['radioSwitch4'] === "switchFourON"){
        $switchMsg4 = 'Switch Four is: ON';
    } elseif ($input['radioSwitch4'] === "switchFourOFF"){
        $switchMsg4 = 'Switch Four is: OFF';
    }

    $msgToSend = [
        $tempMsg,
        $fanDirectionMsg,
        $lastDigitMsg,
        $switchMsg1,
        $switchMsg2,
        $switchMsg3,
        $switchMsg4
    ];

    $stringMsg = implode("\n", $msgToSend);

    $soapWrapper = $this->get('SoapWrapper');

    $userID = $this->get(\ABCoursework\SessionWrapperInterface::class)->get('userId');

    $userData = $this->get('settings')['soap']['login'];

    $params = [
        'username' => $userData['username'],
        'password' => $userData['password'],
        'deviceMSISDN' => '+44',
        'message' => $stringMsg,
        'deliveryReport' => 0,
        'mtBearer' => 'SMS'
    ];

    var_dump($stringMsg);
    $sendMsg = $soapWrapper->performSoapFunction($userID, 'sendMessage', $params);

    return $this->view->render($response,
        'messagesent.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'page_title' => APP_NAME,
            'additional_info' => 'Created by Jared, Charlie and Jordan',
            'page_heading_1' => 'Message Sent!'
        ]);

})->setName('sendmessagesubmit');
