<?php
function consultaCep(string $cep)
{
    $url = "https://viacep.com.br/ws/$cep/json/";
    $response = file_get_contents($url);
    $response = json_decode(json_encode($response), true);

    return $response;
}

function consultaCpf(string $cpf)
{
    // Extrai somente os números
    $cpf = preg_replace('/[^0-9]/is', '', $cpf);

    // Verifica se foi informado todos os digitos corretamente
    if (strlen($cpf) != 11) {
        return false;
    }

    // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // Faz o calculo para validar o CPF
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;
}

function voltar()
{
    echo '<script type="text/javascript">history.go(-1);</script>';
    exit;
}

function validaEmail(string $email)
{
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    } else {
        return true;
    }
}

function irJs($url)
{
    echo '<script type="text/javascript">window.location.href = "' . $url . '";</script>';
    exit;
}

function validarToken()
{
    $token = $_COOKIE['token'];
    $tokenArray = explode('.', $token);
    $header = $tokenArray[0];
    $payload = $tokenArray[1];
    $assinatura = $tokenArray[2];

    $valida_assinatura = hash_hmac('sha256', "$header.$payload", CHAVE, true);

    $valida_assinatura = base64_encode($valida_assinatura);

    if ($assinatura == $valida_assinatura) {
        //Decodificar payload
        $dados_token = base64_decode($payload);
        $dados_token = json_decode($dados_token);

        //Checando a duração do token
        if ($dados_token->exp <= time()) {
            return false;
        }

        return true;
    } else {
        return false;
    }
}
