<?php

/**
 * Returns environment variable.
 * @param string $key Used to find environment variable.
 * @param callable $on_error Triggered when key is invalid.
 * @return string|null
 */
function env(string $key, callable $on_error): ?string
{
    return isset($_ENV[$key]) ? $_ENV[$key] : call_user_func($on_error, $key);
}

