<?php
/*
 * Лабораторна робота №2, завдання 2. Робота з $_SESSION.
 * Головна сторінка: показує привітання, якщо користувач увійшов.
 */

require "../inc/session.php";
$expired = openSession();

// Користувач вважається авторизованим, якщо в сесії є його логін
$authorized = !empty($_SESSION["login"]);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>ЛР 2 — $_SESSION</title>
</head>
<body>
    <h1>Завдання 2. $_SESSION</h1>

    <?php if ($expired) { ?>
        <p><b>Вас не було більше 5 хвилин — сесію завершено.</b></p>
    <?php } ?>

    <?php if ($authorized) { ?>
        <h2>Вітаємо, <?php echo htmlspecialchars($_SESSION["login"]); ?>!</h2>
        <p>Час входу: <?php echo date("d.m.Y H:i:s", $_SESSION["entered_at"]); ?></p>
        <p>Ідентифікатор сесії: <?php echo session_id(); ?></p>
        <p><a href="logout.php">Вихід</a></p>
    <?php } else { ?>
        <p>Ви гість. <a href="login.php">Увійти</a></p>
    <?php } ?>

    <p><a href="../../menu.html">Меню</a></p>
</body>
</html>
