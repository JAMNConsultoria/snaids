<?php
require_once('includes/phpvortex/DB_MySQL.class.php');
require_once('includes/conn.php');

$db = new DB_MySQL($db_conn);
$db->Connect();

$db->Query("SET NAMES 'utf8'");
$db->Query("SET CHARACTER SET utf8");

session_start();

if (isset($_REQUEST['logout'])) {
	if (!empty($_SESSION['user'])) {
		$db->Query("INSERT INTO tb_log (ev_id, usu_id) VALUES (2, {$_SESSION['user']['usu_id']})");
	}
	$_SESSION['user'] = array();
}
if (isset($_REQUEST['user'])) {
	$rs = $db->Query("SELECT * FROM tb_usuario WHERE usu_login = '".$db->AddSlashes($_REQUEST['user'])."' AND usu_password = '".md5($_REQUEST['password'])."' AND usu_ativo = 1");
	if ($rs->RowCount() == 1) {
		$_SESSION['user'] = $rs->Row();
		$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
		$db->Query("UPDATE tb_usuario SET dt_ult_login = CURRENT_TIMESTAMP WHERE usu_id = {$_SESSION['user']['usu_id']}");
		$db->Query("INSERT INTO tb_log (ev_id, usu_id) VALUES (1, {$_SESSION['user']['usu_id']})");
		header("Location:consulta.php");
		exit();
	} else {
		$erro = "Usu&aacute;rio ou Senha Inv&aacute;lidos";
	}
}
?>
<?php 
//barra do governo full - largura 100%
#require_once ('http://www.saopaulo.sp.gov.br/barrasp/barrasp_full.htm');
require_once ("includes/cabecalho.php");
?>
<body>
<?php
 $idCurrent = "Entrar";
 require('includes/menu.php');
?>
<div class="container">
    
  <div class="content">
    <img src="img/logo_snaids.gif"></img><br/><br/><br/>
    <div class="login">
        <form action="index.php" method="post">
        <p><label for="user" >Usu&aacute;rio:</label><input  class="formLogin"  type="text" name="user" /></p>
        <p><label for="password">Senha:</label><input class="formLogin" type="password" name="password" /></p>
        <p><input type="image" src="img/bt_entra.gif" value="Entrar" /></p>
        </form>
        <?php if (isset($erro)) echo "<p><font color='#FF0000'>$erro</font></p>"; ?>
    </div>
  </div>
    <?php require('includes/rodape.php');?>
</div>
</body>
</html>