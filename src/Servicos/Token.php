<?php

namespace SisLogin\Projeto\Servicos;

use SisLogin\Projeto\Conexao\AcoesNoBancoDeDados;
use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Servicos\Sessao;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class Token
{
    private string $token;
    private AcoesNoBancoDeDados $conexaoComOBanco;
    private string $nomeTabela;
    private Sessao $sessao;

    public function __construct(
        AcoesNoBancoDeDados $conexaoComOBanco, 
        string $nomeTabela,
        Sessao $sessao
    )
    {
        $this->conexaoComOBanco = $conexaoComOBanco;
        $this->nomeTabela = $nomeTabela;
        $this->sessao = $sessao;
    }

    public function gerarToken() : void
    {
        $this->sessao->iniciarSessao();
        
        $this->token = sha1(uniqid().date('d-m-Y-H-i-s'));
        
        $_SESSION['TOKEN'] = $this->token;
    }

    public function atualizarTokenNoBanco(Usuario $usuario) : bool
    {
        $instanciaDoBanco = $this->conexaoComOBanco->prepare(
            "UPDATE " . $this->nomeTabela . " SET token = :token WHERE usuario = :usuario"
        );

        if (!$instanciaDoBanco) {
            throw new ConexaoException('OCORREU UM ERRO DE CONEXÃO!');
        }

        $comandoExecutado = $instanciaDoBanco->execute([
            ':token' => $this->token,
            ':usuario' => $usuario->getUsuario()
        ]);

        if (!$comandoExecutado) {
            throw new ConexaoException('OCORREU UM ERRO DE CONEXÃO!');
        }

        return true;
    }

    public function getToken() : string
    {
        return $this->token;
    }
}
