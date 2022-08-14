<?php

namespace SisLogin\src\Conexao;

require_once 'config.php';

class DB
{
    private static $pdo;

    public static function instanciar() : \PDO
    {
        if (!isset(self::$pdo)) {
            try {
                self::$pdo = new \PDO(DSN, USER, PASSWORD, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
                self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                echo "Falha na conexão com o banco!" . $e->getMessage();
            }
        }

        return self::$pdo;
    }

    public static function preparar(string $sql) : \PDOStatement
    {
        return self::instanciar()->prepare($sql);
    }
}