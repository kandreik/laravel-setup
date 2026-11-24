<?php
/*
 * Лабораторна робота №3, завдання 5. Запис у файл log.txt і читання з нього.
 * Використано класичну трійку fopen / fwrite (або fgets) / fclose.
 */

date_default_timezone_set("Europe/Kyiv");

$path = __DIR__ . "/log.txt";
$status = "";

// ---- Запис ----
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $note = trim((string)($_POST["note"] ?? ""));

    if ($note === "") {
        $status = "Порожній текст не записується.";
    } else {
        // Перенесення рядків усередині тексту замінюємо пробілом: один запис = один рядок файлу
        $note = preg_replace('/\s+/u', " ", $note);

        // Режим "a" (append): відкрити для дописування в кінець; якщо файлу немає — створити
        $handle = fopen($path, "a");

        if ($handle === false) {
            $status = "Не вдалося відкрити log.txt для запису.";
        } else {
            // flock блокує файл, поки триває запис, щоб паралельний запит не перемішав рядки
            flock($handle, LOCK_EX);
            fwrite($handle, date("d.m.Y H:i:s") . " | " . $note . "\n");
            flock($handle, LOCK_UN);
            fclose($handle);   // закриваємо файл і звільняємо ресурс
            $status = "Текст додано до log.txt.";
        }
    }
}

// ---- Читання ----
$records = [];
if (is_readable($path)) {
    // Режим "r" (read): тільки читання
    $handle = fopen($path, "r");
    // fgets читає файл по одному рядку; коли рядки закінчуються, повертає false
    while (($line = fgets($handle)) !== false) {
        $line = rtrim($line, "\r\n");
        if ($line !== "") {
            $records[] = $line;
        }
    }
    fclose($handle);
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>ЛР 3 — log.txt</title>
</head>
<body>
    <h1>Завдання 5. Вміст log.txt</h1>

    <?php if ($status !== "") { ?>
        <p><i><?php echo htmlspecialchars($status); ?></i></p>
    <?php } ?>

    <?php if (count($records) === 0) { ?>
        <p>Записів поки немає.</p>
    <?php } else { ?>
        <p>Усього записів: <?php echo count($records); ?></p>
        <pre><?php
        foreach ($records as $record) {
            // Текст увів користувач — перед виводом екрануємо спеціальні символи HTML
            echo htmlspecialchars($record) . "\n";
        }
        ?></pre>
    <?php } ?>

    <p><a href="index.html">Повернутися</a></p>
</body>
</html>
