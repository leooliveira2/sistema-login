<?php

use SisLogin\Projeto\Conexao\AcoesNoBancoDeDados;
use SisLogin\Projeto\Controladores\ControladorCriacaoDeConta;
use SisLogin\Projeto\Infraestrutura\UsuarioSalvoNoBanco;
use SisLogin\Projeto\Modelo\Erros;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Validacoes\Validador;
use SisLogin\Projeto\Validacoes\ValidadorFormulario;
use SisLogin\Projeto\Validacoes\VerificadorDeDadosASeremSalvos;
use SisLogin\Projeto\Validacoes\VerificadorDeDadosExistentes;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

function preparaDependenciasControladorCriarConta(
    Usuario $usuario,
    AcoesNoBancoDeDados $conexaoComOBanco,
    Erros $controladorDeErros
) : ControladorCriacaoDeConta
{
    $validadorFormulario = new ValidadorFormulario($controladorDeErros);
    $verificadorDeDadosExistentes = new VerificadorDeDadosExistentes($conexaoComOBanco, 'usuarios');
    $verificaSeDadosPodemSerSalvos = new VerificadorDeDadosASeremSalvos(
        $controladorDeErros, 
        $verificadorDeDadosExistentes
    );
    
    $validador = new Validador(
        $validadorFormulario, 
        $verificaSeDadosPodemSerSalvos,
    );

    $usuarioDeveSerSalvo = new UsuarioSalvoNoBanco($conexaoComOBanco, 'usuarios');

    $controlador = new ControladorCriacaoDeConta(
        $usuario,
        $validador,
        $usuarioDeveSerSalvo
    );

    return $controlador;
}
