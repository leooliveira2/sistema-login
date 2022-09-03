<?php

namespace SisLogin\Projeto\Modelo;

require_once 'autoload\autoload.php';

use SisLogin\Projeto\Conexao\DB;

class ValidaCadastro
{
    public function validar(
        string $usuario, 
        string $nome, 
        string $email, 
        string $senha, 
        string $repeteSenha
    ) : array
    {
        $sql = DB::preparar("SELECT usuario FROM usuarios WHERE usuario = :usuario");
        $sql->execute([
            ':usuario' => $usuario,
        ]);
        $usuarioVindoDoBanco = $sql->fetchColumn(0);

        $sql = DB::preparar("SELECT email FROM usuarios WHERE email = :email");
        $sql->execute([
            ':email' => $email,
        ]);
        $emailVindoDoBanco = $sql->fetchColumn(0);

        $erros = [];

        if (!preg_match("/^[A-Za-z1-9'\s]+$/", $usuario)) {
            $erros['erro_usuario'] = "Por favor, informe um nome de usuário válido!";
        }

        if (strlen($usuarioVindoDoBanco) >= 1) {
            $erros['erro_usuario'] = 'Usuário já cadastrado no sistema, por favor, escolha outro!';
        }

        if (!preg_match("/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ'\s]+$/", $nome)) {
            $erros['erro_nome'] = "Por favor, informe um nome válido!";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erros['erro_email'] = "Por favor, informe um E-mail válido!";
        }

        if (strlen($emailVindoDoBanco) >= 1) {
            $erros['erro_email'] = "Email ja cadastrado, por favor, escolha outro!";
        }

        if (strlen($senha) < 8) {
            $erros['erro_senha'] = "Informe uma senha de no mínimo, 8 caracteres!";
        }

        if ($senha !== $repeteSenha) {
            $erros['erro_repeteSenha'] = "A repetição da senha está diferente escolhida!";
        }

        return $erros;
    }
}
