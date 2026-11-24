<?php
/*
 * Лабораторна робота №2, завдання 4. $_COOKIE і $_SESSION разом: корзина покупок.
 *
 * $_SESSION["basket"]      — товари, додані в поточній сесії;
 * cookie "purchases"       — оформлені раніше покупки, зберігаються 30 днів
 *                            і не зникають після завершення сесії.
 */

require "../inc/session.php";
$expired = openSession();

const PURCHASES_COOKIE = "purchases";
const MONTH = 60 * 60 * 24 * 30;

// Асортимент: код товару => назва
$goods = [
    "tea"    => "Чай зелений",
    "coffee" => "Кава мелена",
    "cocoa"  => "Какао",
    "honey"  => "Мед липовий",
    "jam"    => "Варення малинове",
];

// Якщо корзини в сесії ще немає — створюємо порожню
$_SESSION["basket"] ??= [];

// Cookie вміє зберігати тільки рядок, тому коди товарів записані через кому: "tea,honey".
// explode розбиває рядок на масив; array_intersect відкидає коди, яких немає в асортименті
// (вміст cookie користувач може відредагувати в браузері).
$stored = $_COOKIE[PURCHASES_COOKIE] ?? "";
$purchases = $stored === "" ? [] : array_values(array_intersect(explode(",", $stored), array_keys($goods)));

$do = $_POST["do"] ?? "";

if ($do === "put" && isset($goods[$_POST["code"] ?? ""])) {
    // Додаємо товар у корзину поточної сесії (без повторів)
    $code = $_POST["code"];
    if (!in_array($code, $_SESSION["basket"], true)) {
        $_SESSION["basket"][] = $code;
    }
}

if ($do === "drop") {
    // Прибираємо один товар із корзини
    $_SESSION["basket"] = array_values(array_diff($_SESSION["basket"], [$_POST["code"] ?? ""]));
}

if ($do === "buy" && count($_SESSION["basket"]) > 0) {
    // Оформлення: товари з корзини переходять у cookie «попередні покупки»
    $purchases = array_values(array_unique(array_merge($purchases, $_SESSION["basket"])));
    setcookie(PURCHASES_COOKIE, implode(",", $purchases), time() + MONTH);
    $_SESSION["basket"] = [];
}

if ($do === "reset") {
    // Очищення історії покупок
    setcookie(PURCHASES_COOKIE, "", time() - MONTH);
    $purchases = [];
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>ЛР 2 — корзина</title>
</head>
<body>
    <h1>Завдання 4. Корзина покупок</h1>

    <?php if ($expired) { ?>
        <p><b>Сесію завершено через 5 хвилин неактивності — корзина порожня.</b></p>
    <?php } ?>

    <h3>Асортимент</h3>
    <?php foreach ($goods as $code => $title) { ?>
        <form method="post" style="display:inline">
            <input type="hidden" name="do" value="put">
            <input type="hidden" name="code" value="<?php echo $code; ?>">
            <button type="submit">+ <?php echo $title; ?></button>
        </form>
    <?php } ?>

    <h3>Корзина (поточна сесія)</h3>
    <?php if (count($_SESSION["basket"]) === 0) { ?>
        <p>У корзині нічого немає.</p>
    <?php } else { ?>
        <ul>
        <?php foreach ($_SESSION["basket"] as $code) { ?>
            <li>
                <?php echo $goods[$code]; ?>
                <form method="post" style="display:inline">
                    <input type="hidden" name="do" value="drop">
                    <input type="hidden" name="code" value="<?php echo $code; ?>">
                    <button type="submit">прибрати</button>
                </form>
            </li>
        <?php } ?>
        </ul>
        <form method="post">
            <input type="hidden" name="do" value="buy">
            <button type="submit">Оформити покупку</button>
        </form>
    <?php } ?>

    <h3>Попередні покупки (cookie)</h3>
    <?php if (count($purchases) === 0) { ?>
        <p>Покупок ще не було.</p>
    <?php } else { ?>
        <ul>
        <?php foreach ($purchases as $code) { ?>
            <li><?php echo $goods[$code]; ?></li>
        <?php } ?>
        </ul>
        <form method="post">
            <input type="hidden" name="do" value="reset">
            <button type="submit">Очистити історію</button>
        </form>
    <?php } ?>

    <p>Щоб перевірити: оформіть покупку, закрийте браузер (сесія завершиться) і відкрийте сторінку знову —
       корзина буде порожня, а попередні покупки залишаться.</p>

    <p><a href="../../menu.html">Меню</a></p>
</body>
</html>
