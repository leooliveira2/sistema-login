<?php

require_once 'src\Modelo\Login.php';

use SisLogin\src\Modelo\Login;

if (isset($_POST['usuario']) && $_POST['senha']) {
    $login = new Login($_POST['usuario'], $_POST['senha']);

    define('USUARIO', $_POST['usuario']);

    if ($login->logar()) {
        header('location: bem_vindo.php');
    } else {
        echo "<script>alert('Senha inválida');</script>";
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
            <form method="post" action="" class="formulario">
                <label for="usuario" class="label">Usuário</label>
                <input type="text" name="usuario" placeholder="Informe seu usuário" required class="input">
                <label for="senha" class="label">Senha</label>
                <input type="password" name="senha" placeholder="Digite sua senha" required class="input">
                <button type="submit" class="button">Entrar</button>

                <h3 class="divisor">ou</h3>

                <a href="criarConta.php" class="ancora">Clique aqui para criar sua conta</a>
            </form>
        </main>
    </body>
</html>