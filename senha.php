<?php
require_once('includes/phpvortex/DB_MySQL.class.php');
require_once('includes/conn.php');
require_once('includes/helper.php');

$db = new DB_MySQL($db_conn);
$db->Connect();

$db->Query("SET NAMES 'utf8'");
$db->Query("SET CHARACTER SET utf8");

session_start();

if (empty($_SESSION['user']) || empty($_SESSION['ip']) || ($_SESSION['ip'] != $_SERVER['REMOTE_ADDR'])) {
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
$idCurrent = "Trocar senha";
require('includes/menu.php');
?>
<div class="container">
  <div class="content">
    <img src="img/logo_snaids.gif"/><br/><br/>
<?php
$ok = false;

if (!empty($_REQUEST['senha'])) {
	if ($_REQUEST['nova1'] == '') {
		$erro = 'Por favor digite a nova senha';
	} elseif ($_REQUEST['nova1'] != $_REQUEST['nova2']) {
		$erro = 'Senha nova n&atilde;o confere';
	} elseif ($_REQUEST['senha'] == $_REQUEST['nova1']) {
		$erro = 'Senha nova e antiga s&atilde;o iguais';
	} else {
		$rs = $db->Query("SELECT * FROM tb_usuario WHERE usu_id = {$_SESSION['user']['usu_id']} AND usu_password = '".md5($_REQUEST['senha'])."'");
		if ($rs->RowCount() == 0) {
			$erro = 'Senha antiga n&atilde;o confere';
		} else {
			$db->Query("UPDATE tb_usuario SET usu_password = '".md5($_REQUEST['nova1'])."' WHERE usu_id = {$_SESSION['user']['usu_id']}");
			$ok = true;
		}
	}
}
if (!empty($erro)) {
	echo "<p>$erro</p>";
}
if (!$ok) {
?>
    <div id="cadusu">
    <form action="senha.php" method="post">
    <p><label for="senha">Senha atual:</label><input type="password" name="senha" size="30" /></p>
    <p><label for="nova1">Nova senha:</label><input type="password" name="nova1" size="30" /></p>
    <p><label for="nova2">Confirme a nova senha:</label><input type="password" name="nova2" size="30" /></p>
    <p><input type="submit" value="Trocar senha" /></p>
    </form><br/><br/></div>
<?php } else { ?>
	<p>Senha trocada com sucesso!</p>
<?php } ?>
  </div>
<?php require('includes/rodape.php'); ?>
</div>
</body>
</html>