<?php
class AccountBank
{
    public static function createAccount(int $idUser)
    {
        global $conn;
        $ins = "INSERT INTO conta_bancaria (id_user, created_at) VALUES(:idUser, NOW());";
        $result = $conn->prepare($ins);
        $result->bindParam(':idUser', $idUser);
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

    public static function contaUser(int $idUser)
    {
        global $conn;
        $sel = "SELECT * FROM conta_bancaria WHERE id_user = :idUser;";
        $result = $conn->prepare($sel);
        $result->bindParam(':idUser', $idUser);
        $result->execute();

        if ($result->rowCount()) {
            $row = $result->fetch(PDO::FETCH_ASSOC);

            $json = array(
                'status' => 200,
                'agencia' => $row['agencia'],
                'conta' => $row['id'],
                'saldo' => $row['saldo'],
            );
        } else {
            $json = array(
                'status' => 400,
            );
        }

        return json_encode($json);
    }

    public static function consutarContaPix(string $tipoChave, string $chave)
    {
        global $conn;

        $sel = "SELECT c.agencia, c.id, c.id_user, c.saldo, u.nome
        FROM conta_bancaria c
        LEFT JOIN usuarios u ON u.id = c.id_user
        WHERE $tipoChave = :chave;";
        $result = $conn->prepare($sel);
        $result->bindParam(':chave', $chave);
        $result->execute();

        if ($result->rowCount()) {
            $row = $result->fetch(PDO::FETCH_ASSOC);

            $json = array(
                'status' => 200,
                'iduser' => (int)$row['id_user'],
                'nome' => $row['nome'],
                'agencia' => $row['agencia'],
                'conta' => $row['id'],
                'saldo' => $row['saldo'],
            );
        } else {
            $json = array(
                'status' => 400,
            );
        }

        return json_encode($json);
    }

    public static function transferencia(int $id_user, float $saldo_user, int $id_user_recebedor, float $saldo_user_recebedor, string $tipo_chave_pix, string $chave_pix, string $valor)
    {
        global $conn;
        $valor = str_replace(',', '.', $valor);

        //Debita o valor da conta do usuario logado
        $valor_user = $saldo_user - $valor;

        $upd = "UPDATE conta_bancaria
        SET saldo = :saldo,
        updated_at = NOW()
        WHERE id_user = :id_user;";
        $resultUser = $conn->prepare($upd);
        $resultUser->bindParam(':id_user', $id_user);
        $resultUser->bindParam(':saldo', $valor_user);
        $resultUser->execute();

        if (!$resultUser->rowCount()) {
            $json = array(
                'status' => 400,
            );
            return json_encode($json);
        }

        //Credita o valor na conta do usuario logado
        $valor_user_recebedor = $saldo_user_recebedor + $valor;
        $upd = "UPDATE conta_bancaria
        SET saldo = :saldo,
        updated_at = NOW()
        WHERE id_user = :id_user;";
        $resultUserRecebedor = $conn->prepare($upd);
        $resultUserRecebedor->bindParam(':id_user', $id_user_recebedor);
        $resultUserRecebedor->bindParam(':saldo', $valor_user_recebedor);
        $resultUserRecebedor->execute();

        if (!$resultUserRecebedor->rowCount()) {
            $json = array(
                'status' => 400,
            );
            return json_encode($json);
        }

        //Salva o registro da transferência
        $ins = "INSERT INTO transferencias (id_user, id_user_recebedor, tipo_chave_pix, chave_pix, valor, created_at)
        VALUES ($id_user, :id_user_recebedor, :tipo_chave_pix, :chave_pix, :valor, NOW());";
        $result = $conn->prepare($ins);
        $result->bindParam(':id_user_recebedor', $id_user_recebedor);
        $result->bindParam(':tipo_chave_pix', $tipo_chave_pix);
        $result->bindParam(':chave_pix', $chave_pix);
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
            return json_encode($json);
        }

        return json_encode($json);
    }

    public static function extrato(int $iduser, string $agencia, int $conta)
    {
        global $conn;

        $sel = "SELECT id, valor, created_at, 'Depósito' as 'operacao'
        FROM depositos
        WHERE agencia = :agencia AND conta = :conta
        UNION
        SELECT id, valor, created_at, IF(id_user_recebedor = :iduser, 'Pix recebido', 'Pix feito') as 'operacao'
        FROM transferencias 
        WHERE observacao IS NULL AND (id_user = :iduser OR id_user_recebedor= :iduser)
        ORDER BY created_at DESC;";
        $result = $conn->prepare($sel);
        $result->bindParam(':iduser', $iduser);
        $result->bindParam(':agencia', $agencia);
        $result->bindParam(':conta', $conta);
        $result->execute();

        if ($result->rowCount()) {
            $count = 0;
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $json[$count] = array(
                    'status' => 200,
                    'id' => $row['id'],
                    'valor' => $row['valor'],
                    'operacao' => $row['operacao'],
                    'data' => $row['created_at'],
                );
                $count++;
            }
        } else {
            $json = array(
                'status' => 400,
            );
        }

