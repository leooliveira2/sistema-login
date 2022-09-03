<?php

namespace SisLogin\Projeto\Modelo;

require_once 'autoload\autoload.php';
use SisLogin\Projeto\Modelo\{SalvaUsuarioNoBanco, ValidacaoDeCadastro, ValidaCadastro};

class Usuario implements ValidacaoDeCadastro
{
    protected string $usuario;
    protected string $nome;
    protected string $email;
    protected string $senha;
    protected string $repeteSenha;
    protected array $erros = [];

    public function __construct(
        string $usuario,
        string $nome,
        string $email,
        string $senha,
        string $repeteSenha
    ) {
        $this->usuario = $usuario;
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->repeteSenha = $repeteSenha;
    }

    // GETERS
    public function getUsuario() : string
    {
        return $this->usuario;
    }

    public function getNome() : string
    {
        return $this->nome;
    }

    public function getEmail() : string
    {
        return $this->email;
    }

    public function getSenha() : string
    {
        return sha1($this->senha);
    }

    // SALVA USUÁRIO NO BANCO
    public function salvarUsuarioNoBanco()
    {
        $salvar = new SalvaUsuarioNoBanco();
        $salvar->inserir($this);
    }

    public function validarCadastro(): array
    {
        $validar = new ValidaCadastro();
        $this->erros = $validar->validar(
            $this->usuario,
            $this->nome,
            $this->email,
            $this->senha,
            $this->repeteSenha
        );

        return $this->erros;
    }
}
