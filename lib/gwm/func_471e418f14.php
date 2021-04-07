<?php

/**
 * Dump
 *
 * @param mixed ...$params
 * @return void
 */
function dmp(...$params): void
{
    echo <<<HTML
<style>
    pre {
        padding: 10px !important;
        margin: auto !important;
    }
    *[class*="n-top"] {
        border-top: 0 !important;
    }
    *[class^="prop"] {
        border: 1px solid #f7f7f7;
        font-family: sans-serif;
        font-size: .85rem;
        background: white;
    }
    label {
        display: block !important;
        color: red !important;
        font-size: 1.2rem !important;
        text-align: center !important;
    }
</style>
HTML;
    echo '<label class="prop">Dead End</label>';

    echo '<pre class="prop n-top">';
    
    var_dump(...$params);

    die('</pre>');
}