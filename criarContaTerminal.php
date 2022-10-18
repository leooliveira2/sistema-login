<?php

use SisLogin\Projeto\Conexao\Conexao;
use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Log\Log;
use SisLogin\Projeto\Modelo\Erros;
use SisLogin\Projeto\Modelo\Usuario;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once 'Funcoes' . DIRECTORY_SEPARATOR . 'preparaDependenciasControladorCriarConta.php';

if (
    isset($argv[1]) &&
    isset($argv[2]) &&
    isset($argv[3]) &&
    isset($argv[4]) &&
    isset($argv[5])
) 
{
    // Preparação do programa
    $usuario = new Usuario(
        $argv[1],
        $argv[2],
        $argv[3],
        $argv[4],
        $argv[5]
    );

    $log = new Log(new \DateTimeImmutable());

    try {
        $con = new Conexao();
        $conexaoComOBanco = $con->instanciar('banco.sqlite');
    } catch (\PDOException $e) {
        $log->gerarLogDeErro($e->getMessage());
        echo $e->getMessage() . PHP_EOL;
    }

    $controladorDeErros = new Erros();

    $controlador = preparaDependenciasControladorCriarConta(
        $usuario,
        $conexaoComOBanco,
        $controladorDeErros
    );

    // Execução do programa
    try {
        $retornoDoControlador = $controlador->execucoes($controladorDeErros);
    } catch (ConexaoException $e) {
        $log->gerarLogDeErro($e->getMessage());
        echo $e->getMessage() . PHP_EOL;
    }

    if ($retornoDoControlador) {
        echo "Conta criada com sucesso!" . PHP_EOL;
    } else {
        $erros = $controladorDeErros->getErros();
        foreach ($erros as $erro) {
            echo $erro . PHP_EOL;
        }
    }
} else {
    echo "TODOS OS CAMPOS PRECISAM SER PREENCHIDOS!" . PHP_EOL;
}
