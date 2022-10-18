<?php

namespace SisLogin\Projeto\Servicos;

class Sessao
{
    public function iniciarSessao() : void
    {
        session_start();
    }
}
