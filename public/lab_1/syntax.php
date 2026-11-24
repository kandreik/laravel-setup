<?php
/*
 * Лабораторна робота №1. Ознайомлення із синтаксисом PHP.
 * Завдання 1–6 зібрані в одному файлі; кожне оформлене окремою функцією,
 * яка повертає готовий HTML, а внизу файлу всі вони виводяться по черзі.
 */

// ---------- Завдання 1: базовий скрипт ----------
function taskHello(): string
{
    // Функція нічого не друкує сама, а повертає рядок — виведемо його пізніше через echo
    return "Hello, World!";
}

// ---------- Завдання 2: змінні та типи даних ----------
function taskVariables(): string
{
    $city = "Харків";        // string — рядок
    $year = 2026;            // int — ціле число
    $temperature = -3.5;     // float — число з плаваючою комою
    $isWinter = false;       // bool — логічне значення

    $html = "";
    // Складаємо всі чотири змінні в масив, щоб вивести їх однаково в циклі
    foreach ([$city, $year, $temperature, $isWinter] as $value) {
        // gettype повертає назву типу; var_export показує значення так, як воно записане в коді
        // (на відміну від echo, який false виводить порожнім рядком)
        $html .= gettype($value) . ": " . var_export($value, true) . "<br>";
    }

    // var_dump друкує одразу на сторінку, тому перехоплюємо його вивід у буфер
    ob_start();
    var_dump($city, $year, $temperature, $isWinter);
    $html .= "<pre>" . ob_get_clean() . "</pre>";

    return $html;
}

// ---------- Завдання 3: конкатенація рядків ----------
function taskConcat(): string
{
    $greeting = "Доброго дня";
    $target = "шановні студенти";

    // Оператор «крапка» з'єднує рядки
    $phrase = $greeting . ", " . $target . "!";

    // Оператор .= дописує рядок у кінець уже наявного
    $phrase .= " Вітаємо на курсі.";

    return $phrase;
}

// ---------- Завдання 4: умовні конструкції ----------
function taskEvenOdd(int $number): string
{
    // % — остача від ділення; у парного числа остача від ділення на 2 дорівнює нулю
    if ($number % 2 == 0) {
        return "Число $number — парне";
    } else {
        return "Число $number — непарне";
    }
}

// ---------- Завдання 5: цикли ----------
function taskLoops(): string
{
    // for: початкове значення; умова продовження; зміна лічильника
    $up = [];
    for ($i = 1; $i <= 10; $i++) {
        $up[] = $i; // додаємо число в кінець масиву
    }

    // while: виконується, поки умова істинна
    $down = [];
    $n = 10;
    while ($n >= 1) {
        $down[] = $n;
        $n--;
    }

    // implode склеює елементи масиву в рядок через вказаний роздільник
    return "for: " . implode(", ", $up) . "<br>while: " . implode(", ", $down);
}

// ---------- Завдання 6: асоціативний масив ----------
function taskArray(): string
{
    // Ключі — рядки, а не порядкові номери
    $student = [
        "Ім'я"          => "Катерина",
        "Прізвище"      => "Андрейко",
        "Вік"           => 20,
        "Спеціальність" => "Business Technology Management",
    ];

    $html = "<b>До змін:</b>" . renderList($student);

    // Новий елемент — присвоєння за ключем, якого ще не було
    $student["Середній бал"] = 88.4;

    $html .= "<b>Після додавання середнього балу:</b>" . renderList($student);

    return $html;
}

// Допоміжна функція: перетворює асоціативний масив на HTML-список «ключ — значення»
function renderList(array $data): string
{
    $html = "<ul>";
    foreach ($data as $key => $value) {
        $html .= "<li>$key — $value</li>";
    }
    return $html . "</ul>";
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>ЛР 1 — синтаксис PHP</title>
</head>
<body>
    <h1>Лабораторна робота №1</h1>

    <h3>Завдання 1. Базовий скрипт</h3>
    <p><?php echo taskHello(); ?></p>

    <h3>Завдання 2. Змінні та типи даних</h3>
    <?php echo taskVariables(); ?>

    <h3>Завдання 3. Конкатенація рядків</h3>
    <p><?php echo taskConcat(); ?></p>

    <h3>Завдання 4. Умовні конструкції</h3>
    <p><?php echo taskEvenOdd(24); ?></p>
    <p><?php echo taskEvenOdd(7); ?></p>

    <h3>Завдання 5. Цикли</h3>
    <p><?php echo taskLoops(); ?></p>

    <h3>Завдання 6. Масиви</h3>
    <?php echo taskArray(); ?>

    <p><a href="index.html">Завдання 7 — форма</a> · <a href="../menu.html">Меню</a></p>
</body>
</html>
