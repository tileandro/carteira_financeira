<?php
require $_SERVER['DOCUMENT_ROOT'] . '/inc/def.php';

if (empty($_POST['cpf']) || empty($_POST['senha'])) {
    $_SESSION['msg'] = 'Erro: Prencha todos os campos';
    voltar();
}

$login = User::loginUser($_POST['cpf'], $_POST['senha']);
$statusLogin = json_decode($login);

if ($statusLogin->status == 200) {
    irJs('/minha-conta');
} else {
    $_SESSION['msg'] = 'Erro: Credenciais inválida!';
    voltar();
}
