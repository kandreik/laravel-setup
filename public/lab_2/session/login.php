<?php
/*
 * Лабораторна робота №2, завдання 2. Форма входу.
 */

require "../inc/session.php";
openSession();

// Список користувачів: логін => хеш пароля.
// Паролі не зберігаються відкритим текстом: password_hash створює хеш,
// а password_verify перевіряє, чи відповідає йому введений пароль.
// Тестовий користувач: student / php2026
$users = [
    "student" => password_hash("php2026", PASSWORD_DEFAULT),
];

$failed = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login = trim((string)($_POST["login"] ?? ""));
    $password = (string)($_POST["password"] ?? "");

    // Спочатку перевіряємо, що такий логін існує, потім — пароль
    if (isset($users[$login]) && password_verify($password, $users[$login])) {
        // Новий ідентифікатор сесії після входу — стандартний захист від перехоплення сесії
        session_regenerate_id(true);

        // Запам'ятовуємо користувача в сесії
        $_SESSION["login"] = $login;
        $_SESSION["entered_at"] = time();

        // Перенаправлення на головну; exit обов'язковий, щоб скрипт не виконувався далі
        header("Location: index.php");
        exit;
    }

    $failed = true;
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>ЛР 2 — вхід</title>
</head>
<body>
    <h1>Вхід</h1>

    <?php if ($failed) { ?>
        <p><b>Логін або пароль неправильні.</b></p>
    <?php } ?>

    <form method="post">
        <label>Логін <input type="text" name="login"></label><br><br>
        <label>Пароль <input type="password" name="password"></label><br><br>
        <button type="submit">Увійти</button>
    </form>

    <p>Для перевірки: <code>student</code> / <code>php2026</code></p>
    <p><a href="index.php">Назад</a></p>
</body>
</html>
