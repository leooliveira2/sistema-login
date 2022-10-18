<?php

namespace SisLogin\Projeto\Log;

class Log
{
    public function __construct(
        private \DateTimeImmutable $data)
    {}

    public function gerarLogDeErro(string $msg) : void
    {
        $msgFormatada = "[" . $this->data->format('d/m/Y') . "]" . " - " . $msg . PHP_EOL;

        $caminhoArquivo = __DIR__ . "/../../log.txt";
        $fp = fopen($caminhoArquivo, "a+");

        fwrite($fp, $msgFormatada);
        
        fclose($fp);
    }
}
