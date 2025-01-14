<?php
require $_SERVER['DOCUMENT_ROOT'] . '/inc/def.php';
require $_SERVER['DOCUMENT_ROOT'] . '/header.php';
?>
<div class="wrapper-page">

    <?php if (!empty($_SESSION['msg'])) { ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['msg']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php } ?>
    <div class="card bg-light col m-auto">

        <div class="card-body mt-30">
            <h4>Criar Conta</h4>
            <form class="form-row mt-20" action="insere.php" method="POST">

                <div class="form-group col-md-6">
                    <input class="form-control <?php echo !empty($_SESSION['msg']) ? 'is-invalid' : ''; ?>" type="text" placeholder="Nome Completo" name="nome">
                </div>

                <div class="form-group col-md-3">
                    <input class="form-control <?php echo !empty($_SESSION['msg']) ? 'is-invalid' : ''; ?>" type="text" placeholder="Data de nascimento" name="datanasc">
                </div>

                <div class="form-group col-md-3">
                    <input class="form-control <?php echo !empty($_SESSION['msg']) ? 'is-invalid' : ''; ?>" type="text" placeholder="Telefone" name="telefone">
                </div>

                <div class="form-group col-md-6">
                    <input class="form-control <?php echo !empty($_SESSION['msg']) ? 'is-invalid' : ''; ?>" type="email" placeholder="E-mail" name="email">
                </div>

                <div class="form-group col-md-6">
                    <input class="form-control <?php echo !empty($_SESSION['msg']) ? 'is-invalid' : ''; ?>" type="text" placeholder="CPF" name="cpf">
                </div>

                <div class="form-group col-md-2">
                    <input class="form-control <?php echo !empty($_SESSION['msg']) ? 'is-invalid' : ''; ?>" type="text" placeholder="CEP" name="cep">
                </div>

                <div class="form-group col-md-5">
                    <input class="form-control <?php echo !empty($_SESSION['msg']) ? 'is-invalid' : ''; ?>" type="text" placeholder="Logradouro" name="logradouro">
                </div>

                <div class="form-group col-md-2">
                    <input class="form-control <?php echo !empty($_SESSION['msg']) ? 'is-invalid' : ''; ?>" type="text" placeholder="Número" name="numero">
                </div>

                <div class="form-group col-md-3">
                    <input class="form-control" type="text" placeholder="Complemento" name="complemento">
                </div>

                <div class="form-group col-md-4">
                    <input class="form-control <?php echo !empty($_SESSION['msg']) ? 'is-invalid' : ''; ?>" type="text" placeholder="Bairro" name="bairro">
                </div>

                <div class="form-group col-md-4">
                    <input class="form-control <?php echo !empty($_SESSION['msg']) ? 'is-invalid' : ''; ?>" type="text" placeholder="Cidade" name="cidade">
                </div>

                <div class="form-group col-md-4">
                    <input class="form-control <?php echo !empty($_SESSION['msg']) ? 'is-invalid' : ''; ?>" type="text" placeholder="Estado" name="estado">
                </div>

                <div class="form-group col-md-6">
                    <input class="form-control <?php echo !empty($_SESSION['msg']) ? 'is-invalid' : ''; ?>" type="password" placeholder="senha" name="senha">
                </div>

                <div class="form-group col-md-6">
                    <input class="form-control <?php echo !empty($_SESSION['msg']) ? 'is-invalid' : ''; ?>" type="password" placeholder="Confirmar senha" name="confirmasenha">
                </div>

                <div class="form-group col-md-12 msgSenha d-none">
                    <div class="alert alert-dismissible fade show" role="alert">
                        <div class="text-alert"></div>
                    </div>
                </div>

                <div class="form-group col-md-12">
                    <input class="btn btn-primary btn-bordred btn-block waves-effect waves-light text-center col-md-4 submit" type="submit" value="Cadastrar" disabled>
                </div>

                <div class="form-group col-md-12">
                    <div class="col-xs-12">
                        Possui conta? <a href="/">Acessar conta</a>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/footer.php';
?>
<script>
    jQuery(document).ready(function($) {
        $('input[name=datanasc]').mask('00/00/0000');

        $("input[name=datanasc]").datepicker({
            format: 'dd/mm/yyyy',
            language: 'pt-BR'
        });

        $('input[name=cpf]').mask('000.000.000-00');
        $('input[name=telefone]').mask('(00) 00000-0000');
        $('input[name=cep]').mask('00000-000');

        $('input[name=cpf]').focusout(function() {
            var cpf = $(this).val();
            $.ajax({
                type: "post",
                data: {
                    "cpf": cpf
                },
                url: "consulta-cpf.php",
                datatype: 'json',
                success: function(response) {
                    var obj = JSON.parse(response)
                    if (obj.status != 200) {
                        Swal.fire({
                            type: 'error',
                            title: 'CPF inválido!',
                            text: 'Por favor digite o um CPF válido',
                            showCloseButton: true,
                            showConfirmButton: false,
                            timer: 10000,
                        })
                        $('input[name=cpf]').val('');
                    }
                }
            })
        });

        $('input[name=cep]').focusout(function() {
            var cepField = $(this).val();
            var cep = cepField.replace("-", "")

            if (cep.length == 8) {
                Swal.fire({
                    type: 'info',
                    title: 'Buscando endereço, aguarde...',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    showCloseButton: true,
                    showConfirmButton: false,
                });
                Swal.showLoading();
                $.ajax({
                    type: "post",
                    data: {
                        "cep": cep
                    },
                    url: "consulta-cep.php",
                    datatype: 'json',
                    success: function(response) {
                        var obj = JSON.parse(response)
                        if (obj.cep) {
                            Swal.close();
                            $('input[name=logradouro], input[name=numero], input[name=complemento], input[name=bairro], input[name=cidade], input[name=estado]').val('');
                            $('input[name=logradouro]').val(obj.logradouro);
                            $('input[name=complemento]').val(obj.complemento);
                            $('input[name=bairro]').val(obj.bairro);
                            $('input[name=cidade]').val(obj.localidade);
                            $('input[name=estado]').val(obj.estado);
                            $('input[name=numero]').focus();
                        } else {
                            Swal.fire({
                                type: 'error',
                                title: 'CEP não encontrado!',
                                text: 'Por favor digite o um CEP válido',
                                showCloseButton: true,
                                showConfirmButton: false,
                                timer: 10000,
                            })
                            $('input[name=logradouro], input[name=numero], input[name=complemento], input[name=bairro], input[name=cidade], input[name=estado]').val('');
                        }
                    }
                });
            }
        })


        $('input[name=confirmasenha]').keyup(function() {
            if ($('input[name=senha]').val() != $('input[name=confirmasenha]').val()) {
                $('.submit').prop("disabled", true);
                $('.msgSenha').removeClass('d-none');
                $('.msgSenha .alert').removeClass('alert-success');
                $('.msgSenha .alert').addClass('alert-danger')
                $('.msgSenha .text-alert').html('Senhas não são iguais!')
            } else {
                $('.submit').prop("disabled", false);
                $('.msgSenha').removeClass('d-none');
                $('.msgSenha .alert').removeClass('alert-danger');
                $('.msgSenha .alert').addClass('alert-success')
                $('.msgSenha .text-alert').html('Senhas iguais!')
            }
        })
    });
</script>