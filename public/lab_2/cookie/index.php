<?php
/*
 * Лабораторна робота №2, завдання 1. Робота з $_COOKIE.
 * Ім'я користувача зберігається в cookie на 7 днів.
 */

const NAME_COOKIE = "visitor_name";
const WEEK = 60 * 60 * 24 * 7;   // секунд у семи днях

$message = "";

// Усі виклики setcookie стоять до першого рядка HTML: cookie передається
// в HTTP-заголовках, а заголовки не можна надсилати після початку виводу сторінки.
if (isset($_POST["save"])) {
    $entered = trim((string)($_POST["visitor"] ?? ""));

    if ($entered === "") {
        $message = "Ім'я не може бути порожнім.";
    } else {
        // Третій аргумент — момент, коли cookie перестане діяти (час у форматі Unix)
        setcookie(NAME_COOKIE, $entered, time() + WEEK);
        // Браузер надішле нову cookie лише в наступному запиті, тому в цьому запиті
        // кладемо значення в $_COOKIE вручну, щоб одразу показати привітання
        $_COOKIE[NAME_COOKIE] = $entered;
    }
}

if (isset($_POST["remove"])) {
    // Щоб видалити cookie, її встановлюють ще раз із часом дії, який уже минув
    setcookie(NAME_COOKIE, "", time() - WEEK);
    unset($_COOKIE[NAME_COOKIE]);
    $message = "Cookie видалено.";
}

$hasCookie = isset($_COOKIE[NAME_COOKIE]);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>ЛР 2 — $_COOKIE</title>
</head>
<body>
    <h1>Завдання 1. $_COOKIE</h1>

    <?php if ($message !== "") { ?>
        <p><i><?php echo htmlspecialchars($message); ?></i></p>
    <?php } ?>

    <?php if ($hasCookie) { ?>
        <h2>З поверненням, <?php echo htmlspecialchars($_COOKIE[NAME_COOKIE]); ?>!</h2>
        <p>Ім'я збережене в cookie «<?php echo NAME_COOKIE; ?>» на 7 днів.</p>
        <form method="post">
            <button type="submit" name="remove" value="1">Видалити cookie</button>
        </form>
    <?php } else { ?>
        <p>Як до вас звертатися?</p>
        <form method="post">
            <input type="text" name="visitor" maxlength="40">
            <button type="submit" name="save" value="1">Запам'ятати мене</button>
        </form>
    <?php } ?>

    <p><a href="../../menu.html">Меню</a></p>
</body>
</html>
