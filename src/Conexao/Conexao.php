<?php

namespace SisLogin\Projeto\Conexao;

use SisLogin\Projeto\Conexao\AcoesNoBancoDeDados;

require_once 'vendor' . DIRECTORY_SEPARATOR .'autoload.php';

class Conexao
{
    private AcoesNoBancoDeDados $conexao;

    public function instanciar(string $nomeDoBanco) : AcoesNoBancoDeDados
    {
        if (!isset($this->conexao)) {
            $caminhoDoArquivo = __DIR__ . 
                                DIRECTORY_SEPARATOR . '..' . 
                                DIRECTORY_SEPARATOR . '..' . 
                                DIRECTORY_SEPARATOR . 'DB' . 
                                DIRECTORY_SEPARATOR . $nomeDoBanco;
                                
            $this->conexao = new AcoesNoBancoDeDados('sqlite:' . $caminhoDoArquivo);
            $this->conexao->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->conexao->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        }

        return $this->conexao;
    }
}
