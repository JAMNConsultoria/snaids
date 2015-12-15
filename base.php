<?php
session_start();

if (empty($_SESSION['user']) || empty($_SESSION['ip']) || ($_SESSION['ip'] != $_SERVER['REMOTE_ADDR']) || ($_SESSION['user']['usu_nivel'] > 1)) {
	header("Location: index.php");
	exit();
}
header('Content-type: text/html; charset=utf-8');
//barra do governo full - largura 100%
#require_once ('http://www.saopaulo.sp.gov.br/barrasp/barrasp_full.htm');
require_once ("includes/cabecalho.php");
if(isset($_REQUEST['btn_upload'])){
    echo "<h1>Arquivo sendo enviado para o servidor, por favor aguarde...</h1>";
}
?>
<body>
<?php
$idCurrent = "Administrar";
require('includes/menu.php');
?>
    <script>
        function alerta(){
            if($("#arq").val()){
                var arquivo = $('#arq').val().split(".")[1].toLowerCase();
                if(arquivo!="dbf"&&arquivo!="zip"){
                    alert("Tipo de arquivo inválido.\nSão aceitos somente arquivos dos tipos: ZIP e/ou DBF.");
                    return false;
                }else{
                    $('#msgUpload').show();
                    return true;
                }
            }else{
              alert("Selecione o arquivo (.zip ou .dbf) para carga.");
              return false;
            }
            
        }
     </script>
<div class="container">
  <div class="content">
    <img src="img/logo_snaids.gif"/><br/><br/>
    <form action="recebearq.php" enctype="multipart/form-data" method="post" onsubmit="return alerta()">
    <p><label for="sistema"><b>SISTEMA:</b> </label><input type="radio" name="sistema" value="N" checked />SINAN NET&nbsp;&nbsp;&nbsp;<input type="radio" name="sistema" value="W" />SINAN Windows</p>
    <p>Arquivo (.dbf ou .zip): <input type="file" id="arq" name="arq" /></p>
    <p><input name="btn_upload"type="submit" value="Enviar" /></p>
    </form><br/><br/>
    <div id="msgUpload">Arquivo sendo enviado, por favor aguarde...<br/><br/></div>
  </div>

    <?php require('includes/rodape.php');?>
</div>
</body>
</html>