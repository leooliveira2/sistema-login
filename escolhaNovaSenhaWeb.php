<?php

session_start();

use SisLogin\Projeto\Conexao\Conexao;
use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Log\Log;
use SisLogin\Projeto\Modelo\Erros;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Servicos\Autenticador;
use SisLogin\Projeto\Servicos\RedefinicaoDeSenha;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once 'Funcoes' . DIRECTORY_SEPARATOR . 'preparaDependenciasControladorNovaSenha.php';

$log = new Log(new \DateTimeImmutable());

if (!isset($_SESSION['TOKEN'])) {
    $log->gerarLogDeErro('Erro na criação do token');
    header('location: erro.html');
}

try {
    $con = new Conexao();
    $conexaoComOBanco = $con->instanciar('banco.sqlite');
} catch (\PDOException $e) {
    $log->gerarLogDeErro($e->getMessage());
    header('location: index.html');
}

$autenticador = new Autenticador($conexaoComOBanco, 'usuarios');

try {
    $autenticando = $autenticador->autenticar($_SESSION['TOKEN']);
} catch (\PDOException | ConexaoException $e) {
    $log->gerarLogDeErro($e->getMessage());
    header('location: erro.html');
}

if (!$autenticando) {
    $log->gerarLogDeErro('Erro de autenticação');
    header('location: erro.html');
}

// ----------------------------

if (isset($_POST['senha']) && isset($_POST['repeteSenha'])) {

    try {
        $con = new Conexao();
        $conexaoComOBanco = $con->instanciar('banco.sqlite');
    } catch (\PDOException $e) {
        $log->gerarLogDeErro($e->getMessage());
    }

    $controladorDeErros = new Erros();
    
    $usuario = new Usuario(
        senha: $_POST['senha'],
        repeteSenha: $_POST['repeteSenha']
    );

    $redefinicaoDeSenha = new RedefinicaoDeSenha(
        $conexaoComOBanco,
        'usuarios'
    );

    $controladorNovaSenha = preparaDependenciasControladorNovaSenha(
        $conexaoComOBanco,
        $controladorDeErros,
        $usuario,
        $redefinicaoDeSenha
    );

    try {
        $salvouNovaSenha = $controladorNovaSenha->execucoes(
            $autenticador->getUsuario()
        );
    } catch (ConexaoException $e) {
        $log->gerarLogDeErro($e->getMessage());
    }

    if ($salvouNovaSenha) {
        session_destroy();
        header('location: index.php');
    }

    $erros = $controladorDeErros->getErros();
}

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Escolha sua nova senha</title>
        <link rel="stylesheet" href="css/reset.css">
        <link rel="stylesheet" href="css/styles.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    </head>

    <body>
    <main>
            <h1 class="titulo_principal titulo_rec_senha">Trocar Senha</h1>
            <form method="post" class="formulario">
                <h2 class='erro'>
                    <?php
                        if (isset($erros[12])) {
                            echo $erros[12];
                        } else if (isset($erros[13])) {
                            echo $erros[13];
                        } else if (isset($erros[14])) {
                            echo $erros[14];
                        }
                    ?>
                </h2>
                <label for="senha" class="label">Informe sua nova senha</label>
                <input type="password" name="senha" placeholder="Digite aqui" class="input">

                <h2 class='erro'>
                    <?php
                        if (isset($erros[15])) {
                            echo $erros[15];
                        } else if (isset($erros[16])) {
                            echo $erros[16];
                        } else if (isset($erros[17])) {
                            echo $erros[17];
                        } else if (isset($erros[18])) {
                            echo $erros[18];
                        }
                    ?>
                </h2>
                <label for="repeteSenha" class="label">Informe a senha novamente</label>
                <input type="password" name="repeteSenha" placeholder="Digite aqui" class="input">
                
                <!-- ENVIAR OS DADOS DO FORMULÁRIO -->
                <button type="submit" class="button">Continuar</button>
            </form>
        </main>
    </body>
</html>
