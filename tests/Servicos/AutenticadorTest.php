<?php

namespace SisLogin\Projeto\Tests\Servicos;

use PHPUnit\Framework\TestCase;
use SisLogin\Projeto\Conexao\AcoesNoBancoDeDados;
use SisLogin\Projeto\Conexao\Conexao;
use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Servicos\Autenticador;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class AutenticadorTest extends TestCase
{
    private static AcoesNoBancoDeDados $conexaoComOBanco;

    public static function setUpBeforeClass(): void
    {
        $con = new Conexao();
        self::$conexaoComOBanco = $con->instanciar('bancoTeste.sqlite');
    }

    public function testTesteEstaAutenticado()
    {
        $this->criaDadosUsuarioParaTeste();

        $autenticador = new Autenticador(self::$conexaoComOBanco, 'usuarios_teste');
        $autenticou = $autenticador->autenticar('1');

        $this->assertTrue($autenticou);
    }

    public function testTesteNaoEstaAutenticado()
    {
        $this->criaDadosUsuarioParaTeste();

        $autenticador = new Autenticador(self::$conexaoComOBanco, 'usuarios_teste');
        $autenticou = $autenticador->autenticar('2');

        $this->assertFalse($autenticou);
    }

    private function criaDadosUsuarioParaTeste() : void
    {
        $usuario = new Usuario(
            usuario: 'usuario2',
            nome: 'nome completo',
            email: 'email2@email.com',
            senha: '123123123',
        );

        $prepare = self::$conexaoComOBanco->prepare(
            'INSERT INTO usuarios_teste (
                usuario, nome, email, senha, token
            ) VALUES (
                :usuario,
                :nome,
                :email,
                :senha,
                :token
            );'
        );

        $prepare->execute([
            ':usuario' => $usuario->getUsuario(),
            ':nome' => $usuario->getNome(),
            ':email' => $usuario->getEmail(),
            ':senha' => $usuario->getSenha(),
            ':token' => '1'
        ]);
    }

    public function testTesteDePreparacaoDoComandoASerExecutadoFalhou()
    {
        $conexaoComOBanco = $this->createMock(AcoesNoBancoDeDados::class);

        $conexaoComOBanco->expects($this->once())->method('prepare')->willReturn(false);

        $this->expectException(ConexaoException::class);
        $this->expectExceptionMessage('OCORREU UM ERRO DE CONEXÃO!');

        $autenticador = new Autenticador($conexaoComOBanco, 'usuarios_teste');
        $autenticador->autenticar('a');
    }

    public function testTesteDaExecucaoDoComandoFalhando()
    {
        $conexaoComOBanco = $this->createMock(AcoesNoBancoDeDados::class);

        $pdoStatement = $this->createMock(\PDOStatement::class);

        $conexaoComOBanco->expects($this->once())->method('prepare')->willReturn($pdoStatement);
        $pdoStatement->expects($this->once())->method('execute')->willReturn(false);

        $this->expectException(ConexaoException::class);
        $this->expectExceptionMessage('OCORREU UM ERRO DE CONEXÃO!');

        $autenticador = new Autenticador($conexaoComOBanco, 'usuarios_teste');
        $autenticador->autenticar('a');
    }

    protected function tearDown(): void
    {
        $con = new Conexao();
        $conexaoComOBanco = $con->instanciar('bancoTeste.sqlite');
        $prepare = $conexaoComOBanco->prepare("DELETE FROM usuarios_teste");
        $prepare->execute();
    }
}
