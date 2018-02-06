<?php
namespace Wiard\Openstack;

use Dotenv\Dotenv;


class Client
{
    public function __construct()
    {
        $env = new Dotenv('./');
        $env->load();
    }
}