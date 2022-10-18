<?php

namespace SisLogin\Projeto\Validacoes;

use SisLogin\Projeto\Conexao\AcoesNoBancoDeDados;
use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Modelo\Usuario;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class VerificadorDeDadosExistentes
{
    private AcoesNoBancoDeDados $conexaoComOBanco;
    private string $nomeTabela;

    public function __construct(AcoesNoBancoDeDados $conexaoComOBanco, string $nomeTabela)
    {
        $this->conexaoComOBanco = $conexaoComOBanco;
        $this->nomeTabela = $nomeTabela;
    }

    public function verificaSeOUsuarioExiste(Usuario $usuario) : bool
    {
        $instanciaDoBanco = $this->conexaoComOBanco->prepare(
            "SELECT usuario FROM " . $this->nomeTabela . " WHERE usuario = :usuario"
        );

        if (!$instanciaDoBanco) {
            throw new ConexaoException('OCORREU UM ERRO DE CONEXÃO!');
        }

        $comandoExecutado = $instanciaDoBanco->execute([
            ':usuario' => $usuario->getUsuario()
        ]);

        if (!$comandoExecutado) {
            throw new ConexaoException('OCORREU UM ERRO DE CONEXÃO!');
        }

        $usuarioVindoDoBanco = $instanciaDoBanco->fetchColumn();

        if (strlen($usuarioVindoDoBanco) >= 1) {
            return true;
        }

        return false;
    }

    public function verificaSeOEmailExiste(Usuario $usuario) : bool
    {
        $instanciaDoBanco = $this->conexaoComOBanco->prepare(
            "SELECT email FROM " . $this->nomeTabela . " WHERE email = :email"
        );

        if (!$instanciaDoBanco) {
            throw new ConexaoException('OCORREU UM ERRO DE CONEXÃO!');
        }

        $comandoExecutado = $instanciaDoBanco->execute([
            ':email' => $usuario->getEmail()
        ]);

        if (!$comandoExecutado) {
            throw new ConexaoException('OCORREU UM ERRO DE CONEXÃO!');
        }

        $emailVindoDoBanco = $instanciaDoBanco->fetchColumn();

        if (strlen($emailVindoDoBanco) >= 1) {
            return true;
        }
        
        return false;
    }

    public function verificaSeUsuarioESenhaEstaoCadastrados(Usuario $usuario) : bool
    {
        $instanciaDoBanco = $this->conexaoComOBanco->prepare(
            "SELECT usuario FROM " . $this->nomeTabela . " WHERE usuario = :usuario AND senha = :senha;"
        );

        if (!$instanciaDoBanco) {
            throw new ConexaoException('OCORREU UM ERRO DE CONEXÃO!');
        }

        $comandoExecutado = $instanciaDoBanco->execute([
            ':usuario' => $usuario->getUsuario(),
            ':senha' => sha1($usuario->getSenha())
        ]);

        if (!$comandoExecutado) {
            throw new ConexaoException('OCORREU UM ERRO DE CONEXÃO!');
        }

        $usuarioESenhaVindosDoBanco = $instanciaDoBanco->fetchColumn();

        if (strlen($usuarioESenhaVindosDoBanco) >= 1) {
            return true;
        }

        return false;
    }
}
