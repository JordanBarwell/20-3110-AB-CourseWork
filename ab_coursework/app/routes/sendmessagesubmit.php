<?php


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/sendmessagesubmit', function (Request $request, Response $response) use ($app) {

    $validator = $this->get('Validator');
    $input = $request->getParsedBody();
    $cleanedInput = cleanUpParams($validator, $input);
    $tempMsg = '<Temperature>' . $cleanedInput['cleanedTemp'] . '</Temperature>';
    $fanDirectionMsg = '';
    $lastDigitMsg = '<LastDigitEntered>' . $cleanedInput['cleanedDigits'] . '</LastDigitEntered>';
    $switchMsg1 = $switchMsg2 = $switchMsg3 = $switchMsg4 = '';

    if ($input['radioFan'] === "Forward"){
          $fanDirectionMsg = '<FanDirection>Forwards</FanDirection>';
    } elseif ($input['radioFan'] === "Reverse"){
        $fanDirectionMsg = '<FanDirection>Reverse</FanDirection>';
    }

    if ($input['radioSwitch1'] === "switchOneON"){
        $switchMsg1 = '<Switch1>ON</Switch1>';
    } elseif ($input['radioSwitch1'] === "switchOneOFF"){
        $switchMsg1 = '<Switch1>OFF</Switch1>';
    }

    if ($input['radioSwitch2'] === "switchTwoON"){
        $switchMsg2 = '<Switch2>ON</Switch2>';
    } elseif ($input['radioSwitch2'] === "switchTwoOFF"){
        $switchMsg2 = '<Switch2>OFF</Switch2>';
    }

    if ($input['radioSwitch3'] === "switchThreeON"){
        $switchMsg3 = '<Switch3>ON</Switch3>';
    } elseif ($input['radioSwitch3'] === "switchThreeOFF"){
        $switchMsg3 = '<Switch3>OFF</Switch3>';
    }

    if ($input['radioSwitch4'] === "switchFourON"){
        $switchMsg4 = '<Switch4>ON</Switch4>';
    } elseif ($input['radioSwitch4'] === "switchFourOFF"){
        $switchMsg4 = '<Switch4>OFF</Switch4>';
    }

    $msgArray = [
        $tempMsg,
        $fanDirectionMsg,
        $lastDigitMsg,
        $switchMsg1,
        $switchMsg2,
        $switchMsg3,
        $switchMsg4
    ];

    $msgToSend = implode("\n", $msgArray);

    $soapWrapper = $this->get('SoapWrapper');

    $userID = $this->get(\ABCoursework\SessionWrapperInterface::class)->get('userId');

    $userData = $this->get('settings')['soap']['login'];

    $params = [
        'username' => $userData['username'],
        'password' => $userData['password'],
        'deviceMSISDN' => '+447757718816',
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
            'additional_info' => 'Created by Jared, Charlie and Jordan',
            'page_heading_1' => 'Message Sent!'
        ]);

})->setName('sendmessagesubmit');

function cleanUpParams($validator, $taintedParams){

    $cleanedParams = [];

    $taintedTemp = $taintedParams['Temperature'] ?? '';
    $taintedDigits = $taintedParams['KeyPad'] ?? '';

    $cleanedParams['cleanedTemp'] = $validator->validateString('Temperature', $taintedTemp, 1, 3);
    $cleanedParams['cleanedDigits'] = $validator->validateString('KeyPad', $taintedDigits);

    return $cleanedParams;
}
