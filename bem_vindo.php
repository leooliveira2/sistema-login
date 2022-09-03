<?php

require_once 'autoload\autoload.php';
use SisLogin\Projeto\Modelo\Login;

$login = new Login();

if (!isset($_SESSION['TOKEN'])) {
    header('location: erro.html');
}

$login->estaAutenticado($_SESSION['TOKEN']);

echo "Olá {$login->getUsuario()}";

?>
