<?php

namespace SisLogin\Projeto\Tests\Validacoes;

use PHPUnit\Framework\TestCase;
use SisLogin\Projeto\Modelo\Erros;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Validacoes\ValidadorLogin;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class ValidadorLoginTest extends TestCase
{
    protected function setUp(): void
    {
        $this->controladorDeErros = new Erros();
        $this->validadorLogin = new ValidadorLogin($this->controladorDeErros);
    }

    public function testTesteUsuarioVazio()
    {
        $usuario = new Usuario(usuario: '');

        $validou = $this->validadorLogin->validar($usuario);

        $erros = $this->controladorDeErros->getErros();

        $this->assertArrayHasKey(1, $erros);    
        $this->assertFalse($validou);
    }

    public function testTesteSenhaVazia()
    {
        $usuario = new Usuario(senha: '');

        $validou = $this->validadorLogin->validar($usuario);

        $erros = $this->controladorDeErros->getErros();

        $this->assertArrayHasKey(9, $erros);    
        $this->assertFalse($validou);
    }

    public function testTesteUsuarioVazioESenhaVazia()
    {
        $usuario = new Usuario(usuario: '', senha: '');

        $validou = $this->validadorLogin->validar($usuario);

        $erros = $this->controladorDeErros->getErros();

        $this->assertArrayHasKey(1, $erros);
        $this->assertArrayHasKey(9, $erros);  
        $this->assertFalse($validou);
    }

    public function testTesteUsuarioPreenchidoESenhaPreenchida()
    {
        $usuario = new Usuario(usuario: 'oi', senha: 'oi');

        $validou = $this->validadorLogin->validar($usuario);

        $erros = $this->controladorDeErros->getErros();

        $this->assertArrayNotHasKey(1, $erros);
        $this->assertArrayNotHasKey(9, $erros);  
        $this->assertTrue($validou);
    }
}
