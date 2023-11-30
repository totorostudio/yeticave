<?php
function format_price ($price) {
$price = ceil($price);
if ($price > 1000) {
$price = number_format($price, 0, '', ' ');
}
return $price ." " ."₽";
}

function get_time_left($date) {
    date_default_timezone_set('Europe/Moscow');
    $final_date = strtotime($date . ' 23:59:59');
    $now = time();

    if ($now >= $final_date) {
        return "00:00";
    }

    $interval = $final_date - $now;
    $hours = floor($interval / (60 * 60));
    $minutes = floor(($interval % (60 * 60)) / 60);

    if ($hours >= 24) {
        $days = floor($hours / 24);
        $hours %= 24;
        $formatted_time = sprintf('%02d:%02d', $hours, $minutes);

        if ($days === 1) {
            return "1 день " . $formatted_time;
        } elseif ($days % 10 === 1 && $days !== 11) {
            return $days . " день " . $formatted_time;
        } elseif (($days % 10 >= 2 && $days % 10 <= 4) && ($days < 10 || $days > 20)) {
            return $days . " дня " . $formatted_time;
        } else {
            return $days . " дней " . $formatted_time;
        }
    }

    $formatted_time = sprintf('%02d:%02d', $hours, $minutes);
    return $formatted_time;
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return stmt Подготовленное выражение
 */
function db_get_prepare_stmt_version($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $key => $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);
        mysqli_stmt_bind_param(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Возвращает массив из объекта результата запроса
 * @param object $result_query mysqli Результат запроса к базе данных
 * @return array
 */
function get_arrow ($result_query) {
    $row = mysqli_num_rows($result_query);
    $arrow = [];
    
    if ($row === 1) {
        $arrow = mysqli_fetch_assoc($result_query);
    } else if ($row > 1) {
        $arrow = mysqli_fetch_all($result_query, MYSQLI_ASSOC);
    }

    return $arrow;
}

/**
 * Валидирует поле категории, если такой категории нет в списке
 * возвращает сообщение об этом
 * @param int $id категория, которую ввел пользователь в форму
 * @param array $allowed_list Список существующих категорий
 * @return string Текст сообщения об ошибке
 */
function validate_category ($id, $allowed_list) {
    if (!in_array($id, $allowed_list)) {
        return "Указана несуществующая категория";
    }
}
/**
 * Проверяет что содержимое поля является числом больше нуля
 * @param string $num число которое ввел пользователь в форму
 * @return string Текст сообщения об ошибке
 */
function validate_number ($num) {
    if (!empty($num)) {
        $num *= 1;
        if (is_int($num) && $num > 0) {
            return NULL;
        }
        return "Содержимое поля должно быть целым числом больше ноля";
    }
};

/**
 * Проверяет что дата окончания торгов не меньше одного дня
 * @param string $date дата которую ввел пользователь в форму
 * @return string Текст сообщения об ошибке
 */
function validate_date ($date) {
    if (is_date_valid($date)) {
        $now = date_create("now");
        $d = date_create($date);
        $diff = date_diff($d, $now);
        $interval = date_interval_format($diff, "%d");

        if ($interval < 1) {
            return "Дата должна быть больше текущей не менее чем на один день";
        };
    } else {
        return "Содержимое поля «дата завершения» должно быть датой в формате «ГГГГ-ММ-ДД»";
    }
};

/**
 * Проверяет что содержимое поля является корректным адресом электронной почты
 * @param string $email адрес электронной почты
 * @return string Текст сообщения об ошибке
 */
function validate_email ($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "E-mail должен быть корректным";
    }
}

/**
 * Проверяет что содержимое поля укладывается в допустимый диапазон
 * @param string $value содержимое поля
 * @param int $min минимальное количество символов
 * @param int $max максимальное количество символов
 * @return string Текст сообщения об ошибке
 */
function validate_length ($value, $min, $max) {
    if ($value) {
        $len = strlen($value);
        if ($len < $min or $len > $max) {
            return "Значение должно быть от $min до $max символов";
        }
    }
}

function isExpired($date_finish) {
    $current_time = time(); // Текущая метка времени (timestamp)
    $lot_finish_time = strtotime($date_finish); // Преобразование строки даты в timestamp

    // Если дата завершения меньше текущей даты, возвращаем true
    return $lot_finish_time < $current_time;
}
