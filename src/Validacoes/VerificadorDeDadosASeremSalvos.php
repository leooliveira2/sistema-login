<?php

namespace SisLogin\Projeto\Validacoes;

use SisLogin\Projeto\Modelo\{Usuario, Erros};
use SisLogin\Projeto\Validacoes\VerificadorDeDadosExistentes;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class VerificadorDeDadosASeremSalvos
{
    private Erros $controladorDeErros;
    private VerificadorDeDadosExistentes $verificadorDeDadosExistentes;

    const FORM_ERROR_USUARIO_JA_EXISTE_NO_BANCO = 19;
    const FORM_ERROR_EMAIL_JA_EXISTE_NO_BANCO = 20;

    public function __construct(
        Erros $controladorDeErros,
        VerificadorDeDadosExistentes $verificadorDeDadosExistentes
    )
    {
        $this->controladorDeErros = $controladorDeErros;
        $this->verificadorDeDadosExistentes = $verificadorDeDadosExistentes;
    }

    public function verificar(Usuario $usuario) : bool
    {
        $isValid = true;

        $usuarioExisteNoBanco = $this->verificadorDeDadosExistentes->verificaSeOUsuarioExiste($usuario);

        if ($usuarioExisteNoBanco) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_USUARIO_JA_EXISTE_NO_BANCO,
                'Esse usuário já está sendo usado! Escolha outro!'
            );

            $isValid = false;
        }

        $emailExisteNoBanco = $this->verificadorDeDadosExistentes->verificaSeOEmailExiste($usuario);

        if ($emailExisteNoBanco) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_EMAIL_JA_EXISTE_NO_BANCO,
                'Esse e-mail já está cadastrado no nosso sistema, escolha outro!'
            );

            $isValid = false;
        }

        return $isValid;
    }
}
