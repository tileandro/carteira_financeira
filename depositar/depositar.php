<?php
require $_SERVER['DOCUMENT_ROOT'] . '/inc/def.php';

if (empty($_POST['nome']) || empty($_POST['email']) || empty($_POST['telefone']) || empty($_POST['agencia']) || empty($_POST['valor']) || empty($_POST['conta'])) {
    $_SESSION['msg'] = 'Erro: Depósito não realizado, tente novamente';
    irJs('/depositar');
}

$deposito = Deposito::depositar($_POST['nome'], $_POST['email'], $_POST['telefone'], $_POST['agencia'], (int)$_POST['conta'], $_POST['valor']);
$deposito = json_decode($deposito);

if ($deposito->status != 200) {
    $_SESSION['msg'] = 'Erro: Depósito não realizado, tente novamente!';
    irJs('/depositar');
} else {
    $_SESSION['msg'] = 'Depósito realizado com sucesso!';
    irJs('/depositar');
}
