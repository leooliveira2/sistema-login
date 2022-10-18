<?php

use SisLogin\Projeto\Conexao\AcoesNoBancoDeDados;
use SisLogin\Projeto\Controladores\ControladorLogin;
use SisLogin\Projeto\Modelo\Erros;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Servicos\Login;
use SisLogin\Projeto\Servicos\Sessao;
use SisLogin\Projeto\Servicos\Token;
use SisLogin\Projeto\Validacoes\BuscadorDeLogin;
use SisLogin\Projeto\Validacoes\ValidadorLogin;
use SisLogin\Projeto\Validacoes\VerificadorDeDadosExistentes;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

function preparaDependenciasControladorLogin(
    Usuario $usuario,
    Erros $controladorDeErros,
    AcoesNoBancoDeDados $conexaoComOBanco
) : ControladorLogin
{
    $validadorLogin = new ValidadorLogin($controladorDeErros);

    $verificaSeLoginExiste = new VerificadorDeDadosExistentes($conexaoComOBanco, 'usuarios');
    
    $buscadorDadosLogin =  new BuscadorDeLogin(
        $controladorDeErros,
        $verificaSeLoginExiste
    );

    $sessao = new Sessao();

    $token = new Token($conexaoComOBanco, 'usuarios', $sessao);

    $login = new Login($buscadorDadosLogin, $token);

    $controlador = new ControladorLogin(
        $usuario,
        $validadorLogin,
        $login
    );

    return $controlador;
}
