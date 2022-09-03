<?php

use SisLogin\Projeto\Modelo\Usuario;

require_once 'autoload\autoload.php';
require_once 'src\Funcoes\limpaPost.php';

// VERIFICA SE TODOS OS CAMPOS ESTÃO SETADOS
if (
    isset($_POST['usuario']) && 
    isset($_POST['nome']) && 
    isset($_POST['email']) && 
    isset($_POST['senha']) && 
    isset($_POST['repete_senha'])) {
    
    $usuario = limpaPost($_POST['usuario']);
    $nome = limpaPost($_POST['nome']);
    $email = limpaPost($_POST['email']);
    $senha = limpaPost($_POST['senha']);
    $repeteSenha = limpaPost($_POST['repete_senha']);

    // VERIFICA SE TODOS OS CAMPOS NÃO ESTÃO VAZIOS
    if (empty($usuario) || empty($nome) || empty($email) || empty($senha) || empty($repeteSenha)) {
        $erroGeral = 'Todos os campos precisam ser preenchidos!';
    } else {

        // CRIA UM NOVO OBJETO DO TIPO USUÁRIO
        $usuario = new Usuario(
            $usuario,
            $nome,
            $email,
            $senha,
            $repeteSenha
        );

        // VALIDA OS DADOS DO USUÁRIO
        $erros = $usuario->validarCadastro();

        // SE NÃO TIVER NENHUM ERRO, SALVA O USUÁRIO NO BANCO E DEPOIS REDIRECIONA PRA PÁGINA DE LOGIN
        if (empty($erros)) {
            $usuario->salvarUsuarioNoBanco();
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
            <h1 class="titulo_principal <?php if (!empty($erros)) { echo 'margem_titulo'; }?>">Criar conta</h1>
            <form method="post" class="formulario">

                <!-- VERIFICAR SE TODOS OS CAMPOS ESTÃO PREENCHIDOS -->
                <h2 class='erro'>
                    <?php if (isset($erroGeral)) {echo $erroGeral;} ?>
                </h2>

                <!-- VALIDAR USUÁRIO -->
                <h2 class='erro'>
                    <?php if (isset($erros['erro_usuario'])) {
                                echo $erros['erro_usuario'];
                            } ?>
                </h2>

                <!-- INSERIR USUÁRIO -->
                <label for="usuario" class="label">Usuário</label>
                <input type="text" name="usuario" placeholder="Insira um nome usuário" class="input">

                <!-- VALIDAR NOME -->
                <h2 class='erro'>
                    <?php if (isset($erros['erro_nome'])) {
                                echo $erros['erro_nome'];
                            } ?>
                </h2>

                <!-- INSERIR NOME -->
                <label for="nome" class="label">Nome Completo</label>
                <input type="text" name="nome" placeholder="Informe seu nome completo" class="input">

                <!-- VALIDAR EMAIL -->
                <h2 class='erro'>
                    <?php if (isset($erros['erro_email'])) {
                                echo $erros['erro_email'];
                            } ?>
                </h2>

                <!-- INSERIR EMAIL -->
                <label for="email" class="label">E-mail</label>
                <input type="email" name="email" placeholder="Informe seu e-mail" class="input">

                <!-- VALIDAR SENHA -->
                <h2 class="erro">
                    <?php if (isset($erros['erro_senha'])) {
                                echo $erros['erro_senha'];
                            } ?>
                </h2>

                <!-- INSERIR SENHA -->
                <label for="senha" class="label">Senha (minímo 8 caracteres)</label>
                <input type="password" name="senha" placeholder="Informe uma senha" class="input">

                <!-- VALIDAR REPETIÇÃO DA SENHA -->
                <h2 class="erro">
                    <?php if (isset($erros['erro_repeteSenha'])) {
                                echo $erros['erro_repeteSenha'];
                            } ?>
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
