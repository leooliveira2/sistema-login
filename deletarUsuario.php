<?php

use SisLogin\Projeto\Conexao\DB;

require_once 'autoload\autoload.php';

$pdo = DB::instanciar();

$op = 2;

if ($op === 1) {
    $id = 1;
    $query = $pdo->query("SELECT * FROM usuarios WHERE id = $id");

    if(!$query->fetch()) {
        echo "Usuário não encontrado!";
    } else {
        $pdo->query("DELETE FROM usuarios WHERE id = $id");
        echo "Usuário deletado com sucesso!";
    }
} elseif ($op === 2) {
    $pdo->query("DELETE FROM usuarios;");
    echo "TODOS OS USUÁRIOS DELETADOS COM SUCESSO!";
}
