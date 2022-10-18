<?php

namespace SisLogin\Projeto\Validacoes;

use SisLogin\Projeto\Modelo\{Usuario, Erros};

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class ValidadorFormulario
{
    private $controladorDeErros;
    
    const FORM_ERROR_USUARIO_VAZIO = 1;
    const FORM_ERROR_USUARIO_COM_CARACTERES_INVALIDOS = 2;
    const FORM_ERROR_USUARIO_COM_MENOS_DE_5_CARACTERES = 3;
    const FORM_ERROR_USUARIO_NAO_PODE_TER_MAIS_DE_32_CARACTERES = 4;
    const FORM_ERROR_NOME_VAZIO = 5;
    const FORM_ERROR_NOME_COM_CARACTERES_INVALIDOS = 6;
    const FORM_ERROR_NOME_COM_MENOS_DE_5_CARACTERES = 7;
    const FORM_ERROR_NOME_NAO_PODE_TER_MAIS_DE_150_CARACTERES = 8;
    const FORM_ERROR_EMAIL_VAZIO = 9;
    const FORM_ERROR_EMAIL_INVALIDO = 10;
    const FORM_ERROR_EMAIL_NAO_PODE_TER_MAIS_DE_200_CARACTERES = 11;
    const FORM_ERROR_SENHA_VAZIA = 12;
    const FORM_ERROR_SENHA_COM_MENOS_DE_8_CARACTERES = 13;
    const FORM_ERROR_SENHA_NAO_PODE_TER_MAIS_DE_32_CARACTERES = 14;
    const FORM_ERROR_REPETE_SENHA_VAZIO = 15;
    const FORM_ERROR_REPETE_SENHA_SEM_SENHA_SETADA = 16;
    const FORM_ERROR_REPETE_SENHA_NAO_E_IGUAL_A_SENHA = 17;
    const FORM_ERROR_REPETE_SENHA_NAO_PODE_TER_MAIS_DE_32_CARACTERES = 18;
    
    public function __construct(Erros $controladorDeErros)
    {
        $this->controladorDeErros = $controladorDeErros;
    }
    
    public function validar(Usuario $usuario) : bool
    {
        $isValid = true;

        // PARTE DE USUÁRIO
        if (!is_null($usuario->getUsuario())) {
            $lenUsuario = strlen($usuario->getUsuario());
        }

        if (!is_null($usuario->getUsuario()) && empty($usuario->getUsuario())) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_USUARIO_VAZIO, 
                "O campo usuário não pode ser vazio!"
            );

            $isValid = false;
        } else if (!is_null($usuario->getUsuario()) && !preg_match("/^[A-Za-z1-9'\s]+$/", $usuario->getUsuario())) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_USUARIO_COM_CARACTERES_INVALIDOS, 
                "Informe um nome de usuário que contenha somente letras e/ou números!"
            );

            $isValid = false;
        } else if (!is_null($usuario->getUsuario()) && $lenUsuario < 5) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_USUARIO_COM_MENOS_DE_5_CARACTERES, 
                "Usuário não pode ter menos de 5 caracteres!"
            );

            $isValid = false;
        } else if (!is_null($usuario->getUsuario()) && $lenUsuario > 32) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_USUARIO_NAO_PODE_TER_MAIS_DE_32_CARACTERES, 
                "Usuário não pode ter mais de 32 caracteres!"
            );

            $isValid = false;
        }

        // PARTE DE NOME
        if (!is_null($usuario->getNome())) {
            $lenNome = strlen($usuario->getNome());
        }

        if (!is_null($usuario->getNome()) && empty($usuario->getNome())) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_NOME_VAZIO,
                "O campo nome não pode ser vazio"
            );

            $isValid = false;
        } else if (
            !is_null($usuario->getNome()) &&
            !preg_match("/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ'\s]+$/", $usuario->getNome())
        ) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_NOME_COM_CARACTERES_INVALIDOS, 
                "Informe um nome sem caracteres especiais ou números!"
            );

            $isValid = false;
        } else if (!is_null($usuario->getNome()) && $lenNome < 5) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_NOME_COM_MENOS_DE_5_CARACTERES, 
                "Nome não pode ter menos de 5 caracteres, se necessário digite seu sobrenome!"
            );

            $isValid = false;
        } else if (!is_null($usuario->getNome()) && $lenNome > 150) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_NOME_NAO_PODE_TER_MAIS_DE_150_CARACTERES, 
                "Nome não pode ter mais de 150 caracteres!"
            );

            $isValid = false;
        }

        // PARTE DE EMAIL
        if (!is_null($usuario->getEmail())) {
            $lenEmail = strlen($usuario->getEmail());
        }

        if (!is_null($usuario->getEmail()) && $lenEmail > 200) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_EMAIL_NAO_PODE_TER_MAIS_DE_200_CARACTERES, 
                "E-mail não pode ter mais de 200 caracteres!"
            );

            $isValid = false;
        } else if (!is_null($usuario->getEmail()) && empty($usuario->getEmail())) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_EMAIL_VAZIO, 
                "O campo e-mail não pode ser vazio!"
            );

            $isValid = false;
        } else if (!is_null($usuario->getEmail()) && !filter_var($usuario->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_EMAIL_INVALIDO, 
                "Informe um e-mail válido. Exemplo: email@email.com"
            );

            $isValid = false;
        }

        // PARTE DE SENHA
        if (!is_null($usuario->getSenha())) {
            $lenSenha = strlen($usuario->getSenha());
        }

        if (!is_null($usuario->getSenha()) && empty($usuario->getSenha())) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_SENHA_VAZIA,
                "O campo senha não pode ser vazio!"
            );

            $isValid = false;
        } else if (!is_null($usuario->getSenha()) && $lenSenha < 8) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_SENHA_COM_MENOS_DE_8_CARACTERES,
                'Informe uma senha com no minímo 8 caracteres!'
            );

            $isValid = false;
        } else if (!is_null($usuario->getSenha()) && $lenSenha > 32) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_SENHA_NAO_PODE_TER_MAIS_DE_32_CARACTERES, 
                "Senha não pode ter mais de 32 caracteres!"
            );

            $isValid = false;
        }

        // PARTE DE REPETE SENHA
        if (!is_null($usuario->getRepeteSenha())) {
            $lenRepeteSenha = strlen($usuario->getRepeteSenha());
        }

        if (!is_null($usuario->getRepeteSenha()) && $lenRepeteSenha > 32) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_REPETE_SENHA_NAO_PODE_TER_MAIS_DE_32_CARACTERES, 
                "Repetição da senha não pode ter mais de 32 caracteres!"
            );

            $isValid = false;
        } else if (!is_null($usuario->getRepeteSenha()) && empty($usuario->getRepeteSenha())) {
            $this->controladorDeErros->add(
                self::FORM_ERROR_REPETE_SENHA_VAZIO,
                'O campo repete senha não pode ser vazio!'
            );

            $isValid = false;
        } else if (
            !is_null($usuario->getRepeteSenha()) &&
            (is_null($usuario->getSenha()) || empty($usuario->getSenha()))) 
            {
                $this->controladorDeErros->add(
                    self::FORM_ERROR_REPETE_SENHA_SEM_SENHA_SETADA,
                    'A senha precisa estar digitada para verificar a repetição!'
                );

                $isValid = false;
        } else if (
            !is_null($usuario->getSenha()) &&
            !is_null($usuario->getRepeteSenha()) &&
            $usuario->getSenha() !== $usuario->getRepeteSenha()
            )
        {
            $this->controladorDeErros->add(
                self::FORM_ERROR_REPETE_SENHA_NAO_E_IGUAL_A_SENHA,
                'A repetição da senha está diferente da senha escolhida'
            );

            $isValid = false;
        }

        return $isValid;
    }
}
