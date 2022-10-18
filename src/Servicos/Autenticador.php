<?php

namespace SisLogin\Projeto\Servicos;

use SisLogin\Projeto\Conexao\AcoesNoBancoDeDados;
use SisLogin\Projeto\Excecoes\ConexaoException;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class Autenticador
{
    private AcoesNoBancoDeDados $conexaoComOBanco;
    private string $usuario;
    private string $nomeTabela;

    public function __construct(AcoesNoBancoDeDados $conexaoComOBanco, string $nomeTabela)
    {
        $this->conexaoComOBanco = $conexaoComOBanco;
        $this->nomeTabela = $nomeTabela;
    }

    public function autenticar(string $token) : bool
    {
        $instanciaDoBanco = $this->conexaoComOBanco->prepare(
            "SELECT usuario FROM " . $this->nomeTabela . " WHERE token = :token"
        );

        if (!$instanciaDoBanco) {
            throw new ConexaoException('OCORREU UM ERRO DE CONEXÃO!');
        }
            
        $comandoExecutado = $instanciaDoBanco
            ->execute([
                ':token' => $token
            ]);

        if (!$comandoExecutado) {
            throw new ConexaoException('OCORREU UM ERRO DE CONEXÃO!');    
        }
        
        $usuario = $instanciaDoBanco->fetchColumn();

        if (strlen($usuario) >= 1) {
            $this->usuario = $usuario;

            return true;
        }
        
        return false;
    }

    public function getUsuario() : string
    {
        return $this->usuario;
    }
}
