<?php

interface ISingleton
{
    public function init();
    
    public static function get();
}