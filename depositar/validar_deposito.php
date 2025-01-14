<?php
require $_SERVER['DOCUMENT_ROOT'] . '/inc/def.php';
require $_SERVER['DOCUMENT_ROOT'] . '/header.php';

if (empty($_POST['nome']) || empty($_POST['email']) || empty($_POST['telefone']) || empty($_POST['agencia']) || empty($_POST['valor']) || empty($_POST['conta'])) {
    $_SESSION['msg'] = 'Erro: Campos obrigatórios não preenchidos!';
    voltar();
}

//Valida se é um email válido
if (!validaEmail($_POST['email'])) {
    $_SESSION['msg'] = 'Formato do e-mail é inválido!';
    voltar();
}

if ($_POST['valor'] < 20) {
    $_SESSION['msg'] = 'Erro: Valor mínimo para depósito é de R$ 20,00!';
    voltar();
}
?>
<div class="wrapper-page">
    <div class="card bg-light col-md-6 m-auto">

        <div class="card-body mt-30">
            <div class="form-group col-md-12">
                <h4>Validar Depósito</h4>
            </div>
            <div class="valida_form form-group col-md-12">
                <div class="accordion" id="accordionExample">
                    <div class="card">
                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                            <div class="card-body">
                                <h5>Conta que receberá o depósito:</h5>
                                <hr>
                                <form class="form mt-20" action="depositar.php" method="POST">
                                    <h6>Agência: <?php echo $_POST['agencia']; ?></h6>
                                    <h6>Conta: <?php echo $_POST['conta']; ?></h6>
                                    <?php $contain = strpos($_POST['valor'], ',') ?>
                                    <h6>Valor: R$ <?php echo $contain === false ? $_POST['valor'] . ',00' : $_POST['valor']; ?></h6>

                                    <input class="form-control" type="hidden" name="nome" value="<?php echo $_POST['nome'] ?>">
                                    <input class="form-control" type="hidden" name="email" value="<?php echo $_POST['email'] ?>">
                                    <input class="form-control" type="hidden" name="telefone" value="<?php echo $_POST['telefone'] ?>">
                                    <input class="form-control" type="hidden" name="agencia" value="<?php echo $_POST['agencia'] ?>">
                                    <input class="form-control" type="hidden" name="conta" value="<?php echo $_POST['conta'] ?>">
                                    <input class="form-control" type="hidden" name="valor" value="<?php echo $_POST['valor'] ?>">
                                    <hr>
                                    <div class="form-row text-center">
                                        <div class="form-group col-md-6">
                                            <a href="/depositar" class="btn btn-danger submit">Cancelar</a>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <button class="btn btn-primary submit" type="submit">Transferir</button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end wrapper page -->
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/footer.php';
?>
<script>
    jQuery(document).ready(function($) {})
</script>