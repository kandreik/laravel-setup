<?php
/*
 * Лабораторна робота №2, завдання 5.
 * Спільний код для всіх сторінок, що працюють із сесією (завдання 2 і 4):
 * запускає сесію та завершує її, якщо користувач не був активний понад 5 хвилин.
 */

date_default_timezone_set("Europe/Kyiv");

// 5 хвилин, записані в секундах
define("INACTIVITY_LIMIT", 300);

/**
 * Запускає сесію і перевіряє час останньої активності.
 * Повертає true, якщо стару сесію щойно було завершено через неактивність.
 */
function openSession(): bool
{
    session_start();

    $now = time();
    $expired = false;

    // $_SESSION['last_activity'] — мітка часу попереднього завантаження будь-якої сторінки
    if (isset($_SESSION["last_activity"])) {
        $idle = $now - $_SESSION["last_activity"];   // скільки секунд користувач нічого не робив

        if ($idle > INACTIVITY_LIMIT) {
            // Знищуємо стару сесію разом з усіма даними й одразу відкриваємо нову, порожню
            session_unset();
            session_destroy();
            session_start();
            $expired = true;
        }
    }

    // Оновлюємо мітку при кожному завантаженні сторінки
    $_SESSION["last_activity"] = $now;

    return $expired;
}
