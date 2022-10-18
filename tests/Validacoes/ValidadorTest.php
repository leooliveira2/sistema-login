<?php

namespace SisLogin\Projeto\Tests\Validacoes;

use PHPUnit\Framework\TestCase;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Validacoes\{Validador, ValidadorFormulario, VerificadorDeDadosASeremSalvos};

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class ValidadorTest extends TestCase
{
    protected function setUp(): void
    {
        $this->validadorFormulario = $this->createMock(ValidadorFormulario::class);
        $this->verificadorDeDadosASeremSalvos = $this->createMock(
            VerificadorDeDadosASeremSalvos::class
        );

        $this->validador = new Validador(
            $this->validadorFormulario, 
            $this->verificadorDeDadosASeremSalvos,
        );

        $this->usuario = $this->createMock(Usuario::class);
    }
    
    public function testTesteValidadorFormularioEVerificadorDeDadosASeremSalvosAmbosRetornandoFalse()
    {
        $this->validadorFormulario->expects($this->once())
            ->method('validar')
            ->willReturn(false);

        $this->verificadorDeDadosASeremSalvos->expects($this->once())
            ->method('verificar')
            ->willReturn(false);
        
        $podeSerSalvoNoBanco = $this->validador->validar($this->usuario);

        $this->assertFalse($podeSerSalvoNoBanco);
    }

    public function testTesteValidadorFormularioEVerificadorDeDadosASeremSalvosAmbosRetornandoTrue()
    {
        $this->validadorFormulario->expects($this->once())
            ->method('validar')->willReturn(true);

        $this->verificadorDeDadosASeremSalvos->expects($this->once())
            ->method('verificar')->willReturn(true);
        
        $podeSerSalvoNoBanco = $this->validador->validar($this->usuario);

        $this->assertTrue($podeSerSalvoNoBanco);
    }

    public function testTesteValidadorFormularioRetornandoTrue_E_VerificadorDeDadosASeremSalvosRetornandoFalse()
    {
        $this->validadorFormulario->expects($this->once())
            ->method('validar')
            ->willReturn(true);

        $this->verificadorDeDadosASeremSalvos->expects($this->once())
            ->method('verificar')
            ->willReturn(false);
        
        $podeSerSalvoNoBanco = $this->validador->validar($this->usuario);

        $this->assertFalse($podeSerSalvoNoBanco);
    }

    public function testTesteValidadorFormularioRetornandoFalse_E_VerificadorDeDadosASeremSalvosRetornandoTrue()
    {
        $this->validadorFormulario->expects($this->once())
            ->method('validar')
            ->willReturn(false);

        $this->verificadorDeDadosASeremSalvos->expects($this->once())
            ->method('verificar')
            ->willReturn(true);
        
        $podeSerSalvoNoBanco = $this->validador->validar($this->usuario);

        $this->assertFalse($podeSerSalvoNoBanco);
    }
}
