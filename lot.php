<?php
require_once("helpers.php");
require_once("functions.php");
require_once("data.php");
require_once("init.php");
require_once("models.php");

$error = '';

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

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if ($id) {
    $sql = get_query_lot ($id);
} else {
    http_response_code(404);
    die();
}

$res = mysqli_query($con, $sql);

if ($res) {
    $lot = mysqli_fetch_assoc($res);
} else {
    $error = mysqli_error($con);
}

if(!$lot) {
    http_response_code(404);
    die();
}

$history = get_bets_history($con, $id);
if (!empty($history)) {
    $current_price = max($lot["start_price"], $history[0]["price_bet"] ?? 0);
} else {
    $current_price = $lot["start_price"];
}
$min_bet = $current_price + ($lot["step"] ?? 0);

$page_content = include_template("lot.tpl.php", [
    "categories" => $categories,
    "lot" => $lot,
    "name_category" => $lot['name_category'],
    "is_auth" => $is_auth,
    "current_price" => $current_price,
    "min_bet" => $min_bet,
    "id" => $id,
    "history" => $history,
    "error" => $error,
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bet = filter_input(INPUT_POST, "cost", FILTER_VALIDATE_INT);

    if ($bet < $min_bet) {
        $error = "Ставка не может быть меньше $min_bet";
    }
    if (empty($bet)) {
        $error = "Ставка должна быть целым числом, болше ноля";
    }

    if ($error) {
        $page_content = include_template("lot.tpl.php", [
            "categories" => $categories,
            "lot" => $lot,
            "is_auth" => $is_auth,
            "current_price" => $current_price,
            "min_bet" => $min_bet,
            "error" => $error,
            "id" => $id,
            "history" => $history
        ]);
    } else {
        $res = add_bet_database($con, $bet, $_SESSION["id"], $id);
        header("Location: /lot.php?id=" .$id);
    }
}

$layout_content = include_template("layout.tpl.php", [
    "content" => $page_content,
    "categories" => $categories,
    "name_category" => $lot['name_category'],
    "title" => $lot["title"],
    "is_auth" => $is_auth,
    "user_name" => $user_name
]);

print($layout_content);
