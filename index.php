<?php
require_once("helpers.php");
require_once("functions.php");
require_once("data.php");
require_once("init.php");
require_once("models.php");

session_start();

if (!$con) {
    $error = mysqli_connect_error();
} else {
    $sql = "SELECT id, character_code, name_category FROM categories";
    $result = mysqli_query($con, $sql);
    if ($result) {
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $error = mysqli_error($con);
    }
}

$sql = get_query_list_lots();

$res = mysqli_query($con, $sql);
if ($res) {
    $goods = mysqli_fetch_all($res, MYSQLI_ASSOC);
} else {
    $error = mysqli_error($con);
}

$page_content = include_template("main.tpl.php", [
    "categories" => $categories,
    "goods" => $goods
]);

if (empty($page_content)) {
    http_response_code(404);
    $page_content = include_template("404.tpl.php", []);
}

$layout_content = include_template("layout.tpl.php", [
    "content" => $page_content,
    "categories" => $categories,
    "title" => "Главная",
    "is_auth" => $is_auth,
    "user_name" => $user_name
]);

print($layout_content);
