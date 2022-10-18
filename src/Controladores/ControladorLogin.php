<?php

namespace SisLogin\Projeto\Controladores;

use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Servicos\Login;
use SisLogin\Projeto\Validacoes\ValidadorLogin;

class ControladorLogin
{
    public function __construct(
        private Usuario $usuario,
        private ValidadorLogin $validadorLogin,
        private Login $login
    )
    {}

    public function execucoes() : bool
    {   
        $validouLogin = $this->validadorLogin->validar($this->usuario);

        if ($validouLogin) {
            try {
                $loginBemSucedido = $this->login->logar($this->usuario);
            } catch (\PDOException | ConexaoException $e) {
                throw new ConexaoException($e->getMessage());
            }

            if ($loginBemSucedido) {
                return true;
            }
        }

        return false;
    }
}
