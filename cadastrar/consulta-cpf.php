<?php
require $_SERVER['DOCUMENT_ROOT'] . '/inc/def.php';

if (consultaCpf($_POST['cpf'])) {
    $json = array(
        'status' => 200
    );
} else {
    $json = array(
        'status' => 400
    );
}

echo json_encode($json);
