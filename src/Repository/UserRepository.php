<?php
declare(strict_types = 1);

namespace App\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use PDO;

class UserRepository
{
    /** @var Connection $conn */
    private $conn;

    /**
     * UserRepository constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->conn = $connection;
    }

    /**
     * @param array $record
     * @throws DBALException
     */
    public function insertUser(array $record)
    {
        list($name, $surname, $emil) = $record;
        $values['name'] = $name;
        $values['surname'] = $surname;
        $values['email'] = $emil;
        $types['name'] = PDO::PARAM_STR;
        $types['surname'] = PDO::PARAM_STR;
        $types['email'] = PDO::PARAM_STR;

        $sql = <<<SQL
INSERT IGNORE INTO CatTest.users (name, surname, email)
VALUES (:name, :surname, :email)
SQL;
        $this->conn->executeQuery($sql, $values, $types);
    }
}
