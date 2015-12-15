<?php
require_once('includes/phpvortex/DB_MySQL.class.php');
require_once('includes/conn.php');
require_once('includes/helper.php');

$db = new DB_MySQL($db_conn);
$db->Connect();

$db->Query("SET NAMES 'utf8'");
$db->Query("SET CHARACTER SET utf8");

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
    <script type="text/javascript">
$(document).ready(function(){
    $('table#resultado tbody tr:odd').css('background','#C3DAE2');

    $('table#resultado tbody tr').hover(
    function(){
        $(this).addClass('destaque');
    },
    function(){
        $(this).removeClass('destaque');
    }
    );
});
</script>
<?php
$idCurrent = "Administrar";
require('includes/menu.php');
?>
<div class="container">
  <div class="content">
    <img src="img/logo_snaids.gif"/><br/><br/>
    <form id="frm_filtro" action="logs.php" method="post">
    <input type="hidden" name="pg" value="<?php echo isset($_REQUEST['pg'])?$_REQUEST['pg']:1; ?>" />
    <p><label for="ev">Tipo de Evento: </label>
    	<select name="ev">
        	<option value="0">Qualquer</option>
<?php
	$rs = $db->Query("SELECT * FROM tb_ev_log ORDER BY ev_id");
	while ($row = $rs->Row()) { ?>
        	<option value="<?php echo $row['ev_id']; ?>" <?php if (isset($_REQUEST['ev']) && ($row['ev_id'] == $_REQUEST['ev'])) echo 'selected'; ?>><?php echo $row['ev_nome']; ?></option>
<?php } ?>
        </select></p>
    <p><label for="usu">Usuário: </label>
    	<select name="usu">
        	<option value="0">Qualquer</option>
<?php
	$rs = $db->Query("SELECT usu_id, usu_nome FROM tb_usuario ORDER BY usu_nivel, usu_nome");
	while ($row = $rs->Row()) { ?>
        	<option value="<?php echo $row['usu_id']; ?>" <?php if (isset($_REQUEST['usu']) && ($row['usu_id'] == $_REQUEST['usu'])) echo 'selected'; ?>><?php echo $row['usu_nome']; ?></option>
<?php } ?>
        </select></p>
    <p><label for="nd">Data do evento entre </label>
    <select name="nd">
		<option value="0">-</option>
<?php for ($i = 1; $i < 32; $i++) { ?>    
		<option value="<?php echo $i; ?>" <?php if (isset($_REQUEST['nd']) && ($_REQUEST['nd'] == $i)) echo 'selected'; ?>><?php echo $i; ?></option>
<?php } ?>    
    </select>
    <select name="nm">
		<option value="0">-</option>
<?php 
	$meses = array('Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez');
	for ($i = 0; $i < 12; $i++) { ?>    
		<option value="<?php echo $i + 1; ?>" <?php if (isset($_REQUEST['nm']) && ($_REQUEST['nm'] == ($i + 1))) echo 'selected'; ?>><?php echo $meses[$i]; ?></option>
<?php } ?>    
    </select>
    <input type="text" name="na" size="4" value="<?php if (isset($_REQUEST['na'])) echo $_REQUEST['na']; ?>" />
    e
    <select name="nd2">
		<option value="0">-</option>
<?php for ($i = 1; $i < 32; $i++) { ?>    
		<option value="<?php echo $i; ?>" <?php if (isset($_REQUEST['nd2']) && ($_REQUEST['nd2'] == $i)) echo 'selected'; ?>><?php echo $i; ?></option>
<?php } ?>    
    </select>
    <select name="nm2">
		<option value="0">-</option>
<?php 
	$meses = array('Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez');
	for ($i = 0; $i < 12; $i++) { ?>    
		<option value="<?php echo $i + 1; ?>" <?php if (isset($_REQUEST['nm2']) && ($_REQUEST['nm2'] == ($i + 1))) echo 'selected'; ?>><?php echo $meses[$i]; ?></option>
<?php } ?>    
    </select>
    <input type="text" name="na2" size="4" value="<?php if (isset($_REQUEST['na2'])) echo $_REQUEST['na2']; ?>" /></p>
    <p><input type="button" value="Filtrar" onClick="document.getElementById('frm_filtro').action = 'logs.php'; document.getElementById('frm_filtro').submit();" /><input type="button" value="Baixar relatório" onClick="document.getElementById('frm_filtro').action = 'log_csv.php'; document.getElementById('frm_filtro').submit();" /></p>
    </form>
<?php
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
	$rs = $db->Query("SELECT COUNT(*) AS cnt FROM (tb_log NATURAL LEFT JOIN tb_ev_log) NATURAL LEFT JOIN tb_usuario $filtro");
	$row = $rs->Row();
	$regs = $row['cnt'];
	$pgs = ceil($regs/20);
	$pg = 20 * min(max(0, (intval(isset($_REQUEST['pg'])?$_REQUEST['pg']:1)-1)), $pgs - 1);
	$rs = $db->Query("SELECT log_dt, ev_nome, usu_nome, usu_local, usu_mun, log_det FROM (tb_log NATURAL LEFT JOIN tb_ev_log) NATURAL LEFT JOIN tb_usuario $filtro ORDER BY log_dt DESC LIMIT $pg, 20");
?>
    <table width="100%" id="resultado" cellpadding="5">
    <thead>
    	<tr style="background: #C3DAE2">
        	<th align="left">Data</th>
        	<th align="left">Evento</th>
        	<th align="left">Usuário</th>
        	<th align="left">Local de trabalho</th>
        	<th align="left">Município</th>
        </tr>
    </thead>
    <tbody>
<?php while ($row = $rs->Row()) { ?>
    	<tr title="<?php echo str_replace("\n", " - ", $row['log_det']); ?>">
        	<td align="left"><?php echo date("d/m/Y H:i:s", strtotime($row['log_dt'])); ?></td>
        	<td align="left"><?php echo $row['ev_nome']; ?></td>
        	<td align="left"><?php echo $row['usu_nome']; ?></td>
        	<td align="left"><?php echo $row['usu_local']; ?></td>
        	<td align="left"><?php echo $row['usu_mun']; ?></td>
        </tr>
<?php } ?>
	</tbody>
    </table>
<?php if ($pgs > 1) { ?>
    <p id="minitabs">Página:
<?php
	for ($i = 1; $i <= $pgs; $i++) {
		if (isset($_REQUEST['pg']) && ($_REQUEST['pg'] == $i)) {
			echo $i.'&nbsp;';
		} else {
			echo '<a href="javascript:void(0);" onClick="document.forms[\'frm_filtro\'].pg.value = '.$i.'; document.forms[\'frm_filtro\'].submit();">'.$i.'</a>&nbsp;';
		}
	}
?>
    </p>
<?php } ?>
  </div>
    <?php require('includes/rodape.php');?>
</div>
</body>
</html>