<?php
session_start();
require_once __DIR__ . '/../app/core/Router.php';

$router = new \App\Core\Router();

// Essential routes for landing and auth
$router->add('GET', '/', 'LandingController', 'index');
$router->add('GET', '/login', 'AuthController', 'login');
$router->add('GET', '/register', 'AuthController', 'register');
$router->add('GET', '/challenge', 'ChallengeController', 'Challenge');
$router->add('GET', '/progress', 'ProgressController', 'Progress');
$router->add('GET', '/history', 'HistoryController', 'History');
$router->add('GET', '/profile', 'ProfileController', 'Profile');

$router->run();
?>
