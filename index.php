<?php

require_once 'autoload\autoload.php';
require_once 'src\Funcoes\limpaPost.php';

use SisLogin\Projeto\Modelo\Login;

// VERIFICA SE TODOS OS CAMPOS ESTÃO SETADOS

if (isset($_POST['usuario']) && isset($_POST['senha'])) {
    $usuario = limpaPost($_POST['usuario']);
    $senha = limpaPost($_POST['senha']);

    if (empty($usuario) || empty($senha)) {
        $erroGeral = 'Todos os campos precisam ser preenchidos!';
    } else {
        $login = new Login();

        $erroLogin = $login->logar($usuario, $senha);
    } 
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

                <!-- VALIDA O LOGIN -->
                <h2 class='erro'>
                    <?php 
                        if (isset($erroGeral)) {
                            echo $erroGeral;
                        }
                    
                        if (!empty($erroLogin)) {
                            echo $erroLogin;
                        }
                    ?>
                </h2>

                <!-- INSERIR USUÁRIO -->
                <label for="usuario" class="label">Usuário</label>
                <input type="text" name="usuario" placeholder="Informe seu usuário" class="input">

                <!-- INSERIR SENHA -->
                <label for="senha" class="label">Senha</label>
                <input type="password" name="senha" placeholder="Digite sua senha" class="input">

                <!-- ENVIAR OS DADOS DO FORMULÁRIO -->
                <button type="submit" class="button">Entrar</button>

                <h3 class="divisor">ou</h3>

                <a href="criarConta.php" class="ancora">Clique aqui para criar sua conta</a>
            </form>
        </main>
    </body>
</html>
