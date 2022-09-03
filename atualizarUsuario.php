<?php

use SisLogin\Projeto\Conexao\DB;

require_once 'autoload\autoload.php';

$id = 1;

$email = 'novoemail@gmail.com';

$prepare = DB::preparar("UPDATE usuarios SET email = :email WHERE id = :id");

if ($prepare->execute([
    ':email' => $email,
    ':id' => $id
])) {
    echo "Dados atualizados com sucesso!";
} else {
    echo "Erro ao atualizar os dados!";
}
