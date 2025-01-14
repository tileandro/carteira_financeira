<?php
class Deposito
{
    public static function depositar(string $nome, string $email, string $telefone, string $agencia, int $conta, string $valor)
    {
        global $conn;

        //Pesquisa se existe agência e conta digitada
        $sel = "SELECT id, saldo FROM conta_bancaria WHERE agencia = :agencia AND id = :conta;";
        $result = $conn->prepare($sel);
        $result->bindParam(':agencia', $agencia);
        $result->bindParam(':conta', $conta);
        $result->execute();

        if (!$result->rowCount()) {
            $json = array(
                'status' => 400,
                'msg' => 'Erro: Conta não existente!'
            );
            return json_encode($json);
        }

        $row = $result->fetch(PDO::FETCH_ASSOC);
        $valor = str_replace(',', '.', $valor);
        $valor_saldo = $row['saldo'] + $valor;

        //Deposita o valor do depósito somando com o valor que tem na conta
        $ins = "UPDATE conta_bancaria
        SET saldo = :valor_saldo,
        updated_at = NOW()
        WHERE agencia = :agencia AND
        id = :conta;";
        $result = $conn->prepare($ins);
        $result->bindParam(':valor_saldo', $valor_saldo);
        $result->bindParam(':agencia', $agencia);
        $result->bindParam(':conta', $conta);
        $result->execute();

        if (!$result->rowCount()) {
            $json = array(
                'status' => 400,
                'msg' => 'Erro: Depósito não efetuado!'
            );

            return json_encode($json);
        }

        //Salva o registro do depósito
        $ins = "INSERT INTO depositos (nome, email, telefone, agencia, conta, valor, created_at)
        VALUES (:nome, :email, :telefone, :agencia, :conta, :valor, NOW());";
        $result = $conn->prepare($ins);
        $result->bindParam(':nome', $nome);
        $result->bindParam(':email', $email);
        $result->bindParam(':telefone', $telefone);
        $result->bindParam(':agencia', $agencia);
        $result->bindParam(':conta', $conta);
        $result->bindParam(':valor', $valor);
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

        return json_encode($json);
    }
}
