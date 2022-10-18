<?php

use SisLogin\Projeto\Conexao\Conexao;
use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Log\Log;
use SisLogin\Projeto\Modelo\Erros;
use SisLogin\Projeto\Modelo\Usuario;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once 'Funcoes' . DIRECTORY_SEPARATOR . 'preparaDependenciasControladorLogin.php';

if (isset($argv[1]) && isset($argv[2])) {
    
    // Prepara o programa
    $usuario = new Usuario(
        usuario: $argv[1],
        senha: $argv[2]
    );

    $controladorDeErros = new Erros();

    $log = new Log(new \DateTimeImmutable());

    try {
        $con = new Conexao();
        $conexaoComOBanco = $con->instanciar('banco.sqlite');
    } catch (\PDOException $e) {
        $log->gerarLogDeErro($e->getMessage());
        echo $e->getMessage() . PHP_EOL;
    }
    
    $controlador = preparaDependenciasControladorLogin(
        $usuario,
        $controladorDeErros,
        $conexaoComOBanco,
    );

    // Executa o programa
    try {
        $retornoDoControlador = $controlador->execucoes();
    } catch (ConexaoException $e) {
        $log->gerarLogDeErro($e->getMessage());
        echo $e->getMessage() . PHP_EOL;
    }

    if ($retornoDoControlador) {
        echo "LOGIN REALIZADO COM SUCESSO!" . PHP_EOL;
    } else {
        $erros = $controladorDeErros->getErros();
        
        foreach ($erros as $erro) {
            echo $erro . PHP_EOL;
        }
    }
} else {
    echo "TODOS OS CAMPOS PRECISAM SER PREENCHIDOS!" . PHP_EOL;
}
