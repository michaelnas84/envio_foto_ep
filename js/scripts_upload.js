    $("#input_cpf").on('propertychange input', function (e) {
        var cpf = $(this).val().replace(/[^\d]/g, "")

        limpa_campos()

        if (cpf.length == '11') {
            pesquisa_cliente(cpf)
            $("#cpf_limpo").val(cpf)
        }

        cpf_mascara = cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4")
        $(this).val(cpf_mascara)
    })

    function limpa_campos() {
        $("#nome, #nome_mae, #nascimento, #arquivo, #tipo_documento").val("")
        $("#tipo_documento").val("0").change()
        $("#foto_anexada").addClass("hidden")
        $("#section_face_documento").addClass("hidden")

        $("input:radio[name='face_documento']").each(function () {
            $(this).prop('checked', false);
        })


        $(".option_list").each(function () {
            if($(this).is(':contains(" ✓")')) {
                $(this).text($(this).text().replace(" ✓", ""))
            }
        })
    }

    $("#tipo_documento").change(function () {
        if (this.value === '1'){
            $("#section_face_documento").removeClass("hidden")
            $("input:radio[name='face_documento']").each(function () {
                if (this.value === '3'){
                    $(this).prop('checked', true)
                }
            })
            return false
        }
        $("#section_face_documento").addClass("hidden")
        $("input:radio[name='face_documento']").each(function () {
            $(this).prop('checked', false)
        })
    })

    $("#arquivo").change(function () {
        if (this.value) {
            $("#foto_anexada").removeClass("hidden")
        }
    })

    $("#tirar_foto").click(function () {
        $("#arquivo").click()
    })

    function pesquisa_cliente(cpf) {
        fetch('admin/central_controller.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `acao=pesquisa_cpf&cpf=${cpf}`
        })
            .then(async response => {
                try {
                    const retorno = await response.text()
                    if (retorno == 'null') {
                        alert('CPF inválido')
                        return false
                    }
                    const json_retorno = JSON.parse(retorno)
                    var nome = json_retorno['NOME']
                    var nome_mae = json_retorno['MAE']
                    var data_nascimento = json_retorno['NASCEU']
                    var tipo_documento = json_retorno['ID_TIPDOC']
                    exibe_dados(nome, nome_mae, data_nascimento, tipo_documento)

                } catch (error) {
                    alert('Erro para consultar cpf. Favor abrir chamado para Intranet')
                    console.error(error)
                    return false
                }
            })
    }

    function exibe_dados(nome, nome_mae, data_nascimento, tipo_documento) {
        var data_nascimento = data_nascimento.split("-")

        $("#nome").val(nome)
        $("#nome").html(nome)
        $("#nome_mae").val(nome_mae)
        $("#nome_mae").html(nome_mae)
        $("#nascimento").val(data_nascimento)
        $("#nascimento").html(data_nascimento)

        if (tipo_documento) {
            tipo_documento = tipo_documento.split(" ")
            tipo_documento.forEach(marca_checks)
        }
    }

    function marca_checks(item) {
        if($(`#option_list_${item}`).is(':not(:contains(" ✓"))')) {
            $(`#option_list_${item}`).append(" ✓")
        }
    }


    $("#button_imprimir").click(function () {
        var form = $('#form')[0]
        var data = new FormData(form)
        $.ajax({
            type: "POST",
            enctype: "multipart/form-data",
            url: "admin/central_controller.php",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            success: function (retorno) {
                alert(retorno)
                if (retorno === "Arquivo salvo com sucesso!") {
                    var tipo_doc_envio = $("#tipo_documento").val()
                    if($(`#option_list_${tipo_doc_envio}`).is(':not(:contains(" ✓"))')) {
                        $(`#option_list_${tipo_doc_envio}`).append(" ✓")
                    }
                    $("input:radio[name='face_documento']").each(function () {
                        $(this).prop('checked', false);
                    })
                    $("#arquivo").val("")
                    $("#foto_anexada").addClass("hidden")
                    $("#tipo_documento").val("0").change()
                }
            },
            error: function (error) {
                alert('Erro ao gravar item! Tente novamente. Se persistir, abrir chamado para Intranet')
                console.error(error)
            },
        })
    })
