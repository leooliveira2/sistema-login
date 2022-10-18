<?php

namespace SisLogin\Projeto\Tests\Servicos;

use PHPUnit\Framework\TestCase;
use SisLogin\Projeto\Conexao\AcoesNoBancoDeDados;
use SisLogin\Projeto\Conexao\Conexao;
use SisLogin\Projeto\Modelo\Erros;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Servicos\{Login, Sessao, Token};
use SisLogin\Projeto\Validacoes\BuscadorDeLogin;
use SisLogin\Projeto\Validacoes\VerificadorDeDadosExistentes;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class LoginTest extends TestCase
{
    private static AcoesNoBancoDeDados $conexaoComOBanco;

    public static function setUpBeforeClass(): void
    {
        $con = new Conexao();
        self::$conexaoComOBanco = $con->instanciar('bancoTeste.sqlite');
    }

    protected function setUp(): void
    {
        $this->buscadorDeDadosLogin = $this->createMock(BuscadorDeLogin::class);
        $this->token = $this->createMock(Token::class);

        $this->login = new Login($this->buscadorDeDadosLogin, $this->token);
    }

    public function testTesteLoginDaCerto()
    {
        $usuario = $this->criaUsuarioParaTestes();

        $login = $this->criaLoginParaTestes();

        $loginFeitoComSucesso = $login->logar($usuario);

        $this->assertTrue($loginFeitoComSucesso);
    }

    public function testTesteLoginDaErrado()
    {
        $usuario = new Usuario(usuario: 'oi', senha: 'oi');

        $login = $this->criaLoginParaTestes();

        $loginFeitoComSucesso = $login->logar($usuario);

        $this->assertFalse($loginFeitoComSucesso);
    }

    private function criaUsuarioParaTestes() : Usuario
    {
        $usuario = new Usuario(
            usuario: 'usuario2',
            nome: 'nome completo',
            email: 'email2@email.com',
            senha: '123123123'
        );

        $prepare = self::$conexaoComOBanco->prepare(
            'INSERT INTO usuarios_teste (
                usuario, nome, email, senha
            ) VALUES (
                :usuario,
                :nome,
                :email,
                :senha
            );'
        );

        $prepare->execute([
            ':usuario' => $usuario->getUsuario(),
            ':nome' => $usuario->getNome(),
            ':email' => $usuario->getEmail(),
            ':senha' => sha1($usuario->getSenha()),
        ]);

        return $usuario;
    }

    private function criaLoginParaTestes() : Login
    {
        $controladorDeErros = new Erros();

        $verificaSeLoginExiste = new VerificadorDeDadosExistentes(self::$conexaoComOBanco, 'usuarios_teste');
        
        $buscadorDadosLogin =  new BuscadorDeLogin(
            $controladorDeErros,
            $verificaSeLoginExiste
        );

        $sessao = $this->createMock(Sessao::class);

        $token = new Token(self::$conexaoComOBanco, 'usuarios_teste', $sessao);

        $login = new Login($buscadorDadosLogin, $token);

        return $login;
    }

    public function testDadosNecessariosParaLoginSaoEncontradosNoBancoETokenECriado()
    {
        $this->buscadorDeDadosLogin->expects($this->once())->method('buscar')->willReturn(true);
        $this->token->expects($this->once())->method('gerarToken');
        $this->token->expects($this->once())->method('atualizarTokenNoBanco')->willReturn(true);

        $usuario = new Usuario();

        $loginFeitoComSucesso = $this->login->logar($usuario);

        $this->assertTrue($loginFeitoComSucesso);
    }

    public function testDadosNecessariosParaLoginNaoSaoEncontradosNoBancoETokenNaoECriado()
    {
        $this->buscadorDeDadosLogin->expects($this->once())->method('buscar')->willReturn(false);
        $this->token->expects($this->never())->method('gerarToken');
        $this->token->expects($this->never())->method('atualizarTokenNoBanco');

        $usuario = new Usuario();

        $loginFeitoComSucesso = $this->login->logar($usuario);

        $this->assertFalse($loginFeitoComSucesso);
    }

    protected function tearDown(): void
    {
        $prepare = self::$conexaoComOBanco->prepare("DELETE FROM usuarios_teste");
        $prepare->execute();
    }
}
