<?php
/*
 * Лабораторна робота №3, завдання 2–4. Приймання файлу, надісланого з index.html.
 */

date_default_timezone_set("Europe/Kyiv");

define("STORAGE", __DIR__ . "/files");       // директорія для завантажених файлів
define("SIZE_LIMIT", 2 * 1024 * 1024);       // 2 МБ
define("ALLOWED", ["png", "jpg", "jpeg"]);   // дозволені розширення

/**
 * Перевіряє завантажений файл.
 * Повертає порожній рядок, якщо все гаразд, або текст помилки.
 */
function validateUpload(?array $upload): string
{
    if ($upload === null) {
        return "Форма не містить файлу.";
    }

    // Поле error заповнює сам PHP: 0 (UPLOAD_ERR_OK) означає, що файл дійшов без проблем
    switch ($upload["error"]) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            return "Ви не вибрали файл.";
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return "Файл перевищує дозволені 2 МБ.";
        default:
            return "Файл не вдалося передати (код помилки {$upload['error']}).";
    }

    // Чи файл справді потрапив на сервер через завантаження форми
    if (!is_uploaded_file($upload["tmp_name"])) {
        return "Файл не є завантаженим через форму.";
    }

    // Розмір: беремо фактичний розмір тимчасового файлу
    if (filesize($upload["tmp_name"]) > SIZE_LIMIT) {
        return "Файл перевищує дозволені 2 МБ.";
    }

    // Тип за розширенням: частина імені після останньої крапки
    $ext = strtolower(pathinfo($upload["name"], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED)) {
        return "Дозволені лише файли png, jpg, jpeg.";
    }

    // Тип за вмістом: getimagesize читає заголовок файлу і повертає false,
    // якщо всередині не зображення (наприклад, текст, перейменований на .png)
    $image = @getimagesize($upload["tmp_name"]);
    if ($image === false || !in_array($image["mime"], ["image/png", "image/jpeg"])) {
        return "Вміст файлу не відповідає формату png або jpeg.";
    }

    return "";
}

/**
 * Завдання 4. Підбирає вільне ім'я: якщо photo.png уже є, буде photo_1.png, photo_2.png і т.д.
 * Через параметр-посилання $wasTaken повідомляє, чи було початкове ім'я зайняте.
 */
function freeName(string $original, bool &$wasTaken): string
{
    $info = pathinfo(basename($original));    // basename відкидає шлях, якщо його підставили в ім'я
    $ext = strtolower($info["extension"]);
    // Прибираємо з імені все, крім літер, цифр, дефіса та підкреслення
    $stem = preg_replace('/[^\p{L}\p{N}_-]/u', "_", $info["filename"]);
    if ($stem === "") {
        $stem = "image";
    }

    $candidate = "$stem.$ext";
    $counter = 0;
    while (file_exists(STORAGE . "/" . $candidate)) {
        $counter++;
        $candidate = "{$stem}_{$counter}.{$ext}";
    }
    // Лічильник більший за нуль — отже, початкове ім'я було зайняте
    $wasTaken = $counter > 0;
    return $candidate;
}

$upload = $_FILES["picture"] ?? null;
$error = validateUpload($upload);
$saved = null;
$renamed = false;

if ($error === "") {
    // Директорію створюємо за потреби
    if (!is_dir(STORAGE)) {
        mkdir(STORAGE, 0775, true);
    }

    // $renamed стане true, якщо файл з таким ім'ям уже був і довелося додати номер
    $finalName = freeName($upload["name"], $renamed);

    // move_uploaded_file переносить файл із тимчасової директорії PHP у постійну
    if (move_uploaded_file($upload["tmp_name"], STORAGE . "/" . $finalName)) {
        $saved = [
            "name" => $finalName,
            "type" => $upload["type"],                               // MIME-тип, який повідомив браузер
            "size" => number_format($upload["size"] / 1024, 1, ",", " "),   // байти -> кілобайти
        ];
    } else {
        $error = "Сервер не зміг зберегти файл.";
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>ЛР 3 — завантаження</title>
</head>
<body>
<?php if ($saved !== null) { ?>
    <h1>Файл збережено</h1>

    <?php if ($renamed) { ?>
        <p><b>Увага:</b> файл «<?php echo htmlspecialchars(basename($upload["name"])); ?>» на сервері вже є,
           тому новий збережено під ім'ям «<?php echo htmlspecialchars($saved["name"]); ?>».</p>
    <?php } ?>

    <table border="1" cellpadding="5">
        <tr><td>Ім'я файлу</td><td><?php echo htmlspecialchars($saved["name"]); ?></td></tr>
        <tr><td>Тип</td><td><?php echo htmlspecialchars($saved["type"]); ?></td></tr>
        <tr><td>Розмір</td><td><?php echo $saved["size"]; ?> КБ</td></tr>
    </table>

    <!-- атрибут download: браузер зберігає файл на диск замість показу у вкладці -->
    <p><a href="files/<?php echo rawurlencode($saved["name"]); ?>" download>Завантажити цей файл із сервера</a></p>
<?php } else { ?>
    <h1>Файл не прийнято</h1>
    <p><?php echo htmlspecialchars($error); ?></p>
<?php } ?>

    <p><a href="index.html">Повернутися</a> · <a href="list.php">Вміст директорії</a></p>
</body>
</html>
