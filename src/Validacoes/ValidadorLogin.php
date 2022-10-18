<?php

namespace SisLogin\Projeto\Validacoes;

use SisLogin\Projeto\Modelo\{Erros, Usuario};

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class ValidadorLogin
{
    private Erros $controladorDeErros;

    const FORM_ERROR_USUARIO_VAZIO = 1;
    const FORM_ERROR_SENHA_VAZIA = 9;

    public function __construct(Erros $controladorDeErros)
    {
        $this->controladorDeErros = $controladorDeErros;
    }

    public function validar(Usuario $usuario): bool
    {
        $isValid = true;

        if (!is_null($usuario->getUsuario()) && empty($usuario->getUsuario())) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_USUARIO_VAZIO, 
                "O campo usuário não pode ser vazio!"
            );

            $isValid = false;
        }

        if (!is_null($usuario->getSenha()) && empty($usuario->getSenha())) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_SENHA_VAZIA,
                "O campo senha não pode ser vazio!"
            );

            $isValid = false;
        }

        return $isValid;
    }
}
