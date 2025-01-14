<?php
require $_SERVER['DOCUMENT_ROOT'] . '/inc/valida_token.php';
require $_SERVER['DOCUMENT_ROOT'] . '/inc/def.php';

if (empty($_POST['iduserecebedor']) || empty($_POST['tipochave']) || empty($_POST['pix']) || empty($_POST['valor']) || empty($_POST['saldouserlogado'])) {
    echo $_SESSION['msg'] = 'Erro: Transferência não realizada, tente novamente';
    irJs('/minha-conta');
}

$transferencia = AccountBank::transferencia($_COOKIE['id'], $_POST['saldouserlogado'], $_POST['iduserecebedor'], $_POST['saldouserecebedor'], $_POST['tipochave'], $_POST['pix'], $_POST['valor']);
$transferencia = json_decode($transferencia);

if ($transferencia->status != 200) {
    echo $_SESSION['msg'] = 'Erro: Transferência não realizada, tente novamente!';
    irJs('/minha-conta');
} else {
    echo $_SESSION['msg'] = 'Transferência realizada com sucesso!';
    irJs('/minha-conta');
}
