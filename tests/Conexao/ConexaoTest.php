<?php

namespace SisLogin\Projeto\Tests\Conexao;

use PHPUnit\Framework\TestCase;
use SisLogin\Projeto\Conexao\Conexao;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class ConexaoTest extends TestCase
{
    public function testTesteConexaoFalhando()
    {
        $conexaoMock = $this->createMock(Conexao::class);

        $e = new \PDOException('Falha na conexão com o banco!');

        $conexaoMock->expects($this->once())->method('instanciar')->willThrowException($e);

        $this->expectException(\PDOException::class);
        
        $conexaoMock->instanciar('bancoTeste.sqlite');
    }

    public function testTesteConexaoDandoCerto()
    {
        $con = new Conexao();
        $conexaoComOBanco = $con->instanciar('bancoTeste.sqlite');

        $this->assertIsObject($conexaoComOBanco);
    }
}
