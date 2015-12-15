<?php
require_once('includes/phpvortex/DB_MySQL.class.php');
require_once('includes/conn.php');

$db = new DB_MySQL($db_conn);
$db->Connect();

$db->Query("SET NAMES 'latin1'");
$db->Query("SET CHARACTER SET latin1");

setlocale (LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");

session_start();

if (empty($_SESSION['user']) || empty($_SESSION['ip']) || ($_SESSION['ip'] != $_SERVER['REMOTE_ADDR']) || ($_SESSION['user']['usu_nivel'] > 1)) {
	header("Location: index.php");
	exit();
}

header("Content-type: text/comma-separated-values; charset=windows-1252");
header("Content-Disposition: attachment; filename=relatorio.csv");

$filtro = '';
if (!empty($_REQUEST['ev'])) {
	$filtro .= (empty($filtro)?'':' AND ')."ev_id = '".$db->AddSlashes($_REQUEST['ev'])."'";
}
if (!empty($_REQUEST['usu'])) {
	$filtro .= (empty($filtro)?'':' AND ')."usu_id = '".$db->AddSlashes($_REQUEST['usu'])."'";
}
if (!empty($_REQUEST['nd']) && !empty($_REQUEST['nm']) && !empty($_REQUEST['na'])) {
	if (is_numeric($_REQUEST['na'])) {
		$d = intval($_REQUEST['nd'], 10);
		$m = intval($_REQUEST['nm'], 10);
		$a = intval($_REQUEST['na'], 10);
		if ($a < 100) {
			$a += 1900;
		}
		$dt = sprintf("%04d-%02d-%02d", $a, $m, $d);
		if (!empty($_REQUEST['nd2']) && !empty($_REQUEST['nm2']) && !empty($_REQUEST['na2'])) {
			if (is_numeric($_REQUEST['na2'])) {
			}
			$d2 = intval($_REQUEST['nd2'], 10);
			$m2 = intval($_REQUEST['nm2'], 10);
			$a2 = intval($_REQUEST['na2'], 10);
			if ($a2 < 100) {
				$a2 += 1900;
			}
			$dt2 = sprintf("%04d-%02d-%02d", $a2, $m2, $d2);
			$filtro .= (empty($find)?'':' AND ')."(DATE(log_dt) >= '$dt' AND DATE(log_dt) <= '$dt2') ";
		} else {
			$filtro .= (empty($find)?'':' AND ')."DATE(log_dt) = '$dt'";
		}
	}
}
if (!empty($filtro)) {
	$filtro = 'WHERE '.$filtro;
}
$rs = $db->Query("SELECT log_dt, ev_nome, usu_nome, usu_local, usu_mun, log_det FROM (tb_log NATURAL LEFT JOIN tb_ev_log) NATURAL LEFT JOIN tb_usuario $filtro ORDER BY log_dt DESC LIMIT 20");
echo "Data;Evento;Usuário;Local de trabalho;Município;Detalhes\r\n";
while ($row = $rs->Row()) {
	echo '"'.date("d/m/Y H:i:s", strtotime($row['log_dt'])).'";"'.$row['ev_nome'].'";"'.$row['usu_nome'].'";"'.$row['usu_local'].'";"'.$row['usu_mun'].'";"'.str_replace("\n", " - ", $row['log_det']).'"'."\r\n";
}