        return json_encode($json);
    }

    public static function devolucaoPix(int $id)
    {
        global $conn;

        //Consulta os dados do pix feito
        $sel = "SELECT id, id_user, id_user_recebedor, valor FROM transferencias WHERE id = :id;";
        $result = $conn->prepare($sel);
        $result->bindParam(':id', $id);
        $result->execute();

        if (!$result->rowCount()) {
            $json = array(
                'status' => 400,
                'msg' => 'Erro na solicitação de devolução de pix, tente novamente!'
            );

            return json_encode($json);
        } else {
            $transferencia = $result->fetch(PDO::FETCH_ASSOC);
        }

        //Consulta os dados bancarios do usuario que recebeu o pix
        $userRecebedor = "SELECT saldo FROM conta_bancaria WHERE id_user = :id_user_recebedor;";
        $resultUserRecebedor = $conn->prepare($userRecebedor);
        $resultUserRecebedor->bindParam(':id_user_recebedor', $transferencia['id_user_recebedor']);
        $resultUserRecebedor->execute();

        if (!$resultUserRecebedor->rowCount()) {
            $json = array(
                'status' => 400,
                'msg' => 'Erro na solicitação de devolução de pix, tente novamente!'
            );

            return json_encode($json);
        } else {
            $saldo_user_recebedor = $resultUserRecebedor->fetch(PDO::FETCH_ASSOC);
        }

        //Debita o valor da conta do usuario que recebeu o pix
        $valor_user_recebedor = $saldo_user_recebedor['saldo'] - $transferencia['valor'];

        $upd = "UPDATE conta_bancaria
        SET saldo = :saldo_recebedor,
        updated_at = NOW()
        WHERE id_user = :id_user_recebedor;";
        $resultUserRecebedorUpd = $conn->prepare($upd);
        $resultUserRecebedorUpd->bindParam(':id_user_recebedor', $transferencia['id_user_recebedor']);
        $resultUserRecebedorUpd->bindParam(':saldo_recebedor', $valor_user_recebedor);
        $resultUserRecebedorUpd->execute();

        if (!$resultUserRecebedorUpd->rowCount()) {
            $json = array(
                'status' => 400,
                'msg' => 'Erro na solicitação de devolução de pix, tente novamente!'
            );
            return json_encode($json);
        }

        //Consulta os dados bancario do usuario que solicitou a devolução do pix
        $user = "SELECT saldo FROM conta_bancaria WHERE id_user = :id_user;";
        $resultUser = $conn->prepare($user);
        $resultUser->bindParam(':id_user', $transferencia['id_user']);
        $resultUser->execute();

        if (!$resultUser->rowCount()) {
            $json = array(
                'status' => 400,
                'msg' => 'Erro na solicitação de devolução de pix, tente novamente!'
            );

            return json_encode($json);
        } else {
            $saldo_user = $resultUser->fetch(PDO::FETCH_ASSOC);
        }

        //Credita o valor na conta do usuario que solicitou a devolução do pix
        $valor_user = $saldo_user['saldo'] + $transferencia['valor'];
        $upd = "UPDATE conta_bancaria
        SET saldo = :saldo,
        updated_at = NOW()
        WHERE id_user = :id_user;";
        $resultUserUpd = $conn->prepare($upd);
        $resultUserUpd->bindParam(':id_user', $transferencia['id_user']);
        $resultUserUpd->bindParam(':saldo', $valor_user);
        $resultUserUpd->execute();

        if (!$resultUserUpd->rowCount()) {
            $json = array(
                'status' => 400,
                'msg' => 'Erro na solicitação de devolução de pix, tente novamente!'
            );
            return json_encode($json);
        }

        $updTransferencia = "UPDATE transferencias
        SET observacao = 'Devolvida',
        updated_at = NOW()
        WHERE id = :id;";
        $resultUserUpdTransferencia = $conn->prepare($updTransferencia);
        $resultUserUpdTransferencia->bindParam(':id', $id);
        $resultUserUpdTransferencia->execute();
        if (!$resultUserUpdTransferencia->rowCount()) {
            $json = array(
                'status' => 400,
                'msg' => 'Erro na solicitação de devolução de pix, tente novamente!'
            );
            return json_encode($json);
        }

        $json = array(
            'status' => 200,
        );
        return json_encode($json);
    }
}
