<?php

namespace SisLogin\src\Modelo;

use SisLogin\src\Conexao\DB;

require_once 'src\Conexao\DB.php';

class Login
{
    private string $usuario;
    private string $senha;

    public function __construct(string $usuario, string $senha)
    {
        $this->usuario = $usuario;
        $this->senha = $senha;
    }

    public function logar()
    {
        $sql = "SELECT usuario, senha FROM usuarios WHERE usuario = '$this->usuario';";
        $preparar = DB::instanciar();

        if ($preparar) {
            $query = $preparar->query($sql);
            $usuario = $query->fetchAll();
            
            $senhaCripto = sha1($this->senha);

            if ($usuario[0]['senha'] === $senhaCripto) {
                return true;
            } else {
                return false;
            }
        }
      
    }
}