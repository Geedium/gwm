<?php

interface IPlugin
{

}

$composer = json_decode(file_get_contents('composer.json'), false)['required'];

return new class implements IPlugin {
    
};