<?php
require_once '../includes/config.php';
session_destroy();
redirect('/mitsubishi/admin/login.php');
