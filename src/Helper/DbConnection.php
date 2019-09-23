<?php
declare(strict_types = 1);

namespace App\Helper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;

class DbConnection
{
    const DB_PORT = 3306;
    const DB_DRIER = 'pdo_mysql';

    /**
     * @param array $params
     * @return Connection
     * @throws DBALException
     */
    public function getConnection(array $params = []): Connection
    {
        $connParams = [
            'host' => $params['host'],
            'port' => self::DB_PORT,
            'user' => $params['user'],
            'password' => $params['password'],
            'driver' => self::DB_DRIER,
        ];
        return DriverManager::getConnection($connParams);
    }
}
