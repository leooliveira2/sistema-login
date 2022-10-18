<?php

use SisLogin\Projeto\Conexao\Conexao;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$id = 1;

$email = 'novoemail@gmail.com';

$conexao = new Conexao();
$pdo = $conexao->instanciar('banco.sqlite');

$prepare = $pdo->prepare("UPDATE usuarios SET email = :email WHERE id = :id");

if ($prepare->execute([
    ':email' => $email,
    ':id' => $id
])) {
    echo "Dados atualizados com sucesso!";
} else {
    echo "Erro ao atualizar os dados!";
}
