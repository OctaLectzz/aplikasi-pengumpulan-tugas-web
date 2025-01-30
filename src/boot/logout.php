<?php
include 'src/boot/config.php';

session_destroy();
header('Location: /login');
exit;
