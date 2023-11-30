<?php
session_start();
$is_auth = isset($_SESSION["name"]);
$user_name = '';

if ($is_auth) {
    $user_name = $_SESSION["name"];
}
