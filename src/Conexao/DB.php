<?php

namespace SisLogin\Projeto\Conexao;

// aqui eu importaria o arquivo config, caso usasse o postgres ou qualquer outro banco que não fosse o sqlite

class DB
{
    private static $pdo;

    public static function instanciar() : \PDO
    {
        if (!isset(self::$pdo)) {
            try {
                $databasePath = __DIR__ . '\..\..\banco.sqlite';
                self::$pdo = new \PDO('sqlite:' . $databasePath);
                self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                echo "Falha na conexão com o banco!";
            }
        }

        return self::$pdo;
    }

    public static function preparar(string $sql) : \PDOStatement
    {
        $preparar = self::instanciar()->prepare($sql);
        return $preparar;
    }
}
