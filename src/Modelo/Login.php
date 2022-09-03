<?php

namespace SisLogin\Projeto\Modelo;

session_start();

use SisLogin\Projeto\Conexao\DB;

require_once 'autoload\autoload.php';

class Login
{
    private string $usuario;
    private string $token;

    public function logar(string $usuario, string $senha)
    {
        $sql = "SELECT usuario, senha FROM usuarios WHERE usuario = :usuario AND senha = :senha;";
        $prepare = DB::preparar($sql);
        $prepare->bindValue(':usuario', $usuario);
        $prepare->bindValue(':senha', sha1($senha));

        $prepare->execute();
        $usuario = $prepare->fetch();
        
        if (empty($usuario)) {
            return "Usuário ou senha inválidos!";
        }

        $this->token = sha1(uniqid().date('d-m-Y-H-i-s'));
        $_SESSION['TOKEN'] = $this->token;

        $sql = "UPDATE usuarios SET token = :token WHERE usuario = :usuario";
        $prepare = DB::preparar($sql);
        $prepare->execute([
            ':token' => $this->token,
            ':usuario' => $usuario['usuario']
        ]);

        header('location: bem_vindo.php');
    }

    public function estaAutenticado(string $token)
    {
        $sql = "SELECT usuario FROM usuarios WHERE token = :token";
        $preparar = DB::preparar($sql);
        $preparar->bindValue(':token', $token);

        $preparar->execute();
        $usuario = $preparar->fetch();
        
        $this->usuario = $usuario['usuario'];
    }

    public function getUsuario() : string
    {
        return $this->usuario;
    }
}
