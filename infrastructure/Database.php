<?php

namespace Infrastructure;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{

    private Capsule $client;

    public function __construct()
    {
        $this->client = new Capsule();

        $this->client->addConnection([
            'driver' => 'mysql',
            'host' => 'database',
            'database' => 'larafony',
            'username' => 'root',
            'password' => 'secret',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]);

        $this->client->setAsGlobal();
        $this->client->bootEloquent();
    }

    /**
     * @return \Illuminate\Database\DatabaseManager
     */
    public function getClient(): \Illuminate\Database\DatabaseManager
    {
        return $this->client->getDatabaseManager();
    }
}
