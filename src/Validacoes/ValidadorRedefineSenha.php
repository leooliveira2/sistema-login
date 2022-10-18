<?php

namespace SisLogin\Projeto\Validacoes;

use SisLogin\Projeto\Conexao\AcoesNoBancoDeDados;
use SisLogin\Projeto\Modelo\Erros;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Validacoes\VerificadorDeDadosExistentes;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class ValidadorRedefineSenha
{   
    const FORM_ERROR_USUARIO_VAZIO = 1;
    const FORM_ERROR_USUARIO_NAO_ESTA_CADASTRADO_NO_BANCO = 22;

    public function __construct(
        private Erros $controladorDeErros,
        private AcoesNoBancoDeDados $conexaoComOBanco,
        private VerificadorDeDadosExistentes $verificadorDeDadosExistentes
    )
    {}

    public function validar(Usuario $usuario) : bool
    {
        $isValid = true;

        $usuarioExisteNoBanco = $this->verificadorDeDadosExistentes
                ->verificaSeOUsuarioExiste($usuario);

        if (!is_null($usuario->getUsuario()) && empty($usuario->getUsuario())) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_USUARIO_VAZIO,
                'Preencha o campo usuário!'
            );
    
            $isValid = false;
        } else if (!is_null($usuario->getUsuario()) && !$usuarioExisteNoBanco) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_USUARIO_NAO_ESTA_CADASTRADO_NO_BANCO,
                'Usuário não encontrado'
            );

            $isValid = false;
        }
        
        return $isValid;
    }
}
