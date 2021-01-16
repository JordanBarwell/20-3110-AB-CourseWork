<?php
/**
 * middleware.php
 *
 * Script to create closure middlewares and add all necessary middleware.
 *
 * Author: Team AB
 * Date: 31/12/2020
 */

use \Psr\Http\Message\{ServerRequestInterface, ResponseInterface};

/**
 * Checks if a user is logged in or not, if a not logged in user tries to access logged in pages, redirect to homepage.
 * If a logged in user tries to access pages for not logged users, redirect to menu.
 * @param ServerRequestInterface $request Psr7 ServerRequestInterface object.
 * @param ResponseInterface $response Psr7 ResponseInterface object.
 * @param callable $next The next middleware callable.
 * @return ResponseInterface A Ps7 ResponseInterface object to route or redirect the user.
 */
$loggedInMiddleware = function (ServerRequestInterface $request, ResponseInterface $response, callable $next) use ($app) {

    // Gets the route name that matches the requested URL.
    $route = $request->getAttribute('route');
    $routeName = $route->getName();

    // Define routes people who aren't logged in can access and what logged in users can't.
    $notLoggedInRoutes = [
        'homepage',
        'login',
        'loginsubmit',
        'registration',
        'registrationsubmit'
    ];

    $wrapper = $app->getContainer()->get(\ABCoursework\SessionWrapperInterface::class);
    $manager = $app->getContainer()->get(\ABCoursework\SessionManagerInterface::class);

    // Start the session.
    $manager::start($wrapper);

    if (!$wrapper->check('username') && !in_array($routeName, $notLoggedInRoutes)) {
        // If not logged and not private, redirect to homepage.
        return $response->withRedirect('/ab_coursework_public/');
    } elseif ($wrapper->check('username') && in_array($routeName, $notLoggedInRoutes)) {
        // Else if logged in and going to not logged in page go to menu.
        return $response->withRedirect('/ab_coursework_public/menu');
    } else {
        // Else carry on with request.
        $response = $next($request, $response);
    }

    return $response;
};

//Add Middleware to App.
$app->add($loggedInMiddleware);