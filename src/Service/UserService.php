<?php
declare(strict_types = 1);

namespace App\Service;

use App\Repository\UserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

class UserService
{
    /** @var UserRepository $userRepository */
    private $userRepository;

    /**
     * UserService constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->userRepository = new UserRepository($connection);
    }

    /**
     * @param array $record
     * @throws DBALException
     */
    public function addUser(array $record)
    {
        $this->userRepository->insertUser($record);
    }
}
