<?php

namespace SisLogin\Projeto\Modelo;

class Usuario
{
    public function __construct(
        private ?string $usuario = null,
        private ?string $nome = null,
        private ?string $email = null,
        private ?string $senha = null,
        private ?string $repeteSenha = null
    ) {}

    public function getUsuario() : ?string
    {
        return $this->usuario;
    }

    public function getNome() : ?string
    {
        return $this->nome;
    }

    public function getEmail() : ?string
    {
        return $this->email;
    }

    public function getSenha() : ?string
    {
        return $this->senha;
    }

    public function getRepeteSenha() : ?string
    {
        return $this->repeteSenha;
    }
}
