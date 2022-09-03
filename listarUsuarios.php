<?php

use SisLogin\Projeto\Conexao\DB;

require_once 'autoload\autoload.php';

try {
    $pdo = DB::instanciar();
    $query = $pdo->query("SELECT * FROM usuarios");
    $usuarios = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "ERRO NA CONEXÃO COM O BANCO!";
}

if (empty($usuarios)) {
    echo "Nenhum usuário cadastrado no banco!";
} else {
    foreach ($usuarios as $usuario) {
        echo "ID: $usuario[id]" . PHP_EOL;
        echo "Usuário: $usuario[usuario]" . PHP_EOL;
        echo "Nome: $usuario[nome]" . PHP_EOL;
        echo "E-mail: $usuario[email]" . PHP_EOL;
        echo "Senha: $usuario[senha]" . PHP_EOL;
        echo "Token: $usuario[token]" . PHP_EOL;
        echo PHP_EOL;
    }
}