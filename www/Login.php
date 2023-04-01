<?php
session_start();
require_once "../bootstrap/bootstrap.php";

$login = "";
$password = "";
$loginSuccess = false;

readPost();
$loginSuccess = login();

if ($loginSuccess) {
    header("Location: index.php");
    exit;
} else {
    $_SESSION['loginError'] = "Špatné zadané jméno nebo heslo";
    header("Location: index.php");
    exit;
}

function readPost(): void
{
    global $login, $password;;
    $login = filter_input(INPUT_POST, 'login', FILTER_DEFAULT);
    $password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);
}

function login(): bool
{
    global $login, $password;

    $employees = Employee::all();
    foreach ($employees as $employee) {
        if ($employee->login === $login && $employee->password === $password) {
            $_SESSION['employee'] = $employee;
            return true;
        }
    }

    return false;
}