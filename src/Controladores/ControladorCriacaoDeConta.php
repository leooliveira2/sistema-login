<?php

namespace SisLogin\Projeto\Controladores;

use SisLogin\Projeto\Excecoes\ConexaoException;
use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Infraestrutura\UsuarioSalvoNoBanco;
use SisLogin\Projeto\Validacoes\Validador;

class ControladorCriacaoDeConta
{
    public function __construct(
        private Usuario $usuario,
        private Validador $validador,
        private UsuarioSalvoNoBanco $usuarioDeveSerSalvo
    )
    {}

    public function execucoes() : bool
    {        
        $validou = $this->validaDadosASeremSalvosNoBanco();
        
        if ($validou) {
            $salvou = $this->salvaUsuarioNoBanco();

            if ($salvou) {
                return true;
            }
        }

        return false;
    }

    private function validaDadosASeremSalvosNoBanco() : bool
    {
        try {
            $validou = $this->validador->validar($this->usuario);
        } catch (\PDOException | ConexaoException $e) {
            throw new ConexaoException($e->getMessage());
        }

        return $validou;
    }

    private function salvaUsuarioNoBanco() : bool
    {
        try {
            $salvou = $this->usuarioDeveSerSalvo->salvar($this->usuario);
        } catch (\PDOException | ConexaoException $e) {
            throw new ConexaoException($e->getMessage());
        }
            
        if ($salvou) {
            return true;
        }

        return false;
    }
}
