<?php

use SisLogin\Projeto\Conexao\Conexao;
use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Log\Log;
use SisLogin\Projeto\Modelo\Erros;
use SisLogin\Projeto\Modelo\Usuario;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once 'Funcoes' . DIRECTORY_SEPARATOR . 'preparaDependenciasControladorLogin.php';

if (isset($_POST['usuario']) && isset($_POST['senha'])) { 
   
    // Prepara o programa
    $usuario = new Usuario(
        usuario: $_POST['usuario'],
        senha: $_POST['senha']
    );

    $controladorDeErros = new Erros();

    $log = new Log(new \DateTimeImmutable());

    try {
        $con = new Conexao();
        $conexaoComOBanco = $con->instanciar('banco.sqlite');
    } catch (\PDOException $e) {
        $log->gerarLogDeErro($e->getMessage());
        header('location: erro.html');
    }
    
    $controlador = preparaDependenciasControladorLogin(
        $usuario,
        $controladorDeErros,
        $conexaoComOBanco,
    );

    // Executa o programa
    try {
        $retornoDoControlador = $controlador->execucoes($controladorDeErros);
    } catch (ConexaoException $e) {
        $log->gerarLogDeErro($e->getMessage());
        header('location: erro.html');
    } 

    if ($retornoDoControlador) {
        header('location: bem_vindo.php');
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
        <title>Login</title>
        <link rel="stylesheet" href="css/reset.css">
        <link rel="stylesheet" href="css/styles.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    </head>

    <body>
        <main>
            <h1 class="titulo_principal">Login</h1>
            <form method="post" class="formulario">

                <h2 class='erro'>
                    <?php 
                        if (isset($erros[21])) {
                            echo $erros[21];
                        }
                    ?>
                </h2>

                <!-- INSERIR USUÁRIO -->
                <h2 class='erro'>
                    <?php 
                        if (isset($erros[1])) {
                            echo $erros[1];
                        }
                    ?>
                </h2>

                <label for="usuario" class="label">Usuário</label>
                <input type="text" name="usuario" placeholder="Informe seu usuário" class="input"
                    <?php if (isset($_POST['usuario'])) { echo " value='$_POST[usuario]'"; } ?>>

                <!-- INSERIR SENHA -->
                <h2 class='erro'>
                    <?php 
                        if (isset($erros[9])) {
                            echo $erros[9];
                        }
                    ?>
                </h2>

                <label for="senha" class="label">Senha</label>
                <input type="password" name="senha" placeholder="Digite sua senha" class="input">

                <!-- ENVIAR OS DADOS DO FORMULÁRIO -->
                <button type="submit" class="button">Entrar</button>

                <a href="usuarioExisteParaRedefinicaoDeSenhaWeb.php" class="ancora">Esqueçeu sua senha? Clique aqui para redefini-lá</a>

                <h3 class='divisor'>Ou se ainda não é cadastrado...</h3>
                <a href="criarContaWeb.php" class="ancora">Clique aqui para criar sua conta</a>
            </form>
        </main>
    </body>
</html>
