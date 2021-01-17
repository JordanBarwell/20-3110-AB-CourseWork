<?php

/**
 * This file contains the downloadmessage route, which downloads messages from the EE server, checks if they belong to
 * the team AB circuit board, if they do they are then added to the database for storage. It will display to the user
 * a page stating how many messages were downloaded and how many were for our circuit board.
 */

use ABCoursework\SessionWrapperInterface;
use ABCoursework\Validator;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/downloadmessage', function (Request $request, Response $response) use ($app) {

    $messageModel = $this->get('MessageModel');
    $xmlParser = $this->get('XmlParser');
    $validator = $this->get('Validator');
    $userModel = $this->get('UserModel');

    $username = $this->get(SessionWrapperInterface::class)->get('username');
    $userPhoneNumber = $userModel->getPhoneNumber($username);
    $messages = $messageModel->downloadMessages($username, 30);
    $latestMessageDateTime = $messageModel->getLatestMessageDateTime($username);
    $parsedMessages = [];

    if (empty($messageModel->getErrors())) {
        foreach ($messages as $message) {
            $message = $xmlParser->parseXml($message);
            $receivedTime = DateTime::createFromFormat('d/m/Y H:i:s', $message['RECEIVEDTIME']);
            if (isset($message['ID'])
                && $message['ID'] === 'GroupAB'
                && $receivedTime > $latestMessageDateTime
            ) {
                $parsedMessages[] = processMessageData($message, $validator);
            }
        }

        foreach ($parsedMessages as $parsedMessage) {
            $messageModel->insertMessage($username, $parsedMessage);
        }

        $message = count($messages) . ' new messages, ' . count($parsedMessages) . ' are from our circuit board.';
        $messageModel->sendMessage($username, $userPhoneNumber, $message);
    }

    return $this->view->render($response, 'messagedownloaded.html.twig', [
        'css_path' => CSS_PATH,
        'landing_page' => LANDING_PAGE,
        'page_title' => APP_NAME,
        'additional_info' => 'Created by Jared, Charlie and Jordan',
        'page_heading_1' => 'Downloaded!',
        'soap_error' => $messageModel->getErrors()['soap'] ?? '',
        'database_error' => $messageModel->getErrors()['database'] ?? '',
        'total_message_count' => count($messages),
        'app_message_count' => count($parsedMessages)
    ]);

})->setName('downloadmessage');

/**
 * Processes parsed XML data from the SOAP server, turning it into an array with keys and values the database will accept.
 * @param array $messageData Parsed message XML data from an XML parser.
 * @param Validator $validator Validator used to ensure temperature and keypad values are accurate.
 * @return array Processed data, with keys and values the database will accept for insertion.
 */
function processMessageData(array $messageData, Validator $validator): array
{
    $processedMessage = [
        'source' => $messageData['SOURCEMSISDN'],
        'bearer' => $messageData['BEARER'],
        'ref' => $messageData['MESSAGEREF']
    ];

    $receivedTime = DateTime::createFromFormat('d/m/Y H:i:s', $messageData['RECEIVEDTIME']);
    $processedMessage['received'] = $receivedTime->format('Y-m-d H:i:s');

    if (isset($messageData['TEMPERATURE'])
        && $validator->validateInt('temperature', $messageData['TEMPERATURE'], -150, 150) !== false
    ) {
        $processedMessage['temperature'] = $messageData['TEMPERATURE'];
    } else {
        $processedMessage['temperature'] = null;
    }

    if (isset($messageData['KEYPAD'])
        && $validator->validateInt('keypad', $messageData['KEYPAD'], 0, 9999) !== false
    ) {
        $processedMessage['keypad'] = $messageData['KEYPAD'];
    } else {
        $processedMessage['keypad'] = null;
    }

    if (isset($messageData['FAN'])
        && in_array($messageData['FAN'], ['Forward', 'forward', 'Reverse', 'reverse', 'N/A'])
    ) {
        $processedMessage['fan'] = ucfirst($messageData['FAN']);
    } else {
        $processedMessage['fan'] = 'N/A';
    }

    if (isset($messageData['SWITCHES'])) {
        $switches = explode(',', $messageData['SWITCHES']);
        if ($switches !== false && count($switches) === 4) {
            $switches['switchOne'] = $switches[0];
            $switches['switchTwo'] = $switches[1];
            $switches['switchThree'] = $switches[2];
            $switches['switchFour'] = $switches[3];
            foreach ($switches as $switchKey => $switchValue) {
                if (in_array($switchValue, ['On', 'on', 'Off', 'off', 'N/A'])) {
                    $processedMessage[$switchKey] = ucfirst($switchValue);
                } else {
                    $processedMessage[$switchKey] = 'N/A';
                }
            }
        }
    }

    if (!isset(
        $processedMessage['switchOne'],
        $processedMessage['switchTwo'],
        $processedMessage['switchThree'],
        $processedMessage['switchFour']
    )) {
        $processedMessage['switchOne'] = 'N/A';
        $processedMessage['switchTwo'] = 'N/A';
        $processedMessage['switchThree'] = 'N/A';
        $processedMessage['switchFour'] = 'N/A';
    }

    return $processedMessage;
}