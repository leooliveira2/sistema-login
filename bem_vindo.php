<?php

session_start();

use SisLogin\Projeto\Conexao\Conexao;
use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Log\Log;
use SisLogin\Projeto\Servicos\Autenticador;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

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
    echo $e->getMessage();
}

if (!$autenticando) {
    $log->gerarLogDeErro('Erro de autenticacão');
    header('location: erro.html');
}

if (isset($_POST['botao'])) {
    session_destroy();
    header('location: index.php');
}

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Bem-vindo <?php echo $autenticador->getUsuario(); ?></title>
        <link rel='stylesheet' href='css\reset.css'>
        <link rel='stylesheet' href='css\bem_vindo_styles.css'>
    </head>

    <body>
        <header class='cabecalho'>
            <h1 class='titulo_cabecalho'>Usuario: <?php echo $autenticador->getUsuario(); ?> </h1>
            <a href='index.php' class='ancora_cabecalho'>Voltar para página inicial</a>

            <form method='post'>
                <button name='botao' type='submit' class='botao_sair'>Sair</button>
            </form>
        </header>
    </body>
</html>
