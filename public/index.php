<?php
session_start();
require_once __DIR__ . '/../app/core/Router.php';

$router = new \App\Core\Router();

// Essential routes for landing and auth
$router->add('GET', '/', 'LandingController', 'index');
$router->add('GET', '/login', 'AuthController', 'login');
$router->add('GET', '/register', 'AuthController', 'register');
$router->add('GET', '/challenge', 'ChallengeController', 'Challenge');
$router->add('GET', '/challenge/take', 'ChallengeController', 'Take');
$router->add('GET', '/progress', 'ProgressController', 'Progress');
$router->add('GET', '/history', 'HistoryController', 'History');
$router->add('GET', '/profile', 'ProfileController', 'Profile');
$router->add('GET', '/teacher', 'TeacherController', 'Teacher');
$router->add('GET', '/teacher/challenges', 'TeacherController', 'Challenges');
$router->add('GET', '/teacher/challenges/create', 'TeacherController', 'CreateChallenge');
$router->add('POST', '/teacher/challenges/create', 'TeacherController', 'CreateChallenge');
$router->add('GET', '/teacher/challenges/{id}/edit', 'TeacherController', 'EditChallenge');
$router->add('POST', '/teacher/challenges/{id}/edit', 'TeacherController', 'EditChallenge');
$router->add('GET', '/teacher/challenges/{id}/delete', 'TeacherController', 'DeleteChallenge');
$router->add('GET', '/teacher/challenges/{id}/submissions', 'TeacherController', 'Submissions');
$router->add('GET', '/teacher/submissions/{id}/grade', 'TeacherController', 'GradeSubmission');
$router->add('POST', '/teacher/submissions/{id}/grade', 'TeacherController', 'GradeSubmission');
$router->add('GET', '/logout', 'AuthController', 'logout');
// Student submission endpoint
$router->add('POST', '/challenge/submit', 'StudentController', 'SubmitChallenge');
$router->add('POST', '/challenge/submit-multiple', 'StudentController', 'SubmitBulk');

$router->run();
?>
