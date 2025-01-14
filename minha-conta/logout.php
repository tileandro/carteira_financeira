<?php
session_start();
setcookie('id', '', -1, '/');
setcookie('nome', '', -1, '/');
setcookie('token', '', -1, '/');

$_SESSION['msg'] = 'Deslogado com sucesso!';
header("Location: /");
