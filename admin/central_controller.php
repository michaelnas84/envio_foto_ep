<?php
    session_start();

    require_once ('../../../../web/Connections/develPDO.php');
    // require_once('../../../../web/Connections/phpPDO.php');

    $cpf_ad                     = $_SESSION["cpf"];
    $cpf                        = $_POST["cpf"];
    $acao                       = $_POST["acao"];
    $tipo_documento             = $_POST["tipo_documento"];
    $face_documento             = $_POST["face_documento"];

    if ($acao == "pesquisa_cpf") {
        echo PesquisaCPF($cpf, $DBOwner3, $PDO);
    }

    if ($acao == "cadastro") {
        if (empty($_POST["nascimento"]) || empty($_POST["nome"]) || empty($_POST["cpf"]) || empty($_POST["tipo_documento"]) || empty($_FILES["arquivo"]['name']) || ($_POST["tipo_documento"] == '1' && empty($_POST["face_documento"]))) {
            echo ("Preencha todos os campos!");
        } else if (empty($_SESSION["cpf"])) {
            echo ("Precisa estar logado para enviar arquivos!");
        } else {

            $totRows = PesquisaDadosJaCadastrados($cpf, $tipo_documento, $face_documento, $DBOwner3, $PDO);

            if ($_FILES['arquivo']['type'] == 'image/jpeg') {
                $file                       = $_FILES['arquivo']['tmp_name'];
                list($width, $height)       = getimagesize($file);
                $image                      = imagecreatefromjpeg($file);
                if ($width > $height) {
                    $new_width = '1280';
                    $new_height = '720';
                } else {
                    $new_width = '720';
                    $new_height = '1280';
                }
                $image                      = imagescale($image, $new_width, $new_height);
                ob_start();
                imagejpeg($image);
                $contents                   = ob_get_contents();
                ob_end_clean();
                $imgData                    = "data:" . $_FILES['arquivo']['type'] . ";base64," . base64_encode($contents);

                echo InsertDados($cpf, $cpf_ad, $imgData, $tipo_documento, $face_documento, $totRows, $DBOwner3, $PDO);
            }

            if ($_FILES['arquivo']['type'] == 'application/pdf') {
                $imgData = base64_encode(file_get_contents($_FILES['arquivo']["tmp_name"]));

                echo InsertDados($cpf, $cpf_ad, $imgData, $tipo_documento, $face_documento, $totRows, $DBOwner3, $PDO);
            }

        }
    }

    /* PESQUISA DADOS DO USUÁRIO */
    function PesquisaCPF($cpf, $DBOwner, $PDO) {
        $sql_cpf = "
            SELECT
                A.NOME                                          AS NOME,
                A.CPF                                           AS CPF,
                TO_CHAR(A.NASCEU, 'DD/MM/YYYY')                 AS NASCEU,
                A.MAE                                           AS MAE,
                (SELECT LISTAGG($DBOwner.CONTATOS_DOCUMENTO.ID_TIPDOC, ' ') WITHIN GROUP (ORDER BY $DBOwner.CONTATOS_DOCUMENTO.ID_TIPDOC) 
                FROM $DBOwner.CONTATOS_DOCUMENTO WHERE $DBOwner.CONTATOS_DOCUMENTO.CK_ATIVO = 'S'AND $DBOwner.CONTATOS_DOCUMENTO.CPF = A.CPF) AS ID_TIPDOC
            FROM
                $DBOwner.CONTATOS A
            WHERE
                A.CPF = '$cpf'
            ";
        // echo '<pre>' . $sql_cpf . '</pre>';
        $stmt = $PDO->query($sql_cpf) or die($PDO->ErrorMsg());
        $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($stmt as $rs_cpf) {
            $JSON['NOME']           = $rs_cpf['NOME'];
            $JSON['NASCEU']         = $rs_cpf['NASCEU'];
            $JSON['MAE']            = $rs_cpf['MAE'];
            $JSON['ID_TIPDOC']      = $rs_cpf['ID_TIPDOC'];
        }

        if (!$JSON['NOME']) {
            return 'null';
            exit;
        }

        return json_encode($JSON);
    }

    /* PESQUISA SE TEM DOCUMENTOS JÁ CADASTRADOS DO USUÁRIO */
    function PesquisaDadosJaCadastrados($cpf, $tipo_documento, $face_documento, $DBOwner, $PDO) {
        $sql = "
            SELECT 
                COUNT(CPF)          AS TOT_ITENS
            FROM
                $DBOwner.CONTATOS_DOCUMENTO

            WHERE
                CPF = '$cpf'
            
            AND
                ID_TIPDOC = $tipo_documento
            ";

        if ($face_documento && $face_documento != '3') {
            $sql .= "
                AND
                    FACE_DOC = $face_documento
                ";
        }
        // echo "<pre>" . $sql . "</pre>";exit;
        $stmt = $PDO->query($sql) or die($PDO->ErrorMsg());
        $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($stmt as $rs) {
            $totRows = $rs['TOT_ITENS'];
        }

        return $totRows;
    }

    /*  EXECUTA SEQUÊNCIA DE TRATAMENTO DADOS ANTERIORES + INSERT
        SE FOR DOCUMENTO 1 OU 4 = DELETA INTRADIA (VERIFICA SE É FRENTE OU VERSO/SE FOR FACE ÚNICA, DELETA OS FRENTE/VERSO) E CK_ATIVO='N' ANTERIORES
        SE FOR DOCUMENTO 2 OU 3 = DELETA TODOS */
    function InsertDados($cpf, $cpf_ad, $imgData, $tipo_documento, $face_documento, $totRows, $DBOwner, $PDO) {
        $PDO->beginTransaction();
        if ($totRows > '0') {
            $sql = "
                    DELETE FROM
                        $DBOwner.CONTATOS_DOCUMENTO
                    WHERE
                        CPF = '$cpf'
                    AND 
                        ID_TIPDOC = $tipo_documento
                ";
            if (($tipo_documento == '1') || ($tipo_documento == '4')) {
                $sql .= "
                    AND 
                        DATA = TRUNC(SYSDATE)
                ";
            }
            if (($face_documento) && ($face_documento != '3')) {
                $sql .= "
                    AND
                        FACE_DOC = $face_documento
                ";
            }
            $stmt = $PDO->query($sql);
            if (!$stmt) {
                $PDO->rollBack();
                die('Erro ao lancar movimento');
            }

            $sql = "
                UPDATE
                    $DBOwner.CONTATOS_DOCUMENTO
                SET
                    CK_ATIVO = 'N'
                WHERE
                    CPF = '$cpf'
                AND 
                    ID_TIPDOC = $tipo_documento
                ";

            if (($face_documento) && ($face_documento != '3')) {
                $sql .= "
                AND
                    FACE_DOC = $face_documento
                ";
            }
            $stmt = $PDO->query($sql);
            if (!$stmt) {
                $PDO->rollBack();
                die('Erro ao lancar movimento');
            }
        }

        /* TROCA FACE DOCUMENTO DE 3 (FACE ÚNICA) PARA 1 (FRENTE) */
        if ($face_documento == '3') { $face_documento = '1'; }

        $sql = "
            INSERT INTO $DBOwner.CONTATOS_DOCUMENTO
            (
                CPF,
                CPF_USUA,
                DATA,
                HORA,
                ID_TIPDOC,
                IMG_DOC,
                CK_ATIVO,
                FACE_DOC
            )
            VALUES
            (
                '$cpf',
                '$cpf_ad',
                TRUNC(SYSDATE),
                TO_CHAR(SYSDATE, 'HH24:MI:SS'),
                $tipo_documento,
                :imgData,
                'S',
                '$face_documento'
            )
            ";
        // echo "<pre>" . $sql . "</pre>";exit;
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':imgData', $imgData, PDO::PARAM_STR, strlen($imgData));
        $stmt->execute();
        if (!$stmt) {
            $PDO->rollBack();
            die('Erro ao lancar movimento');
        }
        $PDO->commit();

        echo ('Arquivo salvo com sucesso!');

    }