<?php

namespace SisLogin\Projeto\Tests\Validacoes;

use PHPUnit\Framework\TestCase;
use SisLogin\Projeto\Modelo\{Erros, Usuario};
use SisLogin\Projeto\Validacoes\{
    VerificadorDeDadosASeremSalvos, 
    VerificadorDeDadosExistentes
};

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class VerificadorDeDadosASeremSalvosTest extends TestCase
{    
    protected function setUp(): void
    {
        $this->controladorDeErros = new Erros();
        
        $this->verificadorDadosExistentes = $this->createMock(VerificadorDeDadosExistentes::class);

        $this->verificadorDeDadosASeremSalvos = new VerificadorDeDadosASeremSalvos(
            $this->controladorDeErros,
            $this->verificadorDadosExistentes
        );
    }

    public function testUsuarioExisteEEmailNaoExiste()
    {
        $this->verificadorDadosExistentes
            ->expects($this->once())
            ->method('verificaSeOUsuarioExiste')
            ->willReturn(true);
        
        $this->verificadorDadosExistentes
            ->expects($this->once())
            ->method('verificaSeOEmailExiste')
            ->willReturn(false);

        $usuario = new Usuario(usuario: 'Usuario', email: 'Email');

        $usuarioPodeSerCadastrado = $this->verificadorDeDadosASeremSalvos->verificar($usuario);

        $erros = $this->controladorDeErros->getErros();

        $this->assertArrayHasKey(19, $erros);
        $this->assertArrayNotHasKey(20, $erros);

        $this->assertFalse($usuarioPodeSerCadastrado);
    }

    public function testUsuarioNaoExisteEEmailExiste()
    {
        $this->verificadorDadosExistentes
            ->expects($this->once())
            ->method('verificaSeOUsuarioExiste')
            ->willReturn(false);

        $this->verificadorDadosExistentes
            ->expects($this->once())
            ->method('verificaSeOEmailExiste')
            ->willReturn(true);

        $usuario = new Usuario(usuario: 'Usuario', email: 'Email');
        
        $usuarioPodeSerCadastrado = $this->verificadorDeDadosASeremSalvos->verificar($usuario);

        $erros = $this->controladorDeErros->getErros();

        $this->assertArrayNotHasKey(19, $erros);
        $this->assertArrayHasKey(20, $erros);

        $this->assertFalse($usuarioPodeSerCadastrado);
    }

    public function testUsuarioExisteEEmailExiste()
    {
        $this->verificadorDadosExistentes
            ->expects($this->once())
            ->method('verificaSeOUsuarioExiste')
            ->willReturn(true);

        $this->verificadorDadosExistentes
            ->expects($this->once())
            ->method('verificaSeOEmailExiste')
            ->willReturn(true);

        $usuario = new Usuario(usuario: 'usuario', email: 'email');
        
        $usuarioPodeSerCadastrado = $this->verificadorDeDadosASeremSalvos->verificar($usuario);

        $erros = $this->controladorDeErros->getErros();

        $this->assertArrayHasKey(19, $erros);
        $this->assertArrayHasKey(20, $erros);

        $this->assertFalse($usuarioPodeSerCadastrado);
    }

    public function testUsuarioNaoExisteEEmailNaoExiste()
    {
        $this->verificadorDadosExistentes
            ->expects($this->once())
            ->method('verificaSeOUsuarioExiste')
            ->willReturn(false);

        $this->verificadorDadosExistentes
            ->expects($this->once())
            ->method('verificaSeOEmailExiste')
            ->willReturn(false);

        $usuario = new Usuario(usuario: 'Usuario', email: 'Email');
        
        $usuarioPodeSerCadastrado = $this->verificadorDeDadosASeremSalvos->verificar($usuario);

        $erros = $this->controladorDeErros->getErros();

        $this->assertArrayNotHasKey(19, $erros);
        $this->assertArrayNotHasKey(20, $erros);

        $this->assertTrue($usuarioPodeSerCadastrado);
    }
}
