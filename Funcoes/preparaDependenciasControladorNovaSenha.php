<?php

use SisLogin\Projeto\Conexao\AcoesNoBancoDeDados;
use SisLogin\Projeto\Controladores\ControladorNovaSenha;
use SisLogin\Projeto\Modelo\Erros;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Servicos\RedefinicaoDeSenha;
use SisLogin\Projeto\Validacoes\ValidadorFormulario;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

function preparaDependenciasControladorNovaSenha(
    AcoesNoBancoDeDados $conexaoComOBanco,
    Erros $controladorDeErros,
    Usuario $usuario,
    RedefinicaoDeSenha $redefinicaoDeSenha
) : ControladorNovaSenha
{
    $validadorFormulario = new ValidadorFormulario($controladorDeErros);

    $controladorNovaSenha = new ControladorNovaSenha(
        $usuario,
        $validadorFormulario,
        $conexaoComOBanco,
        $redefinicaoDeSenha
    );

    return $controladorNovaSenha;
}