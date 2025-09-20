<?php

namespace App\Config;

use PDO;
use PDOException;

/**
 * Clase Database - Manejo de conexiones y operaciones de base de datos
 * 
 * Esta clase proporciona métodos estáticos para gestionar la conexión a la base de datos
 * y realizar operaciones CRUD utilizando PDO con MySQL.
 * 
 * @author Tu Nombre
 * @version 1.0
 * @since 2025
 */
class Database
{
    /**
     * @var string $host Dirección del servidor de base de datos
     */
    private static string $host = "localhost";
    
    /**
     * @var string $db_name Nombre de la base de datos
     */
    private static string $db_name  = "Desafio1LIS";
    
    /**
     * @var string $username Nombre de usuario para la conexión
     */
    private static string $username = "root";
    
    /**
     * @var string $password Contraseña para la conexión
     */
    private static string $password = "";
    
    /**
     * @var PDOStatement|null $statement Declaración PDO para consultas preparadas
     */
    private static $statement = null;
    
    /**
     * @var string|null $error Almacena el último mensaje de error
     */
    private static $error = null;

    /**
     * @var PDO|null $connection Instancia de conexión PDO singleton
     */
    private static ?PDO $connection = null;

    /**
     * Establece la conexión con la base de datos utilizando PDO
     * 
     * Implementa el patrón singleton para asegurar una única instancia de conexión.
     * Si la conexión no existe, la crea con las configuraciones predefinidas.
     * 
     * @return void
     * @throws PDOException Si hay error en la conexión a la base de datos
     */
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

    /**
     * Obtiene múltiples registros de la base de datos
     * 
     * Ejecuta una consulta SQL preparada y retorna todos los registros encontrados.
     * Utiliza fetch mode FETCH_ASSOC para retornar arrays asociativos.
     * 
     * @param string $query Consulta SQL con marcadores de posición
     * @param array|null $values Valores para los marcadores de posición
     * @return array|false Array de registros encontrados o false en caso de error
     */
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

    /**
     * Ejecuta operaciones SQL de modificación (INSERT, UPDATE, DELETE)
     * 
     * Prepara y ejecuta una consulta SQL que modifica datos en la base de datos.
     * No retorna registros, solo el estado de la operación.
     * 
     * @param string $query Consulta SQL con marcadores de posición
     * @param array|null $values Valores para los marcadores de posición
     * @return bool true si la operación fue exitosa, false en caso de error
     */
    public static function executeRow($query, $values)
    {
        try {
            self::connect();
            self::$statement = self::$connection->prepare($query);
            $state = self::$statement->execute($values);
            // Se anula la conexión con el servidor de base de datos.
            self::$connection = null;
            return $state;
        } catch (PDOException $error) {
            // Se obtiene el código y el mensaje de la excepción para establecer un error personalizado.
            self::setException($error->getCode(), $error->getMessage());
            return false;
        }
    }

    /**
     * Obtiene un único registro de la base de datos
     * 
     * Ejecuta una consulta SQL preparada y retorna el primer registro encontrado.
     * Utiliza fetch mode FETCH_ASSOC para retornar un array asociativo.
     * 
     * @param string $query Consulta SQL con marcadores de posición
     * @param array|null $values Valores para los marcadores de posición
     * @return array|false Array asociativo del registro encontrado o false en caso de error
     */
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

    /**
     * Establece mensajes de error personalizados para códigos de excepción específicos
     * 
     * Convierte códigos de error técnicos de MySQL en mensajes amigables al usuario.
     * Los códigos más comunes están mapeados a mensajes descriptivos.
     * 
     * @param string|int $code Código de error MySQL
     * @param string $message Mensaje de error original de PDO
     * @return void
     */
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

    /**
     * Obtiene el último mensaje de error registrado
     * 
     * Retorna el último error establecido por el método setException.
     * Útil para obtener información sobre errores ocurridos durante las operaciones.
     * 
     * @return string|null Mensaje de error o null si no hay errores
     */
    public static function getException()
    {
        return self::$error;
    }
}
