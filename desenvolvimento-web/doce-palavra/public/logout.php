<?php
require_once __DIR__ . '/../src/lib/auth.php';
start_session();
session_destroy();
header('Location: login.php');
