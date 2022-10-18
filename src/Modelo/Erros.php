<?php

namespace SisLogin\Projeto\Modelo;

class Erros
{
    private $erros = [];

    public function add($codigo, $descricao) : void
    {
        $this->erros[$codigo] = $descricao;
    }

    public function getErros() : array
    {
        return $this->erros;
    }
}
