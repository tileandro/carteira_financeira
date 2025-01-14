<?php
require $_SERVER['DOCUMENT_ROOT'] . '/inc/valida_token.php';
require $_SERVER['DOCUMENT_ROOT'] . '/inc/def.php';
require $_SERVER['DOCUMENT_ROOT'] . '/header.php';

if (empty($_POST['pix']) || empty($_POST['valor']) || empty($_POST['inlineRadioOptions'])) {
    $_SESSION['msg'] = 'Erro: Chave pix ou valor da tranferência não preenchidos!';
    voltar();
}

$user = AccountBank::consutarContaPix($_POST['inlineRadioOptions'], $_POST['pix']);
$user = json_decode($user);

if ($user->status != 200) {
    $_SESSION['msg'] = 'Erro: Chave pix não vinculada ha uma conta bancária!';
    voltar();
}

if ($user->iduser == $_COOKIE['id']) {
    $_SESSION['msg'] = 'Erro: Não é possível realizar transferênica para a mesma conta bancária! Faça um depósito.';
    voltar();
}

$saldoUserLogado = AccountBank::contaUser($_COOKIE['id']);
$saldoUserLogado = json_decode($saldoUserLogado);

if ((float)$saldoUserLogado->saldo < (float)$_POST['valor']) {
    $_SESSION['msg'] = 'Erro: Saldo insuficiente!';
    voltar();
}
?>
<div class="wrapper-page">
    <div class="card bg-light col-md-6 m-auto">

        <div class="card-body mt-30">
            <div class="form-group col-md-12">
                <h4>Validar Transferência</h4>
            </div>
            <div class="valida_form form-group col-md-12">
                <div class="accordion" id="accordionExample">
                    <div class="card">
                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                            <div class="card-body">
                                <h5>Transferência via Pix para:</h5>
                                <hr>
                                <form class="form mt-20" action="transferir.php" method="POST">
                                    <h6>Nome: <?php echo $user->nome; ?></h6>
                                    <h6>Agência: <?php echo $user->agencia; ?></h6>
                                    <h6>Conta: <?php echo $user->conta; ?></h6>
                                    <h6>Tipo da chave pix: <?php echo $_POST['inlineRadioOptions']; ?></h6>
                                    <h6>Chave pix: <?php echo $_POST['pix']; ?></h6>
                                    <?php $contain = strpos($_POST['valor'], ',') ?>
                                    <h6>Valor: R$ <?php echo $contain === false ? $_POST['valor'] . ',00' : $_POST['valor']; ?></h6>

                                    <input class="form-control" type="hidden" name="saldouserlogado" value="<?php echo $saldoUserLogado->saldo ?>">
                                    <input class="form-control" type="hidden" name="iduserecebedor" value="<?php echo $user->iduser ?>">
                                    <input class="form-control" type="hidden" name="saldouserecebedor" value="<?php echo $user->saldo ?>">
                                    <input class="form-control" type="hidden" name="tipochave" value="<?php echo $_POST['inlineRadioOptions'] ?>">
                                    <input class="form-control" type="hidden" name="pix" value="<?php echo $_POST['pix'] ?>">
                                    <input class="form-control" type="hidden" name="valor" value="<?php echo $_POST['valor'] ?>">
                                    <input class="form-control" type="hidden" name="validasenha" value="0">
                                    <hr>
                                    <div class="form-row text-center">
                                        <div class="form-group col-md-6">
                                            <a href="/minha-conta" class="btn btn-danger submit">Cancelar</a>
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
<!-- Modal -->
<div class="modal fade" id="ExemploModalCentralizado" tabindex="-1" role="dialog" aria-labelledby="TituloModalCentralizado" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="TituloModalCentralizado">Digite sua senha</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger alert-dismissible d-none fade col mx-auto" role="alert">
                    <span class="msg"></span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-inline senha_form">
                    <div class="form-group mx-sm-3 mb-2">
                        <label for="inputPassword2" class="sr-only">Senha</label>
                        <input type="password" class="form-control" id="inputPassword2" placeholder="Senha">
                    </div>
                    <button class="btn btn-primary mb-2 validaSenha">Logar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/footer.php';
?>
<script>
    jQuery(document).ready(function($) {
        $(".valida_form").submit(function(e) {
            if ($('input[name=validasenha]').val() == 0) {
                $('#ExemploModalCentralizado').modal('show');
                e.preventDefault(e);
            }
        });

        $('.validaSenha').click(function(e) {
            e.preventDefault(e);
            var senha = $('#inputPassword2').val();

            if (senha == '') {
                $('.alert').removeClass('d-none')
                $('.alert').addClass('show')
                $('.alert .msg').text('Digite sua senha')

                return false
            }

            $.ajax({
                type: "post",
                data: {
                    "senha": senha
                },
                url: "valida_senha.php",
                datatype: 'json',
                success: function(response) {
                    var obj = JSON.parse(response)
                    if (obj.status == 200) {
                        $('input[name=validasenha]').val('1');
                        $('#ExemploModalCentralizado').modal('hide');
                        $(".submit").click()
                    } else {
                        $('.alert').removeClass('d-none')
                        $('.alert').addClass('show')
                        $('.alert .msg').text('Senha inválida')
                    }
                }
            });
        })
    })
</script>