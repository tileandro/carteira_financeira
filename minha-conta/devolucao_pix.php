<?php
require $_SERVER['DOCUMENT_ROOT'] . '/inc/valida_token.php';
require $_SERVER['DOCUMENT_ROOT'] . '/inc/def.php';

$status = AccountBank::devolucaoPix($_GET['id']);
echo $status;
