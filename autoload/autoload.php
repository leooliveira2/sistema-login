<?php

spl_autoload_register(function(string $caminho) {
    $caminhoArquivo = str_replace("SisLogin\\Projeto", "src", $caminho);
    $caminhoArquivo .= ".php";
    $caminhoArquivo = str_replace('\\', DIRECTORY_SEPARATOR, $caminhoArquivo);

    require_once $caminhoArquivo;
});
