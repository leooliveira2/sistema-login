<?php

namespace SisLogin\Projeto\Tests\Controladores;

use PHPUnit\Framework\TestCase;
use SisLogin\Projeto\Conexao\AcoesNoBancoDeDados;
use SisLogin\Projeto\Conexao\Conexao;
use SisLogin\Projeto\Controladores\ControladorUsuarioExisteParaRedefinicaoDeSenha;
use SisLogin\Projeto\Modelo\Erros;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Servicos\Sessao;
use SisLogin\Projeto\Servicos\Token;
use SisLogin\Projeto\Validacoes\ValidadorRedefineSenha;
use SisLogin\Projeto\Validacoes\VerificadorDeDadosExistentes;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class ControladorUsuarioExisteParaRedefinicaoDeSenhaTest extends TestCase
{
    private static AcoesNoBancoDeDados $conexaoComOBanco;

    private AcoesNoBancoDeDados $conexaoComOBancoMock;
    private ValidadorRedefineSenha $validadorRedefineSenha;
    private Usuario $usuario;
    private Token $token;

    public static function setUpBeforeClass(): void
    {
        $con = new Conexao();
        self::$conexaoComOBanco = $con->instanciar('bancoTeste.sqlite'); 
    }

    protected function setUp(): void
    {
        $this->conexaoComOBancoMock = $this->createMock(AcoesNoBancoDeDados::class);
        $this->validadorRedefineSenha = $this->createMock(ValidadorRedefineSenha::class);
        $this->usuario = new Usuario();
        $this->token = $this->createMock(Token::class);

        $this->controladorRedefineSenha = new ControladorUsuarioExisteParaRedefinicaoDeSenha(
            $this->conexaoComOBancoMock,
            $this->validadorRedefineSenha,
            $this->usuario,
            $this->token
        );
    }

    public function testTesteUsuarioFoiEncontradoParaARedefinicaoDeSenhaETokenFoiGerado()
    {
        $this->criaUsuarioParaTestes();

        $controladorDeErros = new Erros();

        $usuario = new Usuario(usuario: 'usuarioTeste');

        $controladorRedefineSenha = $this->criaControladorParaTestes($controladorDeErros, $usuario);
        
        $usuarioExiste = $controladorRedefineSenha->execucoes();

        $this->assertTrue($usuarioExiste);

        $erros = $controladorDeErros->getErros();

        $this->assertArrayNotHasKey(1, $erros);
        $this->assertArrayNotHasKey(22, $erros);

        $tokenGerado = $controladorRedefineSenha->getToken();

        $tokenDoBanco = $this->buscaTokenNoBanco($tokenGerado);

        $this->assertEquals($tokenGerado, $tokenDoBanco);
    }

    public function testTesteUsuarioNaoFoiEncontradoParaARedefinicaoDeSenha()
    {
        $this->criaUsuarioParaTestes();

        $controladorDeErros = new Erros();

        $usuario = new Usuario(usuario: 'oi');

        $controladorRedefineSenha = $this->criaControladorParaTestes($controladorDeErros, $usuario);
        
        $usuarioExiste = $controladorRedefineSenha->execucoes();

        $this->assertFalse($usuarioExiste);

        $erros = $controladorDeErros->getErros();

        $this->assertArrayNotHasKey(1, $erros);
        $this->assertArrayHasKey(22, $erros);
    }

    public function testTesteUsuarioNaoFoiDigitado()
    {
        $this->criaUsuarioParaTestes();

        $controladorDeErros = new Erros();

        $usuario = new Usuario(usuario: '');

        $controladorRedefineSenha = $this->criaControladorParaTestes($controladorDeErros, $usuario);
        
        $usuarioExiste = $controladorRedefineSenha->execucoes();

        $this->assertFalse($usuarioExiste);

        $erros = $controladorDeErros->getErros();

        $this->assertArrayHasKey(1, $erros);
        $this->assertArrayNotHasKey(22, $erros);
    }

    public function testTesteExcecaoELancadaNaProcuraDoUsuario()
    {
        $e = new \PDOException("ERRO DE CONEXÃO");

        $this->validadorRedefineSenha->expects($this->once())->method('validar')->willThrowException($e);
        $this->token->expects($this->never())->method('atualizarTokenNoBanco');

        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage("ERRO DE CONEXÃO");

        $this->controladorRedefineSenha->execucoes();
    }

    public function testTesteExcecaoELancadaNaAtualizacaoDoToken()
    {
        $e = new \PDOException("ERRO DE CONEXÃO");

        $this->validadorRedefineSenha->expects($this->once())->method('validar')->willReturn(true);
        $this->token->expects($this->once())->method('atualizarTokenNoBanco')->willThrowException($e);

        $this->expectException(\PDOException::class);
        $this->expectExceptionMessage("ERRO DE CONEXÃO");

        $this->controladorRedefineSenha->execucoes();
    }

    public function testTesteMockUsuarioNaoEEncontrado()
    {
        $this->validadorRedefineSenha->expects($this->once())->method('validar')->willReturn(false);
        $this->token->expects($this->never())->method('atualizarTokenNoBanco');

        $usuarioFoiEncontrado = $this->controladorRedefineSenha->execucoes();

        $this->assertFalse($usuarioFoiEncontrado);
    }

    public function testTesteMockUsuarioEEncontradoMasTokenNaoECriado()
    {
        $this->validadorRedefineSenha->expects($this->once())->method('validar')->willReturn(true);
        $this->token->expects($this->once())->method('atualizarTokenNoBanco')->willReturn(false);

        $usuarioFoiEncontrado = $this->controladorRedefineSenha->execucoes();

        $this->assertFalse($usuarioFoiEncontrado);
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

    private function criaControladorParaTestes(
        Erros $controladorDeErros, 
        Usuario $usuario
    ) : ControladorUsuarioExisteParaRedefinicaoDeSenha
    {
        $verificadorDeDadosExistentes = new VerificadorDeDadosExistentes(
            self::$conexaoComOBanco,
            'usuarios_teste'
        );

        $validadorRedefineSenha = new ValidadorRedefineSenha(
            $controladorDeErros,
            self::$conexaoComOBanco,
            $verificadorDeDadosExistentes
        );

        $sessao = $this->createMock(Sessao::class);

        $token = new Token(
            self::$conexaoComOBanco,
            'usuarios_teste',
            $sessao
        );

        $controladorRedefineSenha = new ControladorUsuarioExisteParaRedefinicaoDeSenha(
            self::$conexaoComOBanco,
            $validadorRedefineSenha,
            $usuario,
            $token
        );

        return $controladorRedefineSenha;
    }

    private function buscaTokenNoBanco(string $token) : string
    {
        $prepare = self::$conexaoComOBanco->prepare(
            "SELECT token FROM usuarios_teste WHERE token = :token"
        );

        $prepare->execute([
            ':token' => $token
        ]);

        $tokenVindoDoBanco = $prepare->fetchColumn();

        return $tokenVindoDoBanco;
    }

    protected function tearDown(): void
    {
        $prepare = self::$conexaoComOBanco->prepare('DELETE FROM usuarios_teste');
        $prepare->execute();
    }
}
