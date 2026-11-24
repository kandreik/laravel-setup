<?php
/*
 * Лабораторна робота №2, завдання 3. Робота з $_SERVER.
 */

date_default_timezone_set("Europe/Kyiv");

// Автоматичне перенаправлення: сторінка зі звітом (?page=report) доступна тільки
// методом POST. Якщо її відкрили інакше — відправляємо на звичайну сторінку.
$isReport = ($_GET["page"] ?? "") === "report";

if ($isReport && $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php?denied=1");
    exit;
}

// Дані, які вимагає завдання. Ключі масиву $_SERVER заповнює веб-сервер.
$rows = [
    ["REMOTE_ADDR",     "IP-адреса клієнта"],
    ["HTTP_USER_AGENT", "Назва та версія браузера"],
    ["PHP_SELF",        "Назва скрипта, що виконується"],
    ["REQUEST_METHOD",  "Метод запиту"],
    ["SCRIPT_FILENAME", "Шлях до файлу на сервері"],
];
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>ЛР 2 — $_SERVER</title>
</head>
<body>
    <h1>Завдання 3. $_SERVER</h1>

    <?php if (isset($_GET["denied"])) { ?>
        <p><b>Звіт відкривається лише методом POST. Вас перенаправлено.</b></p>
    <?php } ?>

    <?php if ($isReport) { ?>
        <p><b>Звіт сформовано за POST-запитом о <?php echo date("H:i:s"); ?> (час сервера).</b></p>
    <?php } ?>

    <dl>
    <?php foreach ($rows as [$key, $title]) { ?>
        <dt><b><?php echo $title; ?></b> — $_SERVER['<?php echo $key; ?>']</dt>
        <!-- значення частково задає клієнт (наприклад, User-Agent), тому екрануємо -->
        <dd><?php echo htmlspecialchars($_SERVER[$key] ?? "немає даних"); ?></dd>
    <?php } ?>
    </dl>

    <h3>Перевірка перенаправлення</h3>
    <form method="post" action="index.php?page=report">
        <button type="submit">Відкрити звіт (POST)</button>
    </form>
    <p><a href="index.php?page=report">Відкрити звіт посиланням (GET)</a> — спрацює перенаправлення</p>

    <p><a href="../../menu.html">Меню</a></p>
</body>
</html>
