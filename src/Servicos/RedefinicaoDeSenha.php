<?php

namespace SisLogin\Projeto\Servicos;

use SisLogin\Projeto\Conexao\AcoesNoBancoDeDados;
use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Modelo\Usuario;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class RedefinicaoDeSenha
{
    public function __construct(
        private AcoesNoBancoDeDados $conexaoComOBanco,
        private string $nomeTabela
    )
    {}

    public function atualizarSenhaNoBanco(string $nomeDeUsuario, Usuario $senhaDoUsuario) : bool
    {
        $instanciaDoBanco = $this->conexaoComOBanco->prepare(
            "UPDATE " . $this->nomeTabela . " SET senha = :senha WHERE usuario = :usuario"
        );

        if (!$instanciaDoBanco) {
            throw new ConexaoException('OCORREU UM ERRO DE CONEXÃO!');
        }

        $comandoExecutado = $instanciaDoBanco->execute([
            ':senha' => sha1($senhaDoUsuario->getSenha()),
            ':usuario' => $nomeDeUsuario
        ]);

        if (!$comandoExecutado) {
            throw new ConexaoException('OCORREU UM ERRO DE CONEXÃO!');
        }

        return true;
    }
}
