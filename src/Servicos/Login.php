<?php

namespace SisLogin\Projeto\Servicos;

use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Validacoes\BuscadorDeLogin;
use SisLogin\Projeto\Servicos\Token;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class Login
{
    private BuscadorDeLogin $buscaDadosLogin;
    private Token $token;

    public function __construct(
        BuscadorDeLogin $buscaDadosLogin, 
        Token $token
    )
    {
        $this->buscaDadosLogin = $buscaDadosLogin;
        $this->token = $token;
    }

    public function logar(Usuario $usuario) : bool
    {
        $encontrouCadastro = $this->buscaDadosLogin->buscar($usuario);

        if ($encontrouCadastro) {
            $this->token->gerarToken();

            $this->token->atualizarTokenNoBanco($usuario);

            return true;
        }

        return false;
    }
}
