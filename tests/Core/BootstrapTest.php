<?php

namespace Core;

use GWM\Core\Bootstrap;
use GWM\Core\Distributor;
use PHPUnit\Framework\TestCase;

class BootstrapTest extends TestCase
{
    public function testNodeInstalled(): void
    {
        $response = Distributor::node_installed();

        $result = [
            'status' => $response
        ];

        $json = json_encode($result, JSON_PRETTY_PRINT);

        $file = file_put_contents('tests/nodeInstalled.json', $json);

        $this->assertTrue($file != false);
    }

    public function testConnection() : void
    {
        $options = [
            'driver' => 'mysql',
            'host' => 'localhost',
            'username' => 'root',
            'password' => 'admin'
        ];

        //new Schema('test_app', $options);

        $this->assertTrue(true);
    }
}
