<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static string $host = "localhost";
    private static string $db_name  = "Desafio1LIS";
    private static string $username = "root";
    private static string $password = "";
    private static $statement = null;
    private static $error = null;

    private static ?PDO $connection = null;

    public static function connect()
    {
        if (self::$connection === null) {
            try {
                $datasource_name = "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ];

                self::$connection = new PDO($datasource_name, self::$username, self::$password, $options);
            } catch (PDOException $e) {
                die("Error a la conexión de base de datos: " . $e->getMessage());
            }
        }
    }

    //Método para leer todos los datos
    public static function getRows($query, $values)
    {
        try {
            self::connect();
            self::$statement = self::$connection->prepare($query);
            self::$statement->execute($values);
            // Cerrando conexión.
            self::$connection = null;
            return self::$statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            // Se obtiene el código y el mensaje de la excepción para establecer un error personalizado.
            self::setException($error->getCode(), $error->getMessage());
            return false;
        }
    }

    public static function getRow($query, $values)
    {
        try {
            self::connect();
            self::$statement = self::$connection->prepare($query);
            self::$statement->execute($values);
            // Se anula la conexión con el servidor de base de datos.
            self::$connection = null;
            return self::$statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            // Se obtiene el código y el mensaje de la excepción para establecer un error personalizado.
            self::setException($error->getCode(), $error->getMessage());
            return false;
        }
    }

    //Método para excepciones
    public static function setException($code, $message)
    {
        // Se asigna el mensaje del error original por si se necesita.
        self::$error = $message;
        // Códigos de error MySQL comunes:
        // 1062: Duplicate entry
        // 1054: Unknown column
        // 1146: Table doesn't exist
        // 1451: Cannot delete or update a parent row: a foreign key constraint fails
        // 1452: Cannot add or update a child row: a foreign key constraint fails
        switch ((string)$code) {
            case '1062':
                self::$error = 'Se han encontrado registros duplicados, escriba otros.';
                break;
            case '1054':
                self::$error = 'Nombre de campo desconocido';
                break;
            case '1146':
                self::$error = 'Nombre de tabla desconocido';
                break;
            case '1451':
                self::$error = 'Registro ocupado, no se puede eliminar';
                break;
            case '1452':
                self::$error = 'No se puede agregar o actualizar el registro debido a una restricción de clave foránea';
                break;
            default:
                self::$error = 'Ocurrió un problema en la base de datos: ' . $message;
        }
    }

    public static function getException()
    {
        return self::$error;
    }
}
