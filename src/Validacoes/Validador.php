<?php

namespace SisLogin\Projeto\Validacoes;

use SisLogin\Projeto\Modelo\Usuario;
use SisLogin\Projeto\Validacoes\{VerificadorDeDadosASeremSalvos, ValidadorFormulario};

require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

class Validador
{
    private ValidadorFormulario $validadorFormulario;
    private VerificadorDeDadosASeremSalvos $verificaSeDadosPodemSerSalvos;

    public function __construct(
        ValidadorFormulario $validadorFormulario,
        VerificadorDeDadosASeremSalvos $verificaSeDadosPodemSerSalvos
    )
    {
        $this->validadorFormulario = $validadorFormulario;
        $this->verificaSeDadosPodemSerSalvos = $verificaSeDadosPodemSerSalvos;
    }

    public function validar(Usuario $usuario) : bool
    {
        $validadorFormulario = $this->validadorFormulario->validar($usuario);
        $verificaSeDadosPodemSerSalvos = $this->verificaSeDadosPodemSerSalvos->verificar($usuario);

        if ($validadorFormulario && $verificaSeDadosPodemSerSalvos) {
            return true;
        }

        return false;
    }
}
