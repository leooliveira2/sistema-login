<?php

function limpaPost(string $valor)
{
    $valor = trim($valor);
    $valor = stripslashes($valor);
    $valor = htmlspecialchars($valor);

    return $valor;
}