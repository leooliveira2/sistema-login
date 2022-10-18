<?php

namespace SisLogin\Projeto\Tests\Validacoes;

use PHPUnit\Framework\TestCase;
use SisLogin\Projeto\Modelo\{Usuario, Erros};
use SisLogin\Projeto\Validacoes\ValidadorFormulario;

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class ValidadorFormularioTest extends TestCase
{
    protected function setUp(): void
    {
        $this->controladorDeErros = new Erros();
        $this->validaFormulario = new ValidadorFormulario($this->controladorDeErros);
    }

    public function procedimentoPadraoDosTestes(int $codigoErro, Usuario $usuario)
    {
        $this->validou = $this->validaFormulario->validar($usuario);

        $erros = $this->controladorDeErros->getErros();

        $this->assertArrayHasKey($codigoErro, $erros);
    }

    /**
     * @dataProvider passaDadosUsuario
     */
    public function testTesteDaPropriedadeUsuarioQuePrecisamDarErro(int $codigoErro, Usuario $usuario)
    {
        $this->procedimentoPadraoDosTestes($codigoErro, $usuario);

        $this->assertFalse($this->validou);
    }

    public function passaDadosUsuario()
    {
        return [
            'usuario-vazio' => [
                1, new Usuario(usuario: '')
            ],

            'usuario-com-caracteres-nao-permitidos' => [
                2, new Usuario(usuario: '@#!++')
            ],

            'usuario-com-menos-de-5-caracteres' => [
                3, new Usuario(usuario: 'aaaa')
            ],

            'usuario-com-mais-de-32-caracteres' => [
                4, new Usuario(usuario: str_repeat('a', 33))
            ]
        ];
    }

    public function testTesteDaPropriedadeUsuarioComUsuarioValido()
    {
        $usuario = new Usuario(usuario: 'Usuario');

        $validou = $this->validaFormulario->validar($usuario);

        $erros = $this->controladorDeErros->getErros();

        $this->assertTrue($validou);

        $this->assertArrayNotHasKey(1, $erros);
        $this->assertArrayNotHasKey(2, $erros);
        $this->assertArrayNotHasKey(3, $erros);
        $this->assertArrayNotHasKey(4, $erros);
    }

    /**
     * @dataProvider passaDadosNome
     */
    public function testTesteDaPropriedadeNomeQuePrecisamDarErro(int $codigoErro, Usuario $usuario)
    {
        $this->procedimentoPadraoDosTestes($codigoErro, $usuario);

        $this->assertFalse($this->validou);
    }

    public function passaDadosNome()
    {
        return [
            'nome-vazio' => [
                5, new Usuario(nome: '')
            ],

            'nome-com-caracteres-nao-permitidos' => [
                6, new Usuario(nome: '@!12#5')
            ],

            'nome-com-menos-de-5-caracteres' => [
                7, new Usuario(nome: 'aaaa')
            ],

            'nome-com-mais-de-150-caracteres' => [
                8, new Usuario(nome: str_repeat('a', 151))
            ]
        ];
    }

    public function testTesteDaPropriedadeNomeComNomeValido()
    {
        $usuario = new Usuario(nome: 'nome teste');

        $validou = $this->validaFormulario->validar($usuario);

        $erros = $this->controladorDeErros->getErros();

        $this->assertTrue($validou);

        $this->assertArrayNotHasKey(5, $erros);
        $this->assertArrayNotHasKey(6, $erros);
        $this->assertArrayNotHasKey(7, $erros);
        $this->assertArrayNotHasKey(8, $erros);
    }

    /**
     * @dataProvider passaDadosEmail
     */
    public function testTesteDaPropriedadeEmailQuePrecisamDarErro(int $codigoErro, Usuario $usuario)
    {
        $this->procedimentoPadraoDosTestes($codigoErro, $usuario);

        $this->assertFalse($this->validou);
    }

    public function passaDadosEmail()
    {
        return [
            'email-vazio' => [
                9, new Usuario(email: '')
            ],

            'email-com-formato-invalido' => [
                10, new Usuario(email: 'shaushaushashu')
            ],

            'email-nao-pode-ter-mais-de-200-caracteres' => [
                11, new Usuario(email: 'e@email.com' . str_repeat('a', 201))
            ]
        ];
    }

    public function testTesteDaPropriedadeEmailComEmailValido()
    {
        $usuario = new Usuario(email: 'email@email.com');

        $validou = $this->validaFormulario->validar($usuario);

        $erros = $this->controladorDeErros->getErros();

        $this->assertTrue($validou);

        $this->assertArrayNotHasKey(9, $erros);
        $this->assertArrayNotHasKey(10, $erros);
        $this->assertArrayNotHasKey(11, $erros);
    }

    /**
     * @dataProvider passaDadosSenha
     */
    public function testTesteDaPropriedadeSenhaQuePrecisamDarErro(int $codigoErro, Usuario $usuario)
    {
        $this->procedimentoPadraoDosTestes($codigoErro, $usuario);

        $this->assertFalse($this->validou);
    }

    public function passaDadosSenha()
    {
        return [
            'senha-vazia' => [
                12, new Usuario(senha: '')
            ],

            'senha-com-menos-de-8-caracteres' => [
                13, new Usuario(senha: '1234567')
            ],

            'senha-com-mais-de-32-caracteres' => [
                14, new Usuario(senha: str_repeat('a', 33))
            ]
        ];
    }

    public function testTesteDaPropriedadeSenhaComSenhaValida()
    {
        $usuario = new Usuario(senha: '12345678');

        $validou = $this->validaFormulario->validar($usuario);

        $erros = $this->controladorDeErros->getErros();

        $this->assertTrue($validou);

        $this->assertArrayNotHasKey(12, $erros);
        $this->assertArrayNotHasKey(13, $erros);
        $this->assertArrayNotHasKey(14, $erros);
    }

    /**
     * @dataProvider passaDadosRepeteSenha
     */
    public function testTesteDaPropriedadeRepeteSenhaQuePrecisamDarErro(int $codigoErro, Usuario $usuario)
    {
        $this->procedimentoPadraoDosTestes($codigoErro, $usuario);

        $this->assertFalse($this->validou);
    }

    public function passaDadosRepeteSenha()
    {
        return [
            'repete-senha-vazio' => [
                15, new Usuario(repeteSenha: '')
            ],

            'repete-senha-sem-senha-preenchida' => [
                16, new Usuario(senha: '', repeteSenha: '12345678')
            ],

            'repete-senha-diferente-de-senha' => [
                17, new Usuario(senha: '12345678', repeteSenha: '1234567')
            ],

            'repete-senha-com-mais-de-32-caracteres' => [
                18, new Usuario(repeteSenha: str_repeat('a', 33))
            ]
        ];
    }

    public function testTesteDaPropriedadeRepeteSenhaComRepeteSenhaValida()
    {
        $usuario = new Usuario(senha: '12345678', repeteSenha: '12345678');

        $validou = $this->validaFormulario->validar($usuario);

        $erros = $this->controladorDeErros->getErros();

        $this->assertTrue($validou);

        $this->assertArrayNotHasKey(15, $erros);
        $this->assertArrayNotHasKey(16, $erros);
        $this->assertArrayNotHasKey(17, $erros);
        $this->assertArrayNotHasKey(18, $erros);
    }
}
