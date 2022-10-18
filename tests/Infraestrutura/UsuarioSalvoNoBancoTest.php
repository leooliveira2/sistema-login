<?php

namespace SisLogin\Projeto\Tests\Infraestrutura;

use PHPUnit\Framework\TestCase;
use SisLogin\Projeto\Conexao\AcoesNoBancoDeDados;
use SisLogin\Projeto\Conexao\Conexao;
use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Infraestrutura\UsuarioSalvoNoBanco;
use SisLogin\Projeto\Modelo\Usuario;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class UsuarioSalvoNoBancoTest extends TestCase
{
    private static AcoesNoBancoDeDados $conexaoComOBanco;

    public static function setUpBeforeClass(): void
    {
        $con = new Conexao();
        self::$conexaoComOBanco = $con->instanciar('bancoTeste.sqlite');
    }

    public function testTesteUsuarioFoiSalvoNoBanco()
    {
        $usuarioDeveSerSalvoNoBanco = new UsuarioSalvoNoBanco(self::$conexaoComOBanco, "usuarios_teste");

        $usuario = new Usuario(
            'usuario',
            'nome completo',
            'email@email.com',
            '123123123',
            '123123123'
        );

        $usuarioFoiSalvo = $usuarioDeveSerSalvoNoBanco->salvar($usuario);

        $this->assertTrue($usuarioFoiSalvo); 
    }

    public function testTestePreparoDoBancoPraReceberOComandoFalhou()
    {
        $con = $this->createMock(AcoesNoBancoDeDados::class);
        $salvarUsuarioNoBanco = new UsuarioSalvoNoBanco($con, '');

        $con->expects($this->once())
            ->method('prepare')
            ->willReturn(false);

        $this->expectException(ConexaoException::class);
        $this->expectExceptionMessage('OCORREU UM ERRO DE CONEXÃO!');

        $salvou = $salvarUsuarioNoBanco->salvar(new Usuario(senha: ''));
    }

    public function testTesteExecucaoDoComandoFalhou()
    {
        $con = $this->createMock(AcoesNoBancoDeDados::class);
        $salvarUsuarioNoBanco = new UsuarioSalvoNoBanco($con, '');

        $pdoStatement = $this->createMock(\PDOStatement::class);

        $con->expects($this->once())
            ->method('prepare')
            ->willReturn($pdoStatement);

        $pdoStatement->expects($this->once())->method('execute')->willReturn(false);

        $this->expectException(ConexaoException::class);
        $this->expectExceptionMessage('OCORREU UM ERRO DE CONEXÃO!');

        $salvou = $salvarUsuarioNoBanco->salvar(new Usuario(senha: ''));
    }

    protected function tearDown(): void
    {
        $prepare = self::$conexaoComOBanco->prepare("DELETE FROM usuarios_teste");
        $execucao = $prepare->execute();
    }
}
