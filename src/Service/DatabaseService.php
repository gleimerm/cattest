<?php
declare(strict_types = 1);

namespace App\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

class DatabaseService
{

    /** @var Connection  $conn */
    private $conn;

    /**
     * DatabaseService constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->conn = $connection;
    }

    /**
     * @return int
     * @throws DBALException
     */
    public function createTable(): int
    {
        $sql = <<<SQL
CREATE DATABASE IF NOT EXISTS CatTest;
CREATE TABLE `CatTest`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `surname` VARCHAR(45) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC));
SQL;
        return $this->conn->exec($sql);
    }
}
