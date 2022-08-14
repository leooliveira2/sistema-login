<?php

namespace SisLogin\src\Modelo;

use SisLogin\src\Modelo\Crud;
use SisLogin\src\Conexao\DB;

require_once 'src\Modelo\Crud.php';
require_once 'src\Conexao\DB.php';

class Usuario extends Crud
{   
    public string $usuario;
    public string $nome;
    private string $email;
    private string $senha;
    private string $repeteSenha = '';
    private array $erro = [];

    public function __construct(string $usuario, string $nome, string $email, string $senha) 
    {
        $this->usuario = $usuario;
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
    }

    public function setRepeteSenha(string $repeteSenha) : void {
        $this->repeteSenha = $repeteSenha;
    }
    
    public function validarCadastro() : array
    {
        if (!preg_match("/^[A-Za-z1-9'\s]+$/", $this->usuario)) {
            $this->erro['erro_usuario'] = "Por favor, informe um nome de usuário válido!";
        }
        
        if (!preg_match("/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ'\s]+$/", $this->nome)) {
            $this->erro['erro_nome'] = "Por favor, informe um nome válido!";
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->erro['erro_email'] = "Por favor, informe um E-mail válido!";
        }

        if (strlen($this->senha) < 8) {
            $this->erro['erro_senha'] = "Informe uma senha de no mínimo, 8 caracteres!";
        }

        if ($this->senha !== $this->repeteSenha) {
            $this->erro['erro_repeteSenha'] = "A repetição da senha está diferente escolhida!";
        }

        return $this->erro;
    }

    public function insert()
    {
        $sql = "INSERT INTO usuarios (usuario, nome, email, senha) VALUES (:usuario, :nome, :email, :senha);";
        $prepare = DB::preparar($sql);

        if ($prepare) {
            $criptoSenha = sha1($this->senha);
            
            return $prepare->execute([
                ':usuario' => $this->usuario,
                ':nome' => $this->nome,
                ':email' => $this->email,
                ':senha' => $criptoSenha
            ]);
        }
    }
}