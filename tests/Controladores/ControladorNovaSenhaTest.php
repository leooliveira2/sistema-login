<?php

namespace SisLogin\Projeto\Tests\Controladores;

use PDOException;
use PHPUnit\Framework\TestCase;
use SisLogin\Projeto\Conexao\AcoesNoBancoDeDados;
use SisLogin\Projeto\Conexao\Conexao;
use SisLogin\Projeto\Controladores\ControladorNovaSenha;
use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Modelo\Erros;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Servicos\RedefinicaoDeSenha;
use SisLogin\Projeto\Validacoes\ValidadorFormulario;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class ControladorNovaSenhaTest extends TestCase
{
    private static AcoesNoBancoDeDados $conexaoComOBanco;

    private ValidadorFormulario $validadorFormulario;
    private RedefinicaoDeSenha $redefinicaoDeSenha;
    private ControladorNovaSenha $controladorNovaSenha;

    public static function setUpBeforeClass(): void
    {
        $con = new Conexao();
        self::$conexaoComOBanco = $con->instanciar('bancoTeste.sqlite'); 
    }

    protected function setUp(): void
    {
        $usuario = new Usuario(senha: 'oi');
        $this->validadorFormulario = $this->createMock(ValidadorFormulario::class);
        $this->redefinicaoDeSenha = $this->createMock(RedefinicaoDeSenha::class);

        $this->controladorNovaSenha = new ControladorNovaSenha(
            $usuario,
            $this->validadorFormulario,
            self::$conexaoComOBanco,
            $this->redefinicaoDeSenha
        );
    }

    public function testTesteSenhaEAtualizadaComSucesso()
    {
        $this->criaUsuarioParaTestes();

        $controladorDeErros = new Erros();

        $usuario = new Usuario(senha: 'senhateste', repeteSenha: 'senhateste');

        $controladorNovaSenha = $this->criaControladorParaTestes($controladorDeErros, $usuario);

        $senhaFoiSalva = $controladorNovaSenha->execucoes('usuarioTeste');

        $this->assertTrue($senhaFoiSalva);

        $erros = $controladorDeErros->getErros();

        $this->assertArrayNotHasKey(12, $erros);
        $this->assertArrayNotHasKey(13, $erros);
        $this->assertArrayNotHasKey(14, $erros);
        $this->assertArrayNotHasKey(15, $erros);
        $this->assertArrayNotHasKey(16, $erros);
        $this->assertArrayNotHasKey(17, $erros);
        $this->assertArrayNotHasKey(18, $erros);

        $senhaDoBanco = $this->buscaSenhaNoBanco();

        $this->assertEquals(sha1('senhateste'), $senhaDoBanco);
    }

    public function testTesteSenhaNaoEAtualizadaComSucesso()
    {
        $this->criaUsuarioParaTestes();

        $controladorDeErros = new Erros();

        $usuario = new Usuario(senha: 'senha123', repeteSenha: 'oi');

        $controladorNovaSenha = $this->criaControladorParaTestes($controladorDeErros, $usuario);

        $senhaFoiSalva = $controladorNovaSenha->execucoes('usuarioTeste');

        $this->assertFalse($senhaFoiSalva);

        $erros = $controladorDeErros->getErros();

        $this->assertArrayHasKey(17, $erros); // aqui eu só verifiquei essa validacao, pois usei o mesmo validador do formulario, que ja esta testado
    }

    public function testTestaSeAExcecaoDeAtualizarASenhaNoBancoELancada()
    {
        $e = new ConexaoException();

        $this->validadorFormulario->expects($this->once())->method('validar')->wilLReturn(true);
        $this->redefinicaoDeSenha->expects($this->once())->method('atualizarSenhaNoBanco')->wilLThrowException($e);

        $this->expectException(ConexaoException::class);

        $this->controladorNovaSenha->execucoes('teste');
    }

    public function testTesteValidacaoDosDadosRetornaFalse()
    {
        $this->validadorFormulario->expects($this->once())->method('validar')->wilLReturn(false);

        $salvouNovaSenha = $this->controladorNovaSenha->execucoes('teste');

        $this->assertFalse($salvouNovaSenha);
    }

    public function testTesteValidacaoDosDadosRetornaTrueMasSenhaNaoEAtualizada()
    {
        $this->validadorFormulario->expects($this->once())->method('validar')->wilLReturn(true);
        $this->redefinicaoDeSenha->expects($this->once())->method('atualizarSenhaNoBanco')->willReturn(false);

        $salvouNovaSenha = $this->controladorNovaSenha->execucoes('teste');

        $this->assertFalse($salvouNovaSenha);
    }

    public function testTesteValidacaoDosDadosRetornaTrueESenhaEAAtualizada()
    {
        $this->validadorFormulario->expects($this->once())->method('validar')->wilLReturn(true);
        $this->redefinicaoDeSenha->expects($this->once())->method('atualizarSenhaNoBanco')->willReturn(true);

        $salvouNovaSenha = $this->controladorNovaSenha->execucoes('teste');

        $this->assertTrue($salvouNovaSenha);
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

    private function criaControladorParaTestes(
        Erros $controladorDeErros,
        Usuario $usuario
    ) : ControladorNovaSenha
    {
        $validadorFormulario = new ValidadorFormulario($controladorDeErros);

        $redefinicaoDeSenha = new RedefinicaoDeSenha(self::$conexaoComOBanco, 'usuarios_teste');

        $controladorNovaSenha = new ControladorNovaSenha(
            $usuario,
            $validadorFormulario,
            self::$conexaoComOBanco,
            $redefinicaoDeSenha
        );

        return $controladorNovaSenha;
    }

    protected function tearDown(): void
    {
        $prepare = self::$conexaoComOBanco->prepare('DELETE FROM usuarios_teste');
        $prepare->execute();
    }
}
