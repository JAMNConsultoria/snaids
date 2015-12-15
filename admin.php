<?php
require_once('includes/phpvortex/DB_MySQL.class.php');
require_once('includes/conn.php');
require_once('includes/helper.php');

session_start();

if (empty($_SESSION['user']) || empty($_SESSION['ip']) || ($_SESSION['ip'] != $_SERVER['REMOTE_ADDR']) || ($_SESSION['user']['usu_nivel'] > 1)) {
	header("Location: index.php");
	exit();
}

header('Content-type: text/html; charset=utf-8');
//barra do governo full - largura 100%
#require_once ('http://www.saopaulo.sp.gov.br/barrasp/barrasp_full.htm');
require_once ("includes/cabecalho.php");
?>
<body>
<?php
$idCurrent = "Administrar";
require('includes/menu.php');
?>
<div class="container">
  <div class="content">
      
        <img src="img/logo_snaids.gif"/><br/><br/>
        <div id="admin">
        <p><a href="usuarios.php">Cadastro de usu&aacute;rios</a></p>
        <p><a href="logs.php">Relat&oacute;rios</a></p>
        <p><a href="base.php">Atualiza&ccedil;&atilde;o das bases de dados</a></p>
      </div>
      <br />
<?php if (!empty($_REQUEST['desativar'])) {
		$db = new DB_MySQL($db_conn);
		$db->Connect();
		$db->Query("UPDATE tb_usuario SET usu_ativo = 0 WHERE DATEDIFF(NOW(), dt_ult_login) > '".$db->AddSlashes($_REQUEST['desativar'])."'"); ?>
        <p>Usuários desativados</p>
<?php } else { ?>
	<form action="admin.php" method="post">
    	<label for="desativar">Desativar usu&aacute;rios que n&atilde;o acessam o sistema por mais de </label>
    	<input type="text" name="desativar" value="365" size="3" /> dias <input type="submit" value="Desativar" />
    </form>
<?php } ?>
  </div>
    <?php require('includes/rodape.php');?>
</div>
</body>
</html>