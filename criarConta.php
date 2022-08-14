<?php

use SisLogin\src\Modelo\Usuario;

require_once 'src\Modelo\Usuario.php';
require_once 'src\limpaPost.php';

if (isset($_POST['usuario']) && isset($_POST['nome']) && isset($_POST['email']) && isset($_POST['senha']) && isset($_POST['repete_senha'])) {
    $usuario = limpaPost($_POST['usuario']);
    $nome = limpaPost($_POST['nome']);
    $email = limpaPost($_POST['email']);
    $senha = limpaPost($_POST['senha']);
    $repete_senha = limpaPost($_POST['repete_senha']);
    if (empty($usuario) || empty($nome) || empty($email) || empty($senha) || empty($repete_senha)) {
        $erroGeral = 'Todos os campos precisam ser preenchidos!';
    } else {
        $usuario = new Usuario(
            $usuario,
            $nome,
            $email,
            $senha
        );

        $usuario->setRepeteSenha($repete_senha);

        $erros = $usuario->validarCadastro();

        if (empty($erros)) {
            $usuario->insert();
            header('location: index.php');
        }
    }
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
            <h1 class="titulo_principal">Criar conta</h1>
            <form method="post" action="" class="formulario">
                <label for="usuario" class="label">Usuário</label>
                <input type="text" name="usuario" placeholder="Insira um nome usuário" required class="input">
                
                <label for="nome" class="label">Nome Completo</label>
                <input type="text" name="nome" placeholder="Informe seu nome completo" required class="input">
                
                <label for="email" class="label">E-mail</label>
                <input type="email" name="email" placeholder="Informe seu e-mail" required class="input">
                
                <label for="senha" class="label">Senha (minímo 8 caracteres)</label>
                <input type="password" name="senha" placeholder="Informe uma senha" required class="input">
                
                <h2 class="erro">
                    <?php
                        if (isset($erros['erro_repeteSenha'])) {
                            echo $erros['erro_repeteSenha'];
                        }
                    ?>
                </h2>

                <label for="repete_senha" class="label">Repita sua senha</label>
                <input type="password" name="repete_senha" placeholder="Repita a senha" required class="input">
                
                <button type="submit" class="button">Criar conta</button>
            </form>
        </main>
    </body>
</html>