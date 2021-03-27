<?php

namespace GWM\Core\Interfaces
{
    interface ITemplateEngine
    {
        function Parse(string $path, array $params = []);

    }
}