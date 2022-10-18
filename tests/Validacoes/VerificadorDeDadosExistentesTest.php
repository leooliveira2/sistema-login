<?php

namespace SisLogin\Projeto\Tests\Validacoes;

use PHPUnit\Framework\TestCase;
use SisLogin\Projeto\Conexao\AcoesNoBancoDeDados;
use SisLogin\Projeto\Conexao\Conexao;
use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Validacoes\VerificadorDeDadosExistentes;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class VerificadorDeDadosExistentesTest extends TestCase
{
    private static AcoesNoBancoDeDados $conexaoComOBanco;

    public static function setUpBeforeClass(): void
    {
        $con = new Conexao();
        self::$conexaoComOBanco = $con->instanciar('bancoTeste.sqlite');
    }

    public function testTesteUsuarioExisteNoBanco()
    {
        $this->criaUsuarioParaTeste();

        $verificadorDeDadosExistentes = new VerificadorDeDadosExistentes(self::$conexaoComOBanco, 'usuarios_teste');

        $usuarioExiste = $verificadorDeDadosExistentes->verificaSeOUsuarioExiste(
            new Usuario(usuario: 'usuario1')
        );

        $this->assertTrue($usuarioExiste);
    }

    public function testTesteUsuarioNaoExisteNoBanco()
    {
        $this->criaUsuarioParaTeste();

        $verificadorDeDadosExistentes = new VerificadorDeDadosExistentes(self::$conexaoComOBanco, 'usuarios_teste');

        $usuarioExiste = $verificadorDeDadosExistentes->verificaSeOUsuarioExiste(
            new Usuario(usuario: 'a')
        );

        $this->assertFalse($usuarioExiste);
    }

    private function criaUsuarioParaTeste() : void
    {
        $usuario = new Usuario(
            usuario: 'usuario1',
            nome: 'nome completo',
            email: 'email1@email.com',
            senha: sha1('123123123')
        );

        $prepare = self::$conexaoComOBanco->prepare(
            'INSERT INTO usuarios_teste (usuario, nome, email, senha) VALUES 
            (
                :usuario, :nome, :email, :senha
            );'
        );

        $prepare->execute([
            ':usuario' => $usuario->getUsuario(),
            ':nome' => $usuario->getNome(),
            ':email' => $usuario->getEmail(),
            ':senha' => $usuario->getSenha()
        ]);
    }

    public function testTestePreparoDoBancoPraReceberOComandoFalhou()
    {
        $con = $this->createMock(AcoesNoBancoDeDados::class);
        $verificadorDeDadosExistentes = new VerificadorDeDadosExistentes($con, 'usuarios_teste');

        $con->expects($this->once())->method('prepare')->willReturn(false);

        $this->expectException(ConexaoException::class);
        $this->expectExceptionMessage('OCORREU UM ERRO DE CONEXÃO!');

        $usuario = new Usuario();
        $verificadorDeDadosExistentes->verificaSeOUsuarioExiste($usuario);
    }

    public function testTesteExecucaoDoComandoFalhou()
    {
        $con = $this->createMock(AcoesNoBancoDeDados::class);
        $verificadorDeDadosExistentes = new VerificadorDeDadosExistentes($con, 'usuarios_teste');

        $pdoStatement = $this->createMock(\PDOStatement::class);
        $con->expects($this->once())->method('prepare')->willReturn($pdoStatement);
        $pdoStatement->expects($this->once())->method('execute')->wilLReturn(false);

        $this->expectException(ConexaoException::class);
        $this->expectExceptionMessage('OCORREU UM ERRO DE CONEXÃO!');

        $usuario = new Usuario();
        $verificadorDeDadosExistentes->verificaSeOUsuarioExiste($usuario);
    }

    public function testTesteEmailExisteNoBanco()
    {
        $this->criaUsuarioParaTeste();

        $verificadorDeDadosExistentes = new VerificadorDeDadosExistentes(self::$conexaoComOBanco, 'usuarios_teste');

        $usuarioExiste = $verificadorDeDadosExistentes->verificaSeOEmailExiste(
            new Usuario(email: 'email1@email.com')
        );

        $this->assertTrue($usuarioExiste);
    }

    public function testTesteEmailNaoExisteNoBanco()
    {
        $verificadorDeDadosExistentes = new VerificadorDeDadosExistentes(self::$conexaoComOBanco, 'usuarios_teste');

        $usuarioExiste = $verificadorDeDadosExistentes->verificaSeOEmailExiste(
            new Usuario(email: 'a')
        );

        $this->assertFalse($usuarioExiste);
    }

    public function testTesteUsuarioESenhaEstaoCadastrados()
    {
        $this->criaUsuarioParaTeste();

        $verificadorDeDadosExistentes = new VerificadorDeDadosExistentes(self::$conexaoComOBanco, 'usuarios_teste');

        $usuarioESenhaEstaoCadastrados = $verificadorDeDadosExistentes->verificaSeUsuarioESenhaEstaoCadastrados(
            new Usuario(usuario: 'usuario1', senha: '123123123')
        );

        $this->assertTrue($usuarioESenhaEstaoCadastrados);
    }

    public function testTesteUsuarioESenhaNaoEstaoCadastrados()
    {
        $this->criaUsuarioParaTeste();

        $verificadorDeDadosExistentes = new VerificadorDeDadosExistentes(self::$conexaoComOBanco, 'usuarios_teste');

        $usuarioESenhaEstaoCadastrados = $verificadorDeDadosExistentes->verificaSeUsuarioESenhaEstaoCadastrados(
            new Usuario(usuario: 'a', senha: 'a')
        );

        $this->assertFalse($usuarioESenhaEstaoCadastrados);
    }

    protected function tearDown(): void
    {
        $prepare = self::$conexaoComOBanco->prepare('DELETE FROM usuarios_teste');
        $prepare->execute();
    }
}
