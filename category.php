<?php
require_once("helpers.php");
require_once("functions.php");
require_once("data.php");
require_once("init.php");
require_once("models.php");

$categories = get_categories($con);
$category_id = $_GET["id"] ?? null;
if ($category_id) {
    $category_name = get_category_name_by_id($con, $category_id);
    if ($category_name) {
        $items_count = get_count_lots_in_category($con, $category_id);
        $cur_page = $_GET["page"] ?? 1;
        $page_items = 9;
        $pages_count = ceil($items_count / $page_items);
        $offset = ($cur_page - 1) * $page_items;
        $pages = range(1, $pages_count);
        $lots_in_category = get_lots_in_category($con, $category_id, $page_items, $offset);

        if (!empty($lots_in_category)) {
            $goods = $lots_in_category;
        } else {
            $goods = [];
        }
    }
}

$header = include_template("header.php", [
    "categories" => $categories
]);

$page_content = include_template("lots-list.tpl.php", [
    "title" => "Все лоты в категории ",
    "categories" => $categories,
    "query" => $category_name,
    "goods" => $goods,
    "header" => $header,
    "pages_count" => $pages_count,
    "pages" => $pages,
    "cur_page" => $cur_page
]);

$layout_content = include_template("layout.tpl.php", [
    "content" => $page_content,
    "categories" => $categories,
    "title" => "Все лоты в категории ",
    "is_auth" => $is_auth,
    "user_name" => $user_name
]);

print($layout_content);
