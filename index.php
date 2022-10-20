<?php
    session_start();
    session_destroy();
?>
<!doctype html>
<html>
   <head>
      <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <link rel="stylesheet" href="../../css/all.min.css<?= '?'.bin2hex(random_bytes(50))?>">
      <link rel="stylesheet" href="../../css/tailwind.css<?= '?'.bin2hex(random_bytes(50))?>" />
      <script src="../../js/jquery-1.11.1.min.js<?= '?'.bin2hex(random_bytes(50))?>"></script>
      <script src="../../bootstrap/js/sweetalert2/dist/sweetalert2.all.min.js<?= '?'.bin2hex(random_bytes(50))?>"></script>
      <link rel="shortcut icon" href="../../intra_new/img/ICON_Deltasul_laranja.ico">
      <title>Login</title>
   </head>

   <body onLoad="load_error()" >
      <!-- <img src="imagens/fluid.svg" class="fixed hidden lg:block inset-0 h-full" style="z-index: -1; transform: rotate(180deg); opacity: 0.15" /> -->
      <div class="w-screen h-screen flex flex-col justify-center items-center" >
         <form action="../../auth_ad.php?redir=welcome_envio_ep.php" name="form1" method="post" class="form1 flex flex-col justify-center items-center md:w-1/2 bg-primarycolor2 px-16 py-32 rounded">
            <img src="../../login/img/Logo_Deltasul_02.png" class="w-64" />
            <div class="relative mt-8 w-full">
               <i id="user_icon" class="fa fa-user absolute text-primarycolor text-xl py-2 px-4 transition-all duration-500"></i>
               <input type="text" placeholder="Insira seu usuário" name="user" id="user" class="py-2 bg-primarycolor2 rounded-full w-full pl-12 border-b-2 font-display focus:outline-none focus:border-primarycolor transition-all duration-500" />
            </div>
            <div class="relative mt-8 w-full">
               <i id="pass_icon" class="fa fa-lock absolute text-primarycolor text-xl py-2 px-4 transition-all duration-500"></i>
               <input type="password" placeholder="Insira sua senha" name="pass" id="pass" class="py-2 bg-primarycolor2 rounded-full w-full pl-12 border-b-2 font-display focus:outline-none focus:border-primarycolor transition-all duration-500" />
            </div>
            <a onclick="validaLogin()" id="logar" class="cursor-pointer py-3 px-20 bg-primarycolor rounded-full text-white font-bold uppercase text-lg mt-4 transform hover:bg-secondarycolor transition-all duration-500">Login</a>
         </form>
      </div>
   </body>

</html>
<script type="text/javascript">
  function load_error(){
    var url_string = window.location.href;
    var url = new URL(url_string);
    var url_data_redir = url.searchParams.get("redir");

    if(url_data_redir == '1'){
      const Toast = Swal.mixin({
        toast: true,
        position: 'top',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      })
      Toast.fire({
        icon: 'error',
        title: 'Faça login para continuar'
      })       
      return;
      } else if(url_data_redir == '2'){
      const Toast = Swal.mixin({
        toast: true,
        position: 'top',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      })
      Toast.fire({
        icon: 'error',
        title: 'Seu perfil não tem acesso'
      })       
      return;
      }
    }

   function validaLogin(){
     if(document.getElementById('user').value == ''){
       const Toast = Swal.mixin({
         toast: true,
         position: 'top',
         showConfirmButton: false,
         timer: 3000,
         timerProgressBar: true,
         didOpen: (toast) => {
           toast.addEventListener('mouseenter', Swal.stopTimer)
           toast.addEventListener('mouseleave', Swal.resumeTimer)
         }
       })
   
       Toast.fire({
         icon: 'error',
         title: 'Insira o seu usuário!'
       })
       document.getElementById('user_icon').classList.add("text-red-700");
       document.getElementById('user_icon').classList.remove("text-primarycolor");
       return;
   }else{
     document.getElementById('user_icon').classList.add("text-primarycolor");
     document.getElementById('user_icon').classList.remove("text-red-700");
   }
     if(document.getElementById('pass').value == ''){
       const Toast = Swal.mixin({
         toast: true,
         position: 'top',
         showConfirmButton: false,
         timer: 3000,
         timerProgressBar: true,
         didOpen: (toast) => {
           toast.addEventListener('mouseenter', Swal.stopTimer)
           toast.addEventListener('mouseleave', Swal.resumeTimer)
         }
       })
       Toast.fire({
         icon: 'error',
         title: 'Insira a sua senha!'
       })       
       document.getElementById('pass_icon').classList.add("text-red-700");
       document.getElementById('pass_icon').classList.remove("text-primarycolor");
       return;
        }else{
       document.getElementById('pass_icon').classList.add("text-primarycolor");
       document.getElementById('pass_icon').classList.remove("text-red-700");
        }     
     if(document.getElementById('user').value != '' && document.getElementById('pass').value != ''){
       document.form1.submit();
     }
   }
     jQuery(document.body).on('keypress', function (e) {
     if (e.keyCode === 13) {
         e.preventDefault();
         $("#logar").trigger("click");
     }
   });
</script>