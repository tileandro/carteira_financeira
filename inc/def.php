<?php
session_start();

require_once 'variables.php';
require_once 'functions.php';
require_once 'classes.php';

try {
    $conn = new PDO('mysql:host=' . DBHOST . ';port=' . DBPORT . ';dbname=' . DBNAME, DBUSER, DBPASS);
} catch (PDOException $err) {
    die("ERRO: Conexão com o banco de dados não realizada! Error: " . $err->getMessage());
}
