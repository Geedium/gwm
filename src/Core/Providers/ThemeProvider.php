<?php

namespace GWM\Core\Providers;

/**
 * Theme Provider
 * @version 1.1.0
 */
class ThemeProvider
{
    /**
     * Resolve Theme Path
     *
     * @deprecated 1.2.0
     * @return string
     */
    public function resolve(): string
    {
        return "{GWM['DIR_ROOT']}/res/geedium.theme.bundle";
    }
}