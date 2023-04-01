<?php
require_once "../bootstrap/bootstrap.php";
session_start();

session_destroy();

header("Location: index.php");
exit;
