<?php

$translations = include 'languages/en_US.php';

function __($key)
{
    global $translations;
    return $translations[$key] ?? $key;
}
