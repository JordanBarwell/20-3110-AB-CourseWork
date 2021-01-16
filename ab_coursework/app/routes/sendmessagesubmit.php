<?php


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/sendmessagesubmit', function (Request $request, Response $response) use ($app) {

    $validator = $this->get('Validator');
    $input = $request->getParsedBody();
    $cleanedInput = cleanUpParams($validator, $input);
    $groupID = '<groupID>AB</groupID>';
    $tempMsg = '<Temperature>' . $cleanedInput['cleanedTemp'] . '</Temperature>';
    $fanDirectionMsg = '';
    $lastDigitMsg = '<LastDigitEntered>' . $cleanedInput['cleanedDigits'] . '</LastDigitEntered>';
    $switchMsg1 = $switchMsg2 = $switchMsg3 = $switchMsg4 = '';


    if(empty($input['Temperature'])){
        $errors['Temp'] = 'please enter a temperature';
    }

    if(empty($input['KeyPad'])){
        $errors['Digit'] = 'please enter a Digit';
    }

    if(empty($input['radioFan'])){
        $errors['Rad1'] = 'please select an option';
    }  elseif ($input['radioFan'] === "Forward"){
        $fanDirectionMsg = '<FanDirection>Forwards</FanDirection>';
    } elseif ($input['radioFan'] === "Reverse"){
        $fanDirectionMsg = '<FanDirection>Reverse</FanDirection>';
    }

    if (empty($input['radioSwitch1'])){
        $errors['Rad2'] = 'please select an option';
    } elseif ($input['radioSwitch1'] === "switchOneON"){
        $switchMsg1 = '<Switch1>ON</Switch1>';
    } elseif ($input['radioSwitch1'] === "switchOneOFF"){
        $switchMsg1 = '<Switch1>OFF</Switch1>';
    }

    if (empty($input['radioSwitch2'])){
        $errors['Rad3'] = 'please select an option';
    } elseif ($input['radioSwitch2'] === "switchTwoON"){
        $switchMsg2 = '<Switch2>ON</Switch2>';
    } elseif ($input['radioSwitch2'] === "switchTwoOFF"){
        $switchMsg2 = '<Switch2>OFF</Switch2>';
    }

    if (empty($input['radioSwitch3'])){
        $errors['Rad4'] = 'please select an option';
    } elseif ($input['radioSwitch3'] === "switchThreeOFF"){
        $switchMsg3 = '<Switch3>OFF</Switch3>';
    }  elseif ($input['radioSwitch3'] === "switchThreeON"){
        $switchMsg3 = '<Switch3>ON</Switch3>';
    }

    if (empty($input['radioSwitch4'])){
        $errors['Rad5'] = 'please select an option';
    } elseif ($input['radioSwitch4'] === "switchFourON"){
        $switchMsg4 = '<Switch4>ON</Switch4>';
    } elseif ($input['radioSwitch4'] === "switchFourOFF"){
        $switchMsg4 = '<Switch4>OFF</Switch4>';
    }

    $msgArray = [
        $groupID,
        $tempMsg,
        $fanDirectionMsg,
        $lastDigitMsg,
        $switchMsg1,
        $switchMsg2,
        $switchMsg3,
        $switchMsg4
    ];

    if(empty($errors))
    {
        $msgToSend = implode("\n", $msgArray);
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
        return $response->withHeader('Location', 'messagesent');
    }

        return $this->view->render($response,
            'sendmessage.html.twig',
            [
                'css_path' => CSS_PATH,
                'landing_page' => LANDING_PAGE,
                'page_title' => APP_NAME,
                'additional_info' => 'Created by Jared, Charlie and Jordan',
                'page_heading_1' => 'Send a Message',
                'action' => '',
                'method'=> 'post',
                'tempError' => $errors['Temp'] ?? '',
                'fanError' => $errors['Digit'] ?? '',
                'lastDigitError' => $errors['Rad1'] ?? '',
                'switch1Error' => $errors['Rad2'] ?? '',
                'switch2Error' => $errors['Rad3'] ?? '',
                'switch3Error' => $errors['Rad4'] ?? '',
                'switch4Error' => $errors['Rad5'] ?? ''
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