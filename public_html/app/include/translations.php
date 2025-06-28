<?php

$translations = include __DIR__ . '/../../website/languages/en_US.php';

function __($key)
{
    global $translations;
    return $translations[$key] ?? $key;
}
