<?php
require $_SERVER['DOCUMENT_ROOT'] . '/inc/def.php';
require $_SERVER['DOCUMENT_ROOT'] . '/header.php';
?>
<div class="wrapper-page">
    <?php if (!empty($_SESSION['msg'])) {
        $erro = explode(':', $_SESSION['msg']);
    ?>
        <div class="alert alert-<?php echo $erro[0] == 'Erro' ? 'danger' : 'success'; ?> alert-dismissible fade show col-md-12 mx-auto" role="alert">
            <?php echo $_SESSION['msg']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php } ?>
    <div class="card bg-light col m-auto">

        <div class="card-body mt-30">
            <h4>Realizar Depósito</h4>
            <hr>
            <form class="form-row mt-20" action="validar_deposito.php" method="POST">
                <div class="form-group col-md-12">
                    <h6>Dados para contato</h6>
                </div>
                <div class="form-group col-md-6">
                    <input class="form-control <?php echo $erro[0] == 'Erro' ? 'is-invalid' : ''; ?>" type="text" placeholder="Nome Completo" name="nome">
                </div>

                <div class="form-group col-md-4">
                    <input class="form-control <?php echo $erro[0] == 'Erro' ? 'is-invalid' : ''; ?>" type="email" placeholder="E-mail" name="email">
                </div>

                <div class="form-group col-md-2">
                    <input class="form-control <?php echo $erro[0] == 'Erro' ? 'is-invalid' : ''; ?>" type="text" placeholder="Telefone" name="telefone">
                </div>

                <div class="form-group col-md-12">
                    <hr>
                    <h6>Dados para depósito</h6>
                </div>

                <div class="form-group col-md-4">
                    <input class="form-control <?php echo $erro[0] == 'Erro' ? 'is-invalid' : ''; ?>" type="number" placeholder="Agência" min="0" name="agencia">
                </div>
                <div class="form-group col-md-4">
                    <input class="form-control <?php echo $erro[0] == 'Erro' ? 'is-invalid' : ''; ?>" type="number" placeholder="Conta" min="0" name="conta">
                </div>
                <div class="form-group col-md-4">
                    <input class="form-control <?php echo $erro[0] == 'Erro' ? 'is-invalid' : ''; ?>" type="text" placeholder="Valor" name="valor">
                </div>

                <div class="form-group col-md-12">
                    <hr>
                </div>

                <div class="form-group col-md-12">
                    <input class="btn btn-primary btn-block waves-effect waves-light text-center col-md-4 submit" type="submit" value="Cadastrar">
                </div>

                <div class="form-group col-md-12">
                    <div class="col-xs-12">
                        Possui conta? <a href="/">Acessar conta</a>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <!-- end card-box-->

</div>
<!-- end wrapper page -->
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/footer.php';
?>
<script>
    jQuery(document).ready(function($) {
        jQuery(document).ready(function($) {
            $('input[name=telefone]').mask('(00) 00000-0000');
        })

        $("input[name=valor]").mask('000.000.000.000.000,00', {
            reverse: true
        })
    })
</script>