<?php

namespace SisLogin\Projeto\Controladores;

use SisLogin\Projeto\Conexao\AcoesNoBancoDeDados;
use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Servicos\Token;
use SisLogin\Projeto\Validacoes\ValidadorRedefineSenha;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class ControladorUsuarioExisteParaRedefinicaoDeSenha
{
    public function __construct(
        private AcoesNoBancoDeDados $conexaoComOBanco,
        private ValidadorRedefineSenha $validadorRedefineSenha,
        private Usuario $usuario,
        private Token $token
    )
    {}

    public function execucoes() : bool
    {
        try {
            $verificouUsuario = $this->validadorRedefineSenha
                ->validar($this->usuario);
        } catch (\PDOException | ConexaoException $e) {
            throw new ConexaoException($e->getMessage());
        }

        if ($verificouUsuario) {
            $tokenFoiSalvo = $this->atualizaToken();

            if ($tokenFoiSalvo) {
                return true;
            }
        }

        return false;
    }

    private function atualizaToken() : bool
    {
        $this->token->gerarToken();

        try {
            $tokenFoiSalvo = $this->token->atualizarTokenNoBanco($this->usuario);
        } catch (\PDOException | ConexaoException $e) {
            throw new ConexaoException($e->getMessage());
        }

        if ($tokenFoiSalvo) {
            return true;
        }

        return false;
    }

    public function getToken() : string
    {
        return $this->token->getToken();
    }
}
