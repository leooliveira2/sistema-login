<?php

namespace SisLogin\Projeto\Conexao;

class AcoesNoBancoDeDados extends \PDO
{
    public function __construct($dsn)
    {
        parent::__construct($dsn);
    }
}
