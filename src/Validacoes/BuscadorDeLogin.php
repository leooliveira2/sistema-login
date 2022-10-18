<?php

namespace SisLogin\Projeto\Validacoes;

use SisLogin\Projeto\Modelo\{Erros, Usuario};
use SisLogin\Projeto\Validacoes\VerificadorDeDadosExistentes;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class BuscadorDeLogin
{
    private Erros $controladorDeErros;
    private VerificadorDeDadosExistentes $verificadorDeDadosExistentes;

    const FORM_ERROR_USUARIO_NAO_EXISTE_NO_BANCO = 21;

    public function __construct(
        Erros $controladorDeErros, 
        VerificadorDeDadosExistentes $verificadorDeDadosExistentes
    )
    {
        $this->controladorDeErros = $controladorDeErros;
        $this->verificadorDeDadosExistentes = $verificadorDeDadosExistentes;
    }

    public function buscar(Usuario $usuario) : bool
    {
        $isValid = true;

        $usuarioESenhaEstaoCadastrados = $this->verificadorDeDadosExistentes
            ->verificaSeUsuarioESenhaEstaoCadastrados($usuario);
        
        if (!$usuarioESenhaEstaoCadastrados) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_USUARIO_NAO_EXISTE_NO_BANCO, 'Usuário ou senha inválidos!'
            );

            $isValid = false;
        }

        return $isValid;
    }
}
