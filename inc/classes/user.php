<?php
class User
{

    public static function createUser(string $nome, string $email, string $cpf, string $data_nascimento, string $telefone, string $senha)
    {
        global $conn;
        $ins = "INSERT INTO usuarios (nome, email, cpf, data_nascimento, telefone, senha, created_at)
        VALUES(:nome, :email, :cpf, :data_nascimento, :telefone, :senha, NOW());";
        $result = $conn->prepare($ins);
        $result->bindParam(':nome', $nome);
        $result->bindParam(':email', $email);
        $result->bindParam(':cpf', $cpf);
        $result->bindParam(':data_nascimento', $data_nascimento);
        $result->bindParam(':telefone', $telefone);
        $result->bindParam(':senha', $senha);
        $result->execute();
        $id = $conn->lastInsertId();

        if ($result->rowCount()) {
            $json = array(
                'status' => 200,
                'id' => (int)$id
            );
        } else {
            $json = array(
                'status' => 400,
                'id' => (int)$id
            );
        }

        return json_encode($json);
    }

    public static function consultaEmailUser(string $email)
    {
        global $conn;
        $sel = "SELECT id FROM usuarios WHERE email = :email;";
        $result = $conn->prepare($sel);
        $result->bindParam(':email', $email);
        $result->execute();

        if ($result->rowCount()) {
            $json = array(
                'status' => 400,
            );
        } else {
            $json = array(
                'status' => 200,
            );
        }

        return json_encode($json['status']);
    }

    public static function consultaCpfUser(string $cpf)
    {
        global $conn;
        $sel = "SELECT id FROM usuarios WHERE cpf = :cpf;";
        $result = $conn->prepare($sel);
        $result->bindParam(':cpf', $cpf);
        $result->execute();

        if ($result->rowCount()) {
            $json = array(
                'status' => 400,
            );
        } else {
            $json = array(
                'status' => 200,
            );
        }

        return json_encode($json['status']);
    }

    public static function enderecoUser(int $idUser, string $cep, string $logradouro, string $numero, string $complemento, string $bairro, string $cidade, string $estado)
    {
        global $conn;
        $ins = "INSERT INTO enderecos (id_user, cep, logradouro, numero, complemento, bairro, cidade, estado, created_at)
        VALUES(:idUser, :cep, :logradouro, :numero, :complemento, :bairro, :cidade, :estado, NOW());";
        $result = $conn->prepare($ins);
        $result->bindParam(":idUser", $idUser);
        $result->bindParam(":cep", $cep);
        $result->bindParam(":logradouro", $logradouro);
        $result->bindParam(":numero", $numero);
        $result->bindParam(":complemento", $complemento);
        $result->bindParam(":bairro", $bairro);
        $result->bindParam(":cidade", $cidade);
        $result->bindParam(":estado", $estado);
        $result->execute();

        if ($result->rowCount()) {
            $json = array(
                'status' => 200,
            );
        } else {
            $json = array(
                'status' => 400,
            );
        }

        return json_encode($json['status']);
    }

    public static function loginUser(string $cpf, string $senha)
    {
        global $conn;

        $sel = "SELECT id, nome, email, senha FROM usuarios WHERE cpf = :cpf";
        $result = $conn->prepare($sel);
        $result->bindParam(":cpf", $cpf);
        $result->execute();

        if ($result->rowCount()) {
            $row = $result->fetch(PDO::FETCH_ASSOC);
            if (password_verify($senha, $row['senha'])) {
                //Gerando Token JWT
                //Cabeçalho do token
                $header = array(
                    'alg' => 'HS256',
                    'typ' => 'JWT'
                );
                $header = json_encode($header);
                $header = base64_encode($header);

                //Payload do token com duração de 2 minutos
                $duracao = time() + (TEMPO_TOKEN);
                $payload = array(
                    //'iss' => 'localhost',
                    //'aud' => 'localhost',
                    'exp' => $duracao,
                    'id' => $row['id'],
                    'email' => $row['email'],
                    'nome' => $row['nome'],
                );
                $payload = json_encode($payload);
                $payload = base64_encode($payload);

                //Assinatura do token
                $assinatura = hash_hmac('sha256', "$header.$payload", CHAVE, true);
                $assinatura = base64_encode($assinatura);

                //Token gerado
                $token = "$header.$payload.$assinatura";

                //Salva o token nos cookies
                setcookie('token', $token, (time() + (TEMPO_TOKEN)), '/');
                setcookie('id', $row['id'], (time() + (TEMPO_TOKEN)), '/');
                setcookie('nome', $row['nome'], (time() + (TEMPO_TOKEN)), '/');

                $json = array(
                    'status' => 200,
                    'id' => $row['id'],
                    'email' => $row['email'],
                );
            } else {
                $json = array(
                    'status' => 400,
                );
            }
        } else {
            $json = array(
                'status' => 400,
            );
        }

        return json_encode($json);
    }

    public static function validaSenha(int $id, string $senha)
    {
        global $conn;

        $sel = "SELECT id, senha FROM usuarios WHERE id = $id";
        $result = $conn->prepare($sel);
        $result->execute();

        if ($result->rowCount()) {
            $row = $result->fetch(PDO::FETCH_ASSOC);
            if (password_verify($senha, $row['senha'])) {
                $json = array(
                    'status' => 200,
                );
            } else {
                $json = array(
                    'status' => 400,
                );
            }
        } else {
            $json = array(
                'status' => 400,
            );
        }

        return json_encode($json);
    }
}
