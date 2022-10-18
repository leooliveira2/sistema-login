<?php

namespace SisLogin\Projeto\Tests\Servicos;

use PHPUnit\Framework\TestCase;
use SisLogin\Projeto\Conexao\AcoesNoBancoDeDados;
use SisLogin\Projeto\Conexao\Conexao;
use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Servicos\RedefinicaoDeSenha;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class RedefinicaoDeSenhaTest extends TestCase
{
    private static AcoesNoBancoDeDados $conexaoComOBanco;

    public static function setUpBeforeClass(): void
    {
        $con = new Conexao();
        self::$conexaoComOBanco = $con->instanciar('bancoTeste.sqlite'); 
    }

    public function testTesteSenhaFoiAtualizadaComSucesso()
    {
        $this->criaUsuarioParaTestes();

        $redefinicaoDeSenha = new RedefinicaoDeSenha(
            self::$conexaoComOBanco,
            'usuarios_teste'
        );

        $senhaUsuario = new Usuario(senha: '123123123');

        $senhaFoiAtualizada = $redefinicaoDeSenha->atualizarSenhaNoBanco(
            'usuarioTeste',
            $senhaUsuario
        );

        $this->assertTrue($senhaFoiAtualizada);

        $senhaDoBanco = $this->buscaSenhaNoBanco();

        $this->assertEquals(sha1($senhaUsuario->getSenha()), $senhaDoBanco);
    }

    public function testTesteExcecaoDoPreparoDoBancoFoiLancada()
    {
        $conexaoComOBancoMock = $this->createMock(AcoesNoBancoDeDados::class);

        $redefinicaoDeSenha = new RedefinicaoDeSenha($conexaoComOBancoMock, 'usuarios_teste');

        $conexaoComOBancoMock->expects($this->once())->method('prepare')->willReturn(false);
        
        $this->expectException(ConexaoException::class);
        $this->expectExceptionMessage('OCORREU UM ERRO DE CONEXÃO!');

        $redefinicaoDeSenha->atualizarSenhaNoBanco('', new Usuario());
    }

    public function testTesteExcecaoDaExecucaoDoComandoDoBancoFoiLancada()
    {
        $conexaoComOBancoMock = $this->createMock(AcoesNoBancoDeDados::class);

        $redefinicaoDeSenha = new RedefinicaoDeSenha($conexaoComOBancoMock, 'usuarios_teste');

        $pdoStatement = $this->createMock(\PDOStatement::class);

        $conexaoComOBancoMock->expects($this->once())->method('prepare')->willReturn($pdoStatement);
        $pdoStatement->expects($this->once())->method('execute')->willReturn(false);
        
        $this->expectException(ConexaoException::class);
        $this->expectExceptionMessage('OCORREU UM ERRO DE CONEXÃO!');

        $redefinicaoDeSenha->atualizarSenhaNoBanco('', new Usuario(senha: '123'));
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

    private function buscaSenhaNoBanco() : string
    {
        $prepare = self::$conexaoComOBanco->prepare(
            'SELECT senha FROM usuarios_teste WHERE usuario = :usuario'
        );

        $prepare->execute([
            ':usuario' => 'usuarioTeste',
        ]);

        $senha = $prepare->fetchColumn();

        return $senha;
    }

    protected function tearDown(): void
    {
        $prepare = self::$conexaoComOBanco->prepare('DELETE FROM usuarios_teste');
        $prepare->execute();
    }
}
