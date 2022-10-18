<?php

use SisLogin\Projeto\Conexao\AcoesNoBancoDeDados;
use SisLogin\Projeto\Controladores\ControladorUsuarioExisteParaRedefinicaoDeSenha;
use SisLogin\Projeto\Modelo\Erros;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Servicos\Sessao;
use SisLogin\Projeto\Servicos\Token;
use SisLogin\Projeto\Validacoes\ValidadorRedefineSenha;
use SisLogin\Projeto\Validacoes\VerificadorDeDadosExistentes;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

function preparaDependenciasControladorUsuarioExisteParaRedefinicaoDeSenha(
    AcoesNoBancoDeDados $conexaoComOBanco,
    Erros $controladorDeErros,
    Usuario $usuario
) : ControladorUsuarioExisteParaRedefinicaoDeSenha
{
    $verificadorDeDadosExistentes = new VerificadorDeDadosExistentes(
        $conexaoComOBanco,
        'usuarios'
    );

    $validadorRedefineSenha = new ValidadorRedefineSenha(
        $controladorDeErros,
        $conexaoComOBanco,
        $verificadorDeDadosExistentes
    );

    $sessao = new Sessao();
    $token = new Token($conexaoComOBanco, 'usuarios', $sessao);

    $controladorRedefineSenha = new ControladorUsuarioExisteParaRedefinicaoDeSenha(
        $conexaoComOBanco,
        $validadorRedefineSenha,
        $usuario,
        $token
    );

    return $controladorRedefineSenha;
}