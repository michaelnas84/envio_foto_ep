<?php 
  // session_start();  
  // if ($_SESSION['department'] == '000'){

      require_once('../../../web/Connections/develADO.php'); 
      // require_once('../../../web/Connections/phpADO.php');
      include("../../../web/include/formatadados.php");

      $CPF          = $_GET['cpf'];
      $ID_TIPDOC    = $_GET['id_tipdoc'];
      $FACE_DOC     = $_GET['face_doc'];

      $sql = "
      SELECT
        IMG_DOC           AS ARQ
      FROM
        $DBOwner3.CONTATOS_DOCUMENTO
      WHERE
        CPF = '$CPF'
      AND
        ID_TIPDOC = $ID_TIPDOC
      AND
        CK_ATIVO = 'S'   
      ";

      if($FACE_DOC){
      $sql .= "
      AND
        FACE_DOC = $FACE_DOC
      ";
      }
      $rs = $phpADO->Execute($sql) or die ($phpADO->ErrorMsg());

      while(!$rs->EOF){
        $base64arq                = $rs->fields('ARQ');
        $rs->MoveNext();
      }
      $rs->Close();
      $totRows = $rs->RecordCount();

      if ($totRows == 0){ echo "<script>alert('Sem dados para os filtros informados.'); window.close(); </script>"; exit; }

      $tipo_arq = substr($base64arq, 11, strpos($base64arq, ';') - 11);

      if($tipo_arq != 'jpeg'){
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename=\"' . $tempfile . '"\";');
        echo file_get_contents('data://application/pdf;base64,' . $base64arq);
      }
?>
<!DOCTYPE html>
<html>
<head>
    <script>
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }
    </script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/pintura.css" />
    <link rel="stylesheet" href="css/styles_pintura.css" />
    <title>EP Deltasul</title>
    <link rel="shortcut icon" href="img/ICON_Deltasul.ico" type="image/x-icon" />
</head>

<body class="p-8 flex justify-center body-bg dark:bg-gray-900">
    <div class="w-full dark:bg-gray-800 rounded-lg">
    <form name="form" id="form" class="form" method="post" enctype="multipart/form-data">
        <div class="shadow sm:rounded-md sm:overflow-hidden">
          <div class="px-4 py-5 space-y-6 sm:p-6">
            <div class="my-editor"></div>

                <script type="module">
                import { appendDefaultEditor } from "./js/pintura.js";

                const pintura = appendDefaultEditor(".my-editor", {
                    // The source image to load
                    src: "<?= $base64arq ?>",

                    // This will set a square crop aspect ratio
                    imageCropAspectRatio: 0,
                });
                </script>
          </div>
        </div>
      </form>
    </div>
    
</body>
</html>
<?php 
// } else { echo ('Você não tem acesso a esta página');}
?>