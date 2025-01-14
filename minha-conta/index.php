<?php
require $_SERVER['DOCUMENT_ROOT'] . '/inc/valida_token.php';
require $_SERVER['DOCUMENT_ROOT'] . '/inc/def.php';
require $_SERVER['DOCUMENT_ROOT'] . '/header.php';

//Captando dados da conta do usuário
$dadosConta = AccountBank::contaUser($_COOKIE['id']);
$dadosConta = json_decode($dadosConta);

//Retorna as transferências e depósitos do usuário
$extrato = AccountBank::extrato($_COOKIE['id'], $dadosConta->agencia, $dadosConta->conta);
$extrato = json_decode($extrato);
?>
<div class="wrapper-page">
    <?php if (!empty($_SESSION['msg'])) {
        $erro = explode(':', $_SESSION['msg']);
    ?>
        <div class="alert alert-<?php echo $erro[0] == 'Erro' ? 'danger' : 'success'; ?> alert-dismissible fade show col-md-8 mx-auto" role="alert">
            <?php echo $_SESSION['msg']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php } ?>
    <div class="card bg-light col-md-8 m-auto">

        <div class="card-body mt-30">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <h6>Olá <?php echo $_COOKIE['nome'] ?></h6>
                    <h6>Agência: <?php echo $dadosConta->agencia ?></h6>
                    <h6>Conta: <?php echo $dadosConta->conta ?></h6>
                </div>

                <div class="form-group col-md-4">
                    <?php $color = ($dadosConta->saldo > 0 ? 'color:green' : ($dadosConta->saldo < 0 ? 'color:red' : 'color:#333')); ?>
                    <h6>Saldo <span style="<? echo $color ?>">R$ <?php echo number_format($dadosConta->saldo, 2, ",", "."); ?></span></h6>
                </div>

                <div class="form-group col-md-2">
                    <a class="btn btn-danger" href="logout.php">Sair</a>
                </div>
            </div>
            <div class="form-group col-md-12">
                <hr>
                <div class="accordion" id="accordionExample">
                    <div class="form-group">
                        <button class="btn btn-outline-secondary colap" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            Transferência
                        </button>
                        <button class="btn btn-outline-secondary colap" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Extrato
                        </button>
                    </div>

                    <div class="card">
                        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                            <div class="card-body">
                                <h5>Transferência via Pix</h5>
                                <form class="form-row mt-20" action="validar_transferencia.php" method="POST">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="cpf">
                                        <label class="form-check-label" for="inlineRadio1">CPF</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="email">
                                        <label class="form-check-label" for="inlineRadio2">E-mail</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="telefone">
                                        <label class="form-check-label" for="inlineRadio3">Telefone</label>
                                    </div>
                                    <input class="form-control" type="text" placeholder="Digite a chave pix da conta" name="pix"><br><br>
                                    <input class="form-control" type="text" placeholder="Digite o valor" name="valor"><br><br>
                                    <input class="btn btn-primary btn-bordred btn-block waves-effect waves-light text-center col-md-4 submit" type="submit" value="Transferir">
                                </form>
                            </div>
                        </div>
                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                            <div class="card-body">
                                <h5>Extratos</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th scope="col">Operação</th>
                                                <th scope="col">Valor</th>
                                                <th scope="col">Efetuado</th>
                                                <th scope="col"></th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            if ($extrato->status != 400) {
                                                foreach ($extrato as $row) {
                                                    $color = $row->operacao == 'Pix feito' ? 'style="color:red"' : 'style="color:green"';
                                                    $menos = $row->operacao == 'Pix feito' ? '- ' : '';
                                                    $button = $row->operacao == 'Pix feito' ? '' : 'd-none';
                                                    $valor = number_format($row->valor, 2, ",", ".");
                                            ?>
                                                    <tr id="row_<?php echo $row->id; ?>">
                                                        <td <?php echo $color; ?>><?php echo $row->operacao; ?></td>
                                                        <td <?php echo $color; ?>><?php echo $menos . 'R$ ' . $valor; ?></td>
                                                        <td><?php echo date('d/m/Y H:i:s', strtotime($row->data)); ?></td>
                                                        <td><button class="btn btn-danger btn-sm devolucao <?php echo $button; ?>" data-id="<?php echo $row->id; ?>" data-valor="<?php echo $valor; ?>" data-toggle="tooltip" title="Solicitar Devolução"><i class="fa fa-retweet"></i></button></td>
                                                    </tr>
                                                <?php
                                                }
                                            } else {
                                            ?>
                                                <tr>
                                                    <td class="text-center" colspan="4">Sem dados</td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
        $('[data-toggle="tooltip"]').tooltip({
            trigger: 'hover'
        });

        $('input[name=inlineRadioOptions]').click(function() {
            $('input[name=pix]').val('');
            var radio = $(this).val();
            if (radio == 'cpf') {
                jQuery(document).ready(function($) {
                    $('input[name=pix]').attr("placeholder", "Digite um CPF");
                    $('input[name=pix]').mask('000.000.000-00');
                })
            }
            if (radio == 'email') {
                jQuery(document).ready(function($) {
                    $('input[name=pix]').attr("placeholder", "Digite um e-mail");
                    $('input[name=pix]').unmask();
                })
            }
            if (radio == 'telefone') {
                jQuery(document).ready(function($) {
                    $('input[name=pix]').attr("placeholder", "Digite um telefone");
                    $('input[name=pix]').mask('(00) 00000-0000');
                })
            }
        })

        $("input[name=valor]").mask('000.000.000.000.000,00', {
            reverse: true
        })

        $(".devolucao").click(function() {
            var id = $(this).data("id");
            var valor = $(this).data("valor");

            if (confirm("Você tem certeza que quer solicitar a devolução do pix no valor de R$ " + valor)) {
                jQuery.ajax({
                    type: "GET", // HTTP method POST or GET
                    url: "devolucao_pix.php", //Where to make Ajax calls
                    dataType: "json", // Data type, HTML, json etc.
                    data: {
                        id: id
                    }, //Form variables
                    success: function(json) {
                        if (json.status == 200) {
                            location.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: json.msg
                            })
                        }
                    }
                });
            }
        });
    })
</script>