<?php

$caminhoArquivo = __DIR__ . '\bancoTeste.sqlite';

$pdo = new PDO('sqlite:' . $caminhoArquivo);

$pdo->exec('CREATE TABLE IF NOT EXIStS usuarios_teste (
    id INTEGER PRIMARY KEY,
    usuario UNIQUE NOT NULL,
    nome TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    senha TEXT NOT NULL,
    token TEXT
);');

echo "DEU CERTO";
