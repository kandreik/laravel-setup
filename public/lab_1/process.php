<?php
/*
 * Лабораторна робота №1, завдання 7.
 * Отримує ім'я та прізвище з index.html, перевіряє їх і виводить вітання.
 */

/**
 * Перевіряє одне поле форми.
 * Повертає текст помилки або null, якщо значення коректне.
 */
function checkField($value, string $title): ?string
{
    // Поля взагалі немає в запиті
    if ($value === null) {
        return "Поле «{$title}» не передано.";
    }
    // Перевірка типу: звичайне текстове поле приходить рядком
    if (!is_string($value)) {
        return "Поле «{$title}» має бути рядком.";
    }
    // Перевірка на порожнє значення (пробіли по краях не рахуються)
    if (trim($value) === "") {
        return "Поле «{$title}» порожнє.";
    }
    // Число замість імені — теж помилка типу даних
    if (is_numeric(trim($value))) {
        return "Поле «{$title}» не може бути числом.";
    }
    // mb_strlen рахує символи, а не байти (кирилична літера в UTF-8 займає 2 байти)
    if (mb_strlen(trim($value)) > 40) {
        return "Поле «{$title}» задовге (максимум 40 символів).";
    }
    return null;
}

$problems = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Оператор ?? повертає null, якщо ключа в масиві немає
    $name = $_POST["name"] ?? null;
    $surname = $_POST["surname"] ?? null;

    // array_filter без другого аргументу прибирає з масиву null-значення,
    // тож у $problems залишаються лише реальні помилки
    $problems = array_filter([
        checkField($name, "Ім'я"),
        checkField($surname, "Прізвище"),
    ]);
} else {
    $problems[] = "Сторінку відкрито напряму. Спочатку заповніть форму.";
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>ЛР 1 — вітання</title>
</head>
<body>
<?php if (count($problems) === 0) { ?>
    <?php
    // htmlspecialchars замінює < > & " на HTML-сутності: якщо в поле ввести тег,
    // він відобразиться як текст і не виконається в браузері
    $safeName = htmlspecialchars(trim($name));
    $safeSurname = htmlspecialchars(trim($surname));
    ?>
    <h1>Привіт, <?php echo $safeName . " " . $safeSurname; ?>!</h1>
    <p>Дані форми отримано методом <?php echo $_SERVER["REQUEST_METHOD"]; ?>.</p>
<?php } else { ?>
    <h1>Дані введено некоректно</h1>
    <ul>
    <?php foreach ($problems as $problem) { ?>
        <li><?php echo htmlspecialchars($problem); ?></li>
    <?php } ?>
    </ul>
<?php } ?>
    <p><a href="index.html">До форми</a></p>
</body>
</html>
