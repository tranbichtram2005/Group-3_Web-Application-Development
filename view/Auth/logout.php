<?php
require_once '../../control/AuthController.php';
$auth = new AuthController();
$auth->handleLogout();