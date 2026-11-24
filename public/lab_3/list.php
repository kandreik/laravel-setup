<?php
/*
 * Лабораторна робота №3, завдання 6. Вміст директорії із завантаженими файлами.
 */

date_default_timezone_set("Europe/Kyiv");

$dir = __DIR__ . "/files";
$items = [];
$totalBytes = 0;

if (is_dir($dir)) {
    // opendir відкриває директорію, readdir повертає по одному імені за виклик,
    // а коли імена закінчуються — false
    $handle = opendir($dir);
    while (($entry = readdir($handle)) !== false) {
        // "." і ".." — службові посилання на саму директорію та на батьківську;
        // також пропускаємо приховані файли (наприклад, .gitignore) і піддиректорії
        if ($entry[0] === "." || !is_file("$dir/$entry")) {
            continue;
        }
        $bytes = filesize("$dir/$entry");
        $totalBytes += $bytes;
        $items[$entry] = $bytes;
    }
    closedir($handle);

    // Сортуємо за іменем файлу (ключем масиву) в алфавітному порядку
    ksort($items);
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>ЛР 3 — вміст директорії</title>
</head>
<body>
    <h1>Завдання 6. Завантажені файли</h1>

    <?php if (count($items) === 0) { ?>
        <p>Директорія порожня.</p>
    <?php } else { ?>
        <ol>
        <?php foreach ($items as $name => $bytes) { ?>
            <li>
                <?php echo htmlspecialchars($name); ?>
                (<?php echo number_format($bytes / 1024, 1, ",", " "); ?> КБ) —
                <a href="files/<?php echo rawurlencode($name); ?>" download>завантажити</a>
            </li>
        <?php } ?>
        </ol>
        <p>Файлів: <?php echo count($items); ?>, загальний розмір:
           <?php echo number_format($totalBytes / 1024, 1, ",", " "); ?> КБ</p>
    <?php } ?>

    <p><a href="index.html">Повернутися</a></p>
</body>
</html>
