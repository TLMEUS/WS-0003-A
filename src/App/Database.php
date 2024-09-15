<?php
/**
 * This file contains the src/App/database.php class for project WS-0003.
 *
 * File information:
 * Project Name: WS-0003
 * Module Name: Source
 * Group Name: App
 * File Name: database.php
 * File Author: Troy L Marker
 * Language: PHP 8.3
 *
 * File Copyright: 05/2024
 */
declare(strict_types=1);

namespace App;

use PDO;

/**
 * Database Class
 *
 * This class represents a database connection.
 * It provides methods to establish and retrieve the database connection.
 */
class Database {
    private ?PDO $pdo = null;

    /**
     * Class Constructor.
     *
     * @param string $host The hostname or IP address of the database server.
     * @param string $name The name of the database.
     * @param string $user The username for the database connection.
     * @param string $password The password for the database connection.
     *
     * @return void
     */
    public function __construct(private readonly string $host,
                                private readonly string $name,
                                private readonly string $user,
                                private readonly string $password) {
    }

    /**
     * Gets the database connection.
     *
     * @return PDO The PDO object representing the database connection.
     */
    public function getConnection(): PDO {
        if ($this->pdo === null) {
            $dsn = "mysql:host=$this->host;dbname=$this->name;charset=utf8;port=3306";
            $this->pdo = new PDO($dsn, $this->user, $this->password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        }
        return $this->pdo;
    }
}