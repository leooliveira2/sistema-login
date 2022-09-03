<?php

$pdo = new PDO('sqlite:banco.sqlite');

$pdo->exec('CREATE TABLE usuarios (
    id INTEGER PRIMARY KEY,
    usuario UNIQUE NOT NULL,
    nome TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    senha TEXT NOT NULL,
    token TEXT
);');

echo "DEU CERTO";
