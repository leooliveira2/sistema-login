<?php

use SisLogin\Projeto\Conexao\Conexao;
use SisLogin\Projeto\Modelo\{Erros, Usuario};
use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Log\Log;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once 'Funcoes' . DIRECTORY_SEPARATOR . 'preparaDependenciasControladorCriarConta.php';

if (
    isset($_POST['usuario']) && 
    isset($_POST['nome']) && 
    isset($_POST['email']) && 
    isset($_POST['senha']) && 
    isset($_POST['repete_senha'])
) 
{
    // Preparação do programa
    $usuario = new Usuario(
        $_POST['usuario'],
        $_POST['nome'],
        $_POST['email'],
        $_POST['senha'],
        $_POST['repete_senha']
    );

    
    $log = new Log(new \DateTimeImmutable());

    try {
        $con = new Conexao();
        $conexaoComOBanco = $con->instanciar('banco.sqlite');
    } catch (\PDOException $e) {
        $log->gerarLogDeErro($e->getMessage());
        header('location: erro.html');
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
        header('location: erro.html');
    }

    if ($retornoDoControlador) {
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
        <title>Crie sua conta</title>
        <link rel="stylesheet" href="css/reset.css">
        <link rel="stylesheet" href="css/styles.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    </head>

    <body>
        <main>
            <h1 class="titulo_principal <?php if (!empty($erros)) { echo 'margem_titulo'; }?>">Criar conta</h1>

            <form method="post" class="formulario <?php if (isset($erros)) {
                                if (count($erros) >= 1) { echo "margem_formulario"; } 
                            }?>">

                <!-- VALIDAR USUÁRIO -->
                <h2 class='erro'>
                    <?php 
                        if (isset($erros[1])) {
                            echo $erros[1];
                        } else if (isset($erros[2])) {
                            echo $erros[2];
                        } else if (isset($erros[3])) {
                            echo $erros[3];
                        } else if (isset($erros[4])) {
                            echo $erros[4];
                        } else if (isset($erros[19])) {
                            echo $erros[19];
                        }
                    ?>
                </h2>

                <!-- INSERIR USUÁRIO -->
                <label for="usuario" class="label">Usuário (Mínimo 5 caracteres)</label>
                <input type="text" name="usuario" placeholder="Insira um nome usuário" class="input"
                    <?php if (isset($_POST['usuario'])) { echo " value='$_POST[usuario]'"; } ?>>

                <!-- VALIDAR NOME -->
                <h2 class='erro'>
                    <?php 
                        if (isset($erros[5])) {
                            echo $erros[5];
                        } else if (isset($erros[6])) {
                            echo $erros[6];
                        } else if (isset($erros[7])) {
                            echo $erros[7];
                        } else if (isset($erros[8])) {
                            echo $erros[8];
                        }
                    ?>
                </h2>

                <!-- INSERIR NOME -->
                <label for="nome" class="label">Nome Completo</label>
                <input type="text" name="nome" placeholder="Informe seu nome completo" class="input"
                    <?php if (isset($_POST['nome'])) { echo " value='$_POST[nome]'"; } ?>>

                <!-- VALIDAR EMAIL -->
                <h2 class='erro'>
                    <?php 
                        if (isset($erros[9])) {
                            echo $erros[9];
                        } else if (isset($erros[10])) {
                            echo $erros[10];
                        } else if (isset($erros[11])) {
                            echo $erros[11];
                        } else if (isset($erros[20])) {
                            echo $erros[20];
                        }
                        
                    ?>
                </h2>

                <!-- INSERIR EMAIL -->
                <label for="email" class="label">E-mail</label>
                <input type="email" name="email" placeholder="Informe seu e-mail" class="input"
                    <?php if (isset($_POST['email'])) { echo " value='$_POST[email]'"; } ?>>

                <!-- VALIDAR SENHA -->
                <h2 class="erro">
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

                <!-- INSERIR SENHA -->
                <label for="senha" class="label">Senha (minímo 8 caracteres)</label>
                <input type="password" name="senha" placeholder="Informe uma senha" class="input">

                <!-- VALIDAR REPETIÇÃO DA SENHA -->
                <h2 class="erro">
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

                <!-- INSERIR REPETIÇÃO DE SENHA -->
                <label for="repete_senha" class="label">Repita sua senha</label>
                <input type="password" name="repete_senha" placeholder="Repita a senha" class="input">

                <!-- ENVIAR OS DADOS DO FORMULÁRIO -->
                <button type="submit" class="button">Criar conta</button>

                <a href='index.php' class='ancora ancora_criaConta'>Ir para a página de login</a>
            </form>
        </main>
    </body>
</html>
