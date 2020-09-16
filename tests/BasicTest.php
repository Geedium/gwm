<?php

use PHPUnit\Framework\TestCase;
use GWM\Core\Schema;

final class BasicTest extends TestCase
{
    public function testConnection() : void
    {
        $options = [
            'driver' => 'mysql',
            'host' => 'localhost',
            'username' => 'root',
            'password' => 'admin'
        ];

        new Schema('test_app', $options);
        
        $this->assertTrue(true);
    }
}