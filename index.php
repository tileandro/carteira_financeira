<?php
require $_SERVER['DOCUMENT_ROOT'] . '/inc/def.php';
require $_SERVER['DOCUMENT_ROOT'] . '/header.php';
?>
<div class="wrapper-page">

    <?php if (!empty($_SESSION['msg'])) {
        $erro = explode(':', $_SESSION['msg']);
    ?>
        <div class="alert alert-<?php echo $erro[0] == 'Erro' ? 'danger' : 'success'; ?> alert-dismissible fade show col-md-6 mx-auto" role="alert">
            <?php echo $_SESSION['msg']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php } ?>
    <div class="card bg-light col-md-6 m-auto">

        <div class="card-body mt-30">
            <h4>Acessar conta</h4>
            <form class="form-horizontal mt-20" action="/inc/verifica_login.php" method="POST">

                <div class="form-group ">
                    <div class="col-xs-12">
                        <input class="form-control" type="text" placeholder="CPF" name="cpf" autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12">
                        <input class="form-control" type="password" placeholder="Senha" name="senha">
                    </div>
                </div>

                <div class="form-group text-center m-t-30">
                    <div class="col-xs-12">
                        <button class="btn btn-primary btn-bordred btn-block waves-effect waves-light" type="submit">Entrar</button>
                    </div>
                </div>

                <div class="form-group m-t-30">
                    <div class="col-xs-12">
                        Não tem conta? <a href="cadastrar">Criar conta</a>
                    </div>
                </div>
                <hr>
                <div class="form-group text-center m-t-30">
                    <div class="col-xs-12">
                        <a href="depositar" class="btn btn-secondary btn-bordred btn-block waves-effect waves-light">Depositar</a>
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
        $('input[name=cpf]').mask('000.000.000-00');
    })
</script>