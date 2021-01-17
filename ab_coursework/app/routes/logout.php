<?php

/**
 * This file is for the logout route, a user can click logout from the menu and their session will be destroyed and they
 * will be shown a 'Log out successful' screen from which they can return to the homepage.
 */

use ABCoursework\SessionManagerInterface;
use ABCoursework\SessionWrapperInterface;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/logout', function (Request $request, Response $response) use ($app) {

    $wrapper = $this->get(SessionWrapperInterface::class);
    $manager = $this->get(SessionManagerInterface::class);
    $this->get('UserModel')->logout($wrapper, $manager);

    return $this->view->render($response, 'loggedout.html.twig', [
        'css_path' => CSS_PATH,
        'landing_page' => LANDING_PAGE,
        'page_title' => APP_NAME,
        'additional_info' => 'Created by Jared, Charlie and Jordan',
        'page_heading_1' => 'Logged Out!',
    ]);

})->setName('logout');
