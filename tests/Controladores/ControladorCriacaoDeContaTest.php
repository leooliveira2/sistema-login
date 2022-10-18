<?php

namespace SisLogin\Projeto\Tests\Controladores;

use PHPUnit\Framework\TestCase;
use SisLogin\Projeto\Conexao\AcoesNoBancoDeDados;
use SisLogin\Projeto\Conexao\Conexao;
use SisLogin\Projeto\Controladores\ControladorCriacaoDeConta;
use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Infraestrutura\UsuarioSalvoNoBanco;
use SisLogin\Projeto\Modelo\Erros;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Validacoes\Validador;
use SisLogin\Projeto\Validacoes\ValidadorFormulario;
use SisLogin\Projeto\Validacoes\VerificadorDeDadosASeremSalvos;
use SisLogin\Projeto\Validacoes\VerificadorDeDadosExistentes;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class ControladorCriacaoDeContaTest extends TestCase
{
    private static AcoesNoBancoDeDados $conexaoComOBanco;

    public static function setUpBeforeClass(): void
    {
        $con = new Conexao();
        self::$conexaoComOBanco = $con->instanciar('bancoTeste.sqlite');
    }

    protected function setUp(): void
    {
        $this->usuario = new Usuario();
        $this->validador = $this->createMock(Validador::class);
        $this->usuarioSalvoNoBanco = $this->createMock(UsuarioSalvoNoBanco::class);

        $this->controlador = new ControladorCriacaoDeConta(
            $this->usuario,
            $this->validador,
            $this->usuarioSalvoNoBanco
        );
    }

    public function testContaECriadaComSucesso()
    {
        $usuario = new Usuario(
            usuario: 'userTeste',
            nome: 'testeUser',
            email: 'email@email.teste',
            senha: '123123123',
            repeteSenha: '123123123'
        );

        $controlador = $this->criaControladorParaTestes($usuario);

        $cadastrouUsuario = $controlador->execucoes();

        $this->assertTrue($cadastrouUsuario);
    }

    public function testContaNaoECriada()
    {
        $usuario = new Usuario(
            usuario: 'userTeste',
            nome: 'testeUser',
            email: 'email@email.teste',
            senha: '123123123',
            repeteSenha: '12312312'
        );

        $controlador = $this->criaControladorParaTestes($usuario);

        $cadastrouUsuario = $controlador->execucoes();

        $this->assertFalse($cadastrouUsuario);
    }

    private function criaControladorParaTestes(Usuario $usuario) : ControladorCriacaoDeConta
    {
        $controladorDeErros = new Erros();

        $validadorFormulario = new ValidadorFormulario($controladorDeErros);

        $verificadorDeDadosExistentes = new VerificadorDeDadosExistentes(
            self::$conexaoComOBanco, 'usuarios_teste'
        );

        $verificadorDeDadosASeremSalvos = new VerificadorDeDadosASeremSalvos(
            $controladorDeErros,
            $verificadorDeDadosExistentes
        );

        $validador = new Validador(
            $validadorFormulario,
            $verificadorDeDadosASeremSalvos
        );

        $usuarioSalvoNoBanco = new UsuarioSalvoNoBanco(
            self::$conexaoComOBanco,
            'usuarios_teste'
        );

        $controlador = new ControladorCriacaoDeConta(
            $usuario,
            $validador,
            $usuarioSalvoNoBanco
        );

        return $controlador;
    }

    public function testTesteUsuarioNaoPassaPelasVerificacoesNecessariasParaSerSalvo()
    {
        $this->validador->expects($this->once())->method('validar')->willReturn(false);
        $this->usuarioSalvoNoBanco->expects($this->never())->method('salvar');

        $usuarioFoiSalvo = $this->controlador->execucoes();

        $this->assertFalse($usuarioFoiSalvo);
    }

    public function testTesteFalhaEmSalvarUsuarioNoBanco()
    {
        $this->validador->expects($this->once())->method('validar')->willReturn(true);
        $this->usuarioSalvoNoBanco->expects($this->once())->method('salvar')->willReturn(false);

        $usuarioFoiSalvo = $this->controlador->execucoes();

        $this->assertFalse($usuarioFoiSalvo);
    }

    public function testTesteSucessoSalvarUsuarioNoBanco()
    {
        $this->validador->expects($this->once())->method('validar')->willReturn(true);
        $this->usuarioSalvoNoBanco->expects($this->once())->method('salvar')->willReturn(true);

        $usuarioFoiSalvo = $this->controlador->execucoes();

        $this->assertTrue($usuarioFoiSalvo);
    }

    public function testTesteExcecaoDaValidacaoDeUsuarioELancadaCorretamente()
    {
        $e = new ConexaoException();

        $this->validador->expects($this->once())->method('validar')->willThrowException($e);
        $this->usuarioSalvoNoBanco->expects($this->never())->method('salvar');

        $this->expectException(ConexaoException::class);

        $this->controlador->execucoes();
    }

    public function testTesteExcecaoDeSalvarUsuarioELancadaCorretamente()
    {
        $e = new ConexaoException();

        $this->validador->expects($this->once())->method('validar')->willReturn(true);
        $this->usuarioSalvoNoBanco->expects($this->once())->method('salvar')->willThrowException($e);

        $this->expectException(ConexaoException::class);

        $this->controlador->execucoes();
    }


    protected function tearDown(): void
    {
        $prepare = self::$conexaoComOBanco->prepare('DELETE FROM usuarios_teste');
        $prepare->execute();
    }
}
