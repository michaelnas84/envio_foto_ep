<?php 
  session_start();  
  if ($_SESSION['department'] == '000'){

      require_once('../../../web/Connections/develADO.php'); 
      // require_once('../../../web/Connections/phpADO.php');
      include("../../../web/include/formatadados.php");

      $sql = "
      SELECT
        DESCR               AS DESCR,
        ID_TIPDOC           AS ID
      FROM
        $DBOwner3.TIPO_DOCUMENTO
      ";
      $rs 		    = $phpADO->Execute($sql) or die ($phpADO->ErrorMsg());
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
    <link href="css/styles.css" rel="stylesheet">
    <script src="js/jquery-1.11.1.min.js"></script>
    <title>Envio de arquivos EP Deltasul</title>
    <link rel="shortcut icon" href="img/ICON_Deltasul.ico" type="image/x-icon" />
  </head>
  <body class="p-8 flex justify-center body-bg dark:bg-gray-900">
    <div class="w-full dark:bg-gray-800 rounded-lg">
      <form name="form" id="form" class="form" method="post" enctype="multipart/form-data">
        <div class="shadow sm:rounded-md sm:overflow-hidden">
          <div class="px-4 py-5 space-y-6 sm:p-6">
            <div>
              <label for="about" class="block text-sm font-medium text-gray-700 dark:text-gray-100"> CPF </label>
              <div class="mt-1">
                <input id="input_cpf" type="text" class="shadow-sm px-3 focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md" placeholder="xxxxxxxxxxx" maxlength="11" />
              </div>
            </div>
            <input name="cpf" id="cpf_limpo" hidden></input>
            <div>
              <label for="about" class="block text-sm font-medium text-gray-700 dark:text-gray-100"> Nome </label>
              <div class="mt-1">
                <input id="nome" name="nome" readonly type="text" class="shadow-sm px-3 focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md" />
              </div>
            </div>
            <div>
              <label for="about" class="block text-sm font-medium text-gray-700 dark:text-gray-100"> Nome da Mãe </label>
              <div class="mt-1">
                <input id="nome_mae" name="nome_mae" readonly type="text" class="shadow-sm px-3 focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md" placeholder="" />
              </div>
            </div>
            <div>
              <label for="about" class="block text-sm font-medium text-gray-700 dark:text-gray-100"> Data Nascimento </label>
              <div class="mt-1">
                <input id="nascimento" name="nascimento" readonly type="text" class="shadow-sm px-3 focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md" />
              </div>
            </div>
            <div>
              <label for="about" class="block text-sm font-medium text-gray-700 dark:text-gray-100"> Tipo de Documento </label>
              <div class="mt-1">
                <select name="tipo_documento" class=" leading-tight focus:outline-none shadow-sm px-3 focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md" id="tipo_documento">
                  <option value="0" id="option_list_0" selected disabled>Selecione</option>
                  <?php while(!$rs->EOF){ ?>
                    <option value="<?= $rs->fields('ID') ?>" id="option_list_<?= $rs->fields('ID') ?>" class="option_list"><?= $rs->fields('DESCR') ?></option>
                  <?php $rs->MoveNext();} $rs->Close(); ?>
                </select>
              </div>
            </div>
            <div id="section_face_documento" class="hidden">
              <label for="face_documento" class="block text-sm font-medium text-gray-700 dark:text-gray-100"> Face do documento </label>
              <div class="mt-1">
                <div>
                  <div class="space-y-4 flex items-center space-y-0 space-x-10">
                    <div class="flex w-1/4 items-center">
                      <input name="face_documento" type="radio" value="3" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                      <label for="face_documento" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-100">Face Única</label>
                    </div>
                    <div class="flex w-1/4 items-center">
                      <input name="face_documento" type="radio" value="1" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                      <label for="face_documento" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-100">Frente</label>
                    </div>
                    <div class="flex w-1/4 items-center">
                      <input name="face_documento" type="radio" value="2" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                      <label for="face_documento" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-100">Verso</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="flex w-full">
              <div class="flex w-full flex-wrap pr-4">
                <label class="w-full block text-sm font-medium text-gray-700 dark:text-gray-100"> Foto/Arquivo </label>
                <div class="mt-1 flex flex-wrap items-center w-full" style="flex-wrap: wrap">
                  <div id="tirar_foto" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-800 hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900 w-full cursor-pointer">Anexar Foto/Arquivo</div>
                  <a id="foto_anexada" readonly type="text" class="hidden shadow-sm px-3 focus:ring-blue-500 focus:border-blue-500 mt-1 w-full sm:text-sm border border-gray-300 rounded-md">Foto/Arquivo anexado</a>
                </div>
              </div>
            </div>
          <input class="hidden" type="file" name="arquivo" id="arquivo" />
          <input class="hidden" value="cadastro" name="acao" />
      </form>
      <div class="flex justify-between px-4 py-3 bg-gray-50 dark:bg-gray-800 sm:px-6">
        <button class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-800 hover:bg-green-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-900">Limpar</button>
        <a class="cursor-pointer inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-800 hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900" id="button_imprimir">Enviar</a>
      </div>
    </div>
    <script src="js/scripts_upload.js"></script>
  </body>
</html>
<?php 
 } else { 
  header("Location: index.php?redir=1");
  die();
 }
?>