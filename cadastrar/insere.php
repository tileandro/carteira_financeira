<?php
require $_SERVER['DOCUMENT_ROOT'] . '/inc/def.php';

if (empty($_POST['nome']) || empty($_POST['datanasc']) || empty($_POST['telefone']) || empty($_POST['email']) || empty($_POST['cpf']) || empty($_POST['cep']) || empty($_POST['logradouro']) || empty($_POST['numero']) || empty($_POST['bairro']) || empty($_POST['cidade']) || empty($_POST['estado']) || empty($_POST['senha']) || empty($_POST['confirmasenha'])) {
    $_SESSION['msg'] = 'Campos obrigatórios não preenchidos!';
    voltar();
}

if ($_POST['senha'] != $_POST['confirmasenha']) {
    $_SESSION['msg'] = 'Campos senha não coincidem!';
    voltar();
}

//Valida se é um email válido
if (!validaEmail($_POST['email'])) {
    $_SESSION['msg'] = 'Formato do e-mail é inválido!';
    voltar();
}

//Checa se não existe um E-mail já cadastrado
$consultaEmail = User::consultaEmailUser($_POST['email']);
if ($consultaEmail != 200) {
    $_SESSION['msg'] = 'E-mail já cadastrado!';
    voltar();
}

//Checa se não existe um CPF já cadastrado
$consultaCpf = User::consultaCpfUser($_POST['cpf']);
if ($consultaCpf != 200) {
    $_SESSION['msg'] = 'CPF já cadastrado!';
    voltar();
}

$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

$data_nasc = date('Y-m-d', strtotime($_POST['datanasc']));

//Cadastra usuário
$userJson = User::createUser($_POST['nome'],  $_POST['email'],  $_POST['cpf'],  $data_nasc,  $_POST['telefone'],  $senha);
$userJson = json_decode($userJson);
$idUser = $userJson->id;
$statusUser = $userJson->status;

//Cadastra endereço
$enderecoUser = User::enderecoUser($idUser, $_POST['cep'], $_POST['logradouro'], $_POST['numero'], !empty($_POST['complemento']) ? $_POST['complemento'] : '', $_POST['bairro'], $_POST['cidade'], $_POST['estado']);

//Cadastrar conta bancaria
$accountUser = AccountBank::createAccount($idUser);

if ($statusUser == 200 && $enderecoUser == 200 && $accountUser == 200) {
    echo $_SESSION['msg'] = 'Usuário cadastrado com sucesso!';
    irJs('/');
} else {
    echo $_SESSION['msg'] = 'Erro: Usuário não cadastrado!';
    voltar();
}
