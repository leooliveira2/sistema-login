<?php

namespace SisLogin\Projeto\Controladores;

use SisLogin\Projeto\Conexao\AcoesNoBancoDeDados;
use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Servicos\RedefinicaoDeSenha;
use SisLogin\Projeto\Validacoes\ValidadorFormulario;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class ControladorNovaSenha
{
    public function __construct(
        private Usuario $usuario,
        private ValidadorFormulario $validadorFormulario,
        private AcoesNoBancoDeDados $conexaoComOBanco,
        private RedefinicaoDeSenha $redefinicaoDeSenha
    )
    {}
    
    public function execucoes(string $nomeDeUsuario) : bool
    {
        $senhaPodeSerSalva = $this->validadorFormulario->validar($this->usuario);

        if ($senhaPodeSerSalva) {
            try {
                $atualizouSenha = $this->redefinicaoDeSenha->atualizarSenhaNoBanco($nomeDeUsuario, $this->usuario);
            } catch (\PDOException | ConexaoException $e) {
                throw new ConexaoException($e->getMessage());
            }

            if ($atualizouSenha) {
                return true;
            }
        }

        return false;
    }
}
