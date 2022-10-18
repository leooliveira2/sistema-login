<?php

namespace SisLogin\Projeto\Tests\Validacoes;

use PHPUnit\Framework\TestCase;
use SisLogin\Projeto\Modelo\Erros;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Validacoes\BuscadorDeLogin;
use SisLogin\Projeto\Validacoes\VerificadorDeDadosExistentes;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class BuscadorDeLoginTest extends TestCase
{
    protected function setUp(): void
    {
        $this->controladorDeErros = new Erros();
        $this->verificadorDeDadosExistentes = $this->createMock(
            VerificadorDeDadosExistentes::class
        );
        $this->buscadorLogin = new BuscadorDeLogin(
            $this->controladorDeErros, 
            $this->verificadorDeDadosExistentes
        );
    }

    public function testTesteUsuarioESenhaNaoExistemNoBanco()
    {
        $usuarioESenhaEstaoCadastrados = $this->verificadorDeDadosExistentes->expects($this->once())
            ->method('verificaSeUsuarioESenhaEstaoCadastrados')->willReturn(false);

        $usuario = new Usuario();

        $loginExiste = $this->buscadorLogin->buscar($usuario);

        $erros = $this->controladorDeErros->getErros();

        $this->assertFalse($loginExiste);

        $this->assertArrayHasKey(21, $erros);
    }

    public function testTesteUsuarioESenhaExistemNoBanco()
    {
        $usuarioESenhaEstaoCadastrados = $this->verificadorDeDadosExistentes->expects($this->once())
            ->method('verificaSeUsuarioESenhaEstaoCadastrados')->willReturn(true);

        $usuario = new Usuario();

        $loginExiste = $this->buscadorLogin->buscar($usuario);

        $erros = $this->controladorDeErros->getErros();

        $this->assertTrue($loginExiste);

        $this->assertArrayNotHasKey(21, $erros);
    }
}
