<?php

/**
 * This file is for the sendmessagesubmit route, when a user submits settings to the circuit board, this route validates
 * and sanitises the data for sending, if it is valid, the settings are sent to the board via the SOAP server.
 */

use ABCoursework\SessionWrapperInterface;
use ABCoursework\Validator;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/sendmessagesubmit', function (Request $request, Response $response) use ($app) {

    $formName = 'sendmessage.html.twig';

    $validator = $this->get('Validator');
    $sessionWrapper = $this->get(SessionWrapperInterface::class);
    $messageModel = $this->get('MessageModel');

    $input = $request->getParsedBody();
    $cleanedInput = cleanupSendMessageParams($validator, $input);
    $errors = getSendMessageValidationErrors($validator, $sessionWrapper, $formName, $cleanedInput);

    $radioInputs = ['fan', 'switchOne', 'switchTwo', 'switchThree', 'switchFour'];
    foreach ($radioInputs as $input) {
        if ($input === 'fan') {
            if (!($cleanedInput[$input] === 'Forward' || $cleanedInput[$input] === 'Reverse')) {
                $cleanedInput[$input] = 'N/A';
            }
        } else {
            if (!($cleanedInput[$input] === 'On' || $cleanedInput[$input] === 'Off')) {
                $cleanedInput[$input] = 'N/A';
            }
        }
    }

    if(empty($errors)) {
        $username = $sessionWrapper->get('username');
        $message = createMessage($cleanedInput);
        if ($messageModel->sendMessage($username, '447817814149', $message)) {
            return $response->withHeader('Location', 'messagesent');
        } else {
            $errors = array_merge($errors, $messageModel->getErrors());
        }
    }

    return $this->view->render($response, $formName, [
        'css_path' => CSS_PATH,
        'landing_page' => LANDING_PAGE,
        'page_title' => APP_NAME,
        'additional_info' => 'Created by Jared, Charlie and Jordan',
        'page_heading_1' => 'Send a Message',
        'action' => '',
        'method'=> 'post',
        'database_error' => $errors['database'] ?? '',
        'soap_error' => $errors['soap'] ?? '',
        'csrf_error' => $errors['csrfToken'] ?? '',
        'temperatureError' => $errors['temperature'] ?? '',
        'fanError' => $errors['fan'] ?? '',
        'keypadError' => $errors['keypad'] ?? '',
        'switchOneError' => $errors['switchOne'] ?? '',
        'switchTwoError' => $errors['switchTwo'] ?? '',
        'switchThreeError' => $errors['switchThree'] ?? '',
        'switchFourError' => $errors['switchFour'] ?? '',
        'csrf_token' => $sessionWrapper->getCsrfToken($formName)
    ]);

})->setName('sendmessagesubmit');

/**
 * Validates and sanitises user input, to check it suits the requirements of the form and the application.
 * @param Validator $validator Validator used to sanitise and validate user input.
 * @param array $taintedParams Tainted user input to be sanitised and validated.
 * @return array Cleaned user input to be used to register the user.
 */
function cleanupSendMessageParams(Validator $validator, array $taintedParams): array
{
    $taintedTemperature = $taintedParams['temperature'] ?? '';
    $taintedKeypad = $taintedParams['keypad'] ?? '';

    $cleanedParams = [
        'temperature' => $validator->validateInt('temperature', $taintedTemperature, -150, 150),
        'keypad' => $validator->validateInt('keypad', $taintedKeypad, 0, 9999),
        'csrfToken' => $taintedParams['csrfToken'] ?? ''
    ];

    $radioInputs = ['fan', 'switchOne', 'switchTwo', 'switchThree', 'switchFour'];
    foreach ($radioInputs as $input) {
        $cleanedParams[$input] = $taintedParams[$input] ?? '';
    }

    return $cleanedParams;
}

/**
 * Gets errors from the validator, checks radio button options are set and also checks to see if the CSRF token input
 * matches the stored session value.
 * @param Validator $validator Validator to retrieve errors.
 * @param SessionWrapperInterface $sessionWrapper Session wrapper used to verify CSRF token.
 * @param string $formName Name of Twig template used to verify CSRF token.
 * @param array $cleanedInput Cleaned parameters for retrieving user input CSRF token.
 * @return array All validation and CSRF errors.
 */
function getSendMessageValidationErrors(
    Validator $validator,
    SessionWrapperInterface $sessionWrapper,
    string $formName,
    array $cleanedInput
): array
{
    $errors = $validator->getErrors();

    if (!$sessionWrapper->verifyCsrfToken($cleanedInput['csrfToken'], $formName)) {
        $errors['csrfToken'] = 'CSRF Error, please try again';
    }

    $radioInputs = ['fan', 'switchOne', 'switchTwo', 'switchThree', 'switchFour'];
    foreach ($radioInputs as $input) {
        if (empty($cleanedInput[$input])) {
            $errors[$input] = 'Please select an option';
        }
    }

    return $errors;
}

/**
 * Creates an XML message to send to the circuit board through the SOAP server.
 * @param array $cleanedInput Cleaned input to use to create the message.
 * @return string XML Message to be sent to the circuit board.
 */
function createMessage(array $cleanedInput): string
{
    $message = '<id>GroupAB</id>';
    $message.= "<temperature>{$cleanedInput['temperature']}</temperature>";
    $message.= "<fan>{$cleanedInput['fan']}</fan>";
    $message.= "<keypad>{$cleanedInput['keypad']}</keypad>";
    $message.= '<switches>';
    $message.= $cleanedInput['switchOne'].',';
    $message.= $cleanedInput['switchTwo'].',';
    $message.= $cleanedInput['switchThree'].',';
    $message.= $cleanedInput['switchFour'];
    $message.= '</switches>';

    return $message;
}