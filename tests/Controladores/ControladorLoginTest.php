<?php

namespace SisLogin\Projeto\Tests\Controladores;

use PHPUnit\Framework\TestCase;
use SisLogin\Projeto\Conexao\AcoesNoBancoDeDados;
use SisLogin\Projeto\Conexao\Conexao;
use SisLogin\Projeto\Controladores\ControladorLogin;
use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Modelo\Erros;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Servicos\Login;
use SisLogin\Projeto\Servicos\Sessao;
use SisLogin\Projeto\Servicos\Token;
use SisLogin\Projeto\Validacoes\BuscadorDeLogin;
use SisLogin\Projeto\Validacoes\ValidadorLogin;
use SisLogin\Projeto\Validacoes\VerificadorDeDadosExistentes;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class ControladorLoginTest extends TestCase
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
        $this->validadorLogin = $this->createMock(ValidadorLogin::class);
        $this->login = $this->createMock(Login::class);

        $this->controlador = new ControladorLogin(
            $this->usuario,
            $this->validadorLogin,
            $this->login
        );
    }

    public function testTesteLoginERealizadoComSucesso ()
    {   
        $this->criaUsuarioParaTestes();

        $usuario = new Usuario(usuario: 'usuarioTeste', senha: '123123123');
        $controlador = $this->criaControladorParaLogin($usuario);

        $loginFoiRealizado = $controlador->execucoes();

        $this->assertTrue($loginFoiRealizado);
    }

    public function testTesteLoginNaoERealizado()
    {   
        $this->criaUsuarioParaTestes();

        $usuario = new Usuario(usuario: 'user', senha: '123');
        $controlador = $this->criaControladorParaLogin($usuario);

        $loginFoiRealizado = $controlador->execucoes();

        $this->assertFalse($loginFoiRealizado);
    }

    private function criaUsuarioParaTestes() : void
    {
        $prepare = self::$conexaoComOBanco->prepare(
            "INSERT INTO usuarios_teste (
                usuario,
                nome,
                email,
                senha
            ) VALUES (
                :usuario,
                :nome,
                :email,
                :senha
              );"
        );

        $prepare->execute([
            ':usuario' => 'usuarioTeste',
            ':nome' => 'nomeTeste',
            ':email' => 'email@email.com',
            'senha' => sha1('123123123')
        ]);
    }

    private function criaControladorParaLogin(Usuario $usuario) : ControladorLogin
    {
        $controladorDeErros = new Erros();

        $validadorLogin = new ValidadorLogin($controladorDeErros);

        $verificaSeLoginExiste = new VerificadorDeDadosExistentes(self::$conexaoComOBanco, 'usuarios_teste');
        
        $buscadorDadosLogin =  new BuscadorDeLogin(
            $controladorDeErros,
            $verificaSeLoginExiste
        );

        $sessao = $this->createMock(Sessao::class);

        $token = new Token(self::$conexaoComOBanco, 'usuarios_teste', $sessao);

        $login = new Login($buscadorDadosLogin, $token);

        $controlador = new ControladorLogin(
            $usuario,
            $validadorLogin,
            $login
        );

        return $controlador;
    }

    public function testTesteValidacoesDoLoginFalham()
    {
        $this->validadorLogin->expects($this->once())->method('validar')->willReturn(false);
        $this->login->expects($this->never())->method('logar');

        $loginFoiRealizado = $this->controlador->execucoes();

        $this->assertFalse($loginFoiRealizado);
    }

    public function testTesteLoginFalha()
    {
        $this->validadorLogin->expects($this->once())->method('validar')->willReturn(true);
        $this->login->expects($this->once())->method('logar')->willReturn(false);

        $loginFoiRealizado = $this->controlador->execucoes();

        $this->assertFalse($loginFoiRealizado);
    }

    public function testTesteLoginDaCerto()
    {
        $this->validadorLogin->expects($this->once())->method('validar')->willReturn(true);
        $this->login->expects($this->once())->method('logar')->willReturn(true);

        $loginFoiRealizado = $this->controlador->execucoes();

        $this->assertTrue($loginFoiRealizado);
    }

    public function testExcecaoDoLoginELancada()
    {
        $e = new ConexaoException();

        $this->validadorLogin->expects($this->once())->method('validar')->willReturn(true);
        $this->login->expects($this->once())->method('logar')->willThrowException($e);

        $this->expectException(ConexaoException::class);

        $this->controlador->execucoes();
    }

    protected function tearDown(): void
    {
        $prepare = self::$conexaoComOBanco->prepare('DELETE FROM usuarios_teste');
        $prepare->execute();
    }
}
