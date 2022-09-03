<?php

namespace SisLogin\Projeto\Modelo;

require_once 'autoload\autoload.php';

use SisLogin\Projeto\Conexao\DB;

class SalvaUsuarioNoBanco
{
    public function inserir(Usuario $usuario)
    {
        $sql = "INSERT INTO usuarios (usuario, nome, email, senha)
                VALUES (:usuario, :nome, :email, :senha);";

        $prepare = DB::preparar($sql);

        return $prepare->execute([
            ':usuario' => $usuario->getUsuario(),
            ':nome' => $usuario->getNome(),
            ':email' => $usuario->getEmail(),
            ':senha' => $usuario->getSenha()
        ]);
    }
}
