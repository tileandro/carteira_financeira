<?php
require $_SERVER['DOCUMENT_ROOT'] . '/inc/def.php';

if (!validarToken()) {
    $_SESSION['msg'] = "Erro: Faça login novamente";
    irJs('/');
}
