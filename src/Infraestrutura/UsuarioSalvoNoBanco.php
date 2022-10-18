<?php

namespace SisLogin\Projeto\Infraestrutura;

use SisLogin\Projeto\Conexao\AcoesNoBancoDeDados;
use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Modelo\Usuario;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class UsuarioSalvoNoBanco
{
    private AcoesNoBancoDeDados $conexaoComOBanco;
    private string $nomeTabela;

    public function __construct(
        AcoesNoBancoDeDados $conexaoComOBanco, 
        string $nomeTabela,
    )
    {
        $this->conexaoComOBanco = $conexaoComOBanco;
        $this->nomeTabela = $nomeTabela;
    }

    public function salvar(Usuario $usuario) : bool
    {
        $instanciaDoBanco = $this->conexaoComOBanco->prepare(
            "INSERT INTO " . $this->nomeTabela . " (usuario, nome, email, senha)
                VALUES (:usuario, :nome, :email, :senha);"
        );

        if (!$instanciaDoBanco) {
            throw new ConexaoException('OCORREU UM ERRO DE CONEXÃO!');
        }

        $senhaCripto = sha1($usuario->getSenha());

        $comandoExecutado =$instanciaDoBanco->execute([
            ':usuario' => $usuario->getUsuario(),
            ':nome' => $usuario->getNome(),
            ':email' => $usuario->getEmail(),
            ':senha' => $senhaCripto
        ]);

        if (!$comandoExecutado) {
            throw new ConexaoException('OCORREU UM ERRO DE CONEXÃO!');
        }

        return true;
    }
}
