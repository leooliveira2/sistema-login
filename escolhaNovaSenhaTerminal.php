<?php

use SisLogin\Projeto\Conexao\Conexao;
use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Log\Log;
use SisLogin\Projeto\Modelo\Erros;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Servicos\RedefinicaoDeSenha;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once 'Funcoes' . DIRECTORY_SEPARATOR . 'preparaDependenciasControladorUsuarioExisteParaRedefinicaoDeSenha.php';
require_once 'Funcoes' . DIRECTORY_SEPARATOR . 'preparaDependenciasControladorNovaSenha.php';

if (isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {

    $log = new Log(new \DateTimeImmutable());
    
    try {
        $con = new Conexao();
        $conexaoComOBanco = $con->instanciar('banco.sqlite');
    } catch (ConexaoException $e) {
        $log->gerarLogDeErro($e->getMessage());
        echo $e->getMessage() . PHP_EOL;
    }

    $controladorDeErros = new Erros();
    $usernameDoUsuario = new Usuario(usuario: $argv[1]);

    $controladorRedefineSenha = preparaDependenciasControladorUsuarioExisteParaRedefinicaoDeSenha(
        $conexaoComOBanco,
        $controladorDeErros,
        $usernameDoUsuario
    );

    try {
        $usuarioExiste = $controladorRedefineSenha->execucoes();
    } catch (ConexaoException $e) {
        $log->gerarLogDeErro($e->getMessage());
        echo $e->getMessage() . PHP_EOL;
    }

    if ($usuarioExiste) {
        
        $controladorDeErrosNovaSenha = new Erros();

        $senhaERepeteSenhaDoUsuario = new Usuario(
            senha: $argv[2],
            repeteSenha: $argv[3]
        );

        $redefinicaoDeSenha = new RedefinicaoDeSenha(
            $conexaoComOBanco,
            'usuarios'
        );

        $controladorNovaSenha = preparaDependenciasControladorNovaSenha(
            $conexaoComOBanco,
            $controladorDeErrosNovaSenha,
            $senhaERepeteSenhaDoUsuario,
            $redefinicaoDeSenha
        );

        try {
            $senhaFoiAtualizada = $controladorNovaSenha->execucoes($usernameDoUsuario->getUsuario());
        } catch (ConexaoException $e) {
            $log->gerarLogDeErro($e->getMessage());
            echo $e->getMessage() . PHP_EOL;
        }

        if ($senhaFoiAtualizada) {
            echo "A senha do usuario: {$usernameDoUsuario->getUsuario()} foi atualizada com sucesso!" . PHP_EOL;
        } else {
            $errosNovaSenha = $controladorDeErrosNovaSenha->getErros();

            foreach ($errosNovaSenha as $erroNovaSenha) {
                echo $erroNovaSenha . PHP_EOL;
            }
        }

    } else {
        $erros = $controladorDeErros->getErros();
        
        foreach ($erros as $erro) {
            echo $erro . PHP_EOL;
        }
    }

} else {
    echo 'POR FAVOR, PREENCHA OS TRÊS CAMPOS NECESSÁRIOS' . PHP_EOL;
}
