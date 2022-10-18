<?php

use SisLogin\Projeto\Conexao\Conexao;
use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Log\Log;
use SisLogin\Projeto\Modelo\Erros;
use SisLogin\Projeto\Modelo\Usuario;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once 'Funcoes' . DIRECTORY_SEPARATOR . 'preparaDependenciasControladorUsuarioExisteParaRedefinicaoDeSenha.php';

if (isset($_POST['usuario'])) {

    $log = new Log(new \DateTimeImmutable());
    
    try {
        $con = new Conexao();
        $conexaoComOBanco = $con->instanciar('banco.sqlite');
    } catch (\PDOException $e) {
        $log->gerarLogDeErro($e->getMessage());
        header('location: erro.html');
    }

    $controladorDeErros = new Erros();
    $usuario = new Usuario(usuario: $_POST['usuario']);

    $controladorRedefineSenha = preparaDependenciasControladorUsuarioExisteParaRedefinicaoDeSenha(
        $conexaoComOBanco,
        $controladorDeErros,
        $usuario
    );

    try {
        $usuarioFoiEncontrado = $controladorRedefineSenha->execucoes();
    } catch (ConexaoException $e) {
        $log->gerarLogDeErro($e->getMessage());
        header('location: erro.html');
    }

    if ($usuarioFoiEncontrado) {
        header('location: escolhaNovaSenhaWeb.php');
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
        <title>Redefinir senha</title>
        <link rel="stylesheet" href="css/reset.css">
        <link rel="stylesheet" href="css/styles.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    </head>

    <body>
    <main>
            <h1 class="titulo_principal titulo_rec_senha">Recuperação de senha</h1>
            <form method="post" class="formulario">
                <h2 class='erro'>
                    <?php
                        if (isset($erros[1])) {
                            echo $erros[1];
                        } else if (isset($erros[22])) {
                            echo $erros[22];
                        }
                    ?>
                </h2>
                <label for="usuario" class="label">Informe seu usuário</label>
                <input type="text" name="usuario" placeholder="exemplo: usuario123" class="input">
                
                <!-- ENVIAR OS DADOS DO FORMULÁRIO -->
                <button type="submit" class="button">Continuar</button>
            </form>
        </main>
    </body>
</html>
