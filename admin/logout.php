<?php
require_once '../backend/autoload.php';
require_once '../backend/src/Auth.php';

use App\Auth;

$auth = new Auth();
$auth->logout();

header('Location: login.php');
exit;