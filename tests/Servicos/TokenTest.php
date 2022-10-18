<?php

namespace SisLogin\Projeto\Tests\Servicos;

use PHPUnit\Framework\TestCase;
use SisLogin\Projeto\Conexao\{AcoesNoBancoDeDados, Conexao};
use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Servicos\{Sessao, Token};

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class TokenTest extends TestCase
{
    private static AcoesNoBancoDeDados $conexaoComOBanco;

    public static function setUpBeforeClass(): void
    {
        $con = new Conexao();
        self::$conexaoComOBanco = $con->instanciar('bancoTeste.sqlite');
    }

    protected function setUp(): void
    {
        $this->sessao = $this->createMock(Sessao::class);
        $this->conexaoComOBancoMock = $this->createMock(AcoesNoBancoDeDados::class);

        $this->token = new Token($this->conexaoComOBancoMock, 'usuarios_teste', $this->sessao);
    }

    public function testTesteTokenECriadoComSucesso()
    {
        $usuario = $this->criaUsuarioParaTestes();

        $sessao = $this->createMock(Sessao::class);

        $token = new Token(self::$conexaoComOBanco, 'usuarios_teste', $sessao);
        $gerouToken = $token->gerarToken();
        $atualizouTokenNoBanco = $token->atualizarTokenNoBanco($usuario);

        $this->assertTrue($atualizouTokenNoBanco);

        $tokenVindoDoBanco = $this->buscaTokenNoBanco($token->getToken());

        $this->assertEquals($token->getToken(), $tokenVindoDoBanco);
    }

    public function testTestePreparacaoParaAtualizarTokenNoBancoFalha()
    {
        $gerouToken = $this->token->gerarToken();

        $this->conexaoComOBancoMock->expects($this->once())->method('prepare')->willReturn(false);

        $this->expectException(ConexaoException::class);
        $this->expectExceptionMessage('OCORREU UM ERRO DE CONEXÃO!');

        $usuario = $this->createMock(Usuario::class);

        $atualizouTokenNoBanco = $this->token->atualizarTokenNoBanco($usuario);

        $this->assertFalse($atualizouTokenNoBanco);
    }

    public function testTesteExecucaoDoComandoParaAtualizarTokenNoBancoFalha()
    {
        $gerouToken = $this->token->gerarToken();

        $pdoStatement = $this->createMock(\PDOStatement::class);

        $this->conexaoComOBancoMock->expects($this->once())->method('prepare')->willReturn($pdoStatement);
        $pdoStatement->expects($this->once())->method('execute')->willReturn(false);

        $this->expectException(ConexaoException::class);
        $this->expectExceptionMessage('OCORREU UM ERRO DE CONEXÃO!');

        $usuario = $this->createMock(Usuario::class);

        $atualizouTokenNoBanco = $this->token->atualizarTokenNoBanco($usuario);

        $this->assertFalse($atualizouTokenNoBanco);
    }

    private function criaUsuarioParaTestes() : Usuario
    {
        $con = new Conexao();
        $conexaoComOBanco = $con->instanciar('bancoTeste.sqlite');

        $usuario = new Usuario(
            usuario: 'usuario3',
            nome: 'nome completo',
            email: 'email3@email.com',
            senha: '123123123'
        );

        $prepare = $conexaoComOBanco->prepare(
            'INSERT INTO usuarios_teste (
                usuario, nome, email, senha
            ) VALUES (
                :usuario, :nome, :email, :senha
            );'
        );

        $prepare->execute([
            ':usuario' => $usuario->getUsuario(),
            ':nome' => $usuario->getNome(),
            ':email' => $usuario->getEmail(),
            ':senha' => $usuario->getSenha()
        ]);

        return $usuario;
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
        $prepare = self::$conexaoComOBanco->prepare("DELETE FROM usuarios_teste");
        $prepare->execute();
    }
}
