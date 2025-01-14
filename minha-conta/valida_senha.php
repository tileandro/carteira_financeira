<?php
require $_SERVER['DOCUMENT_ROOT'] . '/inc/valida_token.php';
require $_SERVER['DOCUMENT_ROOT'] . '/inc/def.php';

if (empty($_POST['senha'])) {
    die($json = array(
        'status' => 400,
    ));
}

$login = User::validaSenha($_COOKIE['id'], $_POST['senha']);

echo $login;
