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

function str2query($str) {
	$tr = array(
		'A' => 'H?A+H?',
		'B' => '[BP]+',
		'C' => '[CKS]+',
		'D' => 'D+',
		'E' => 'H?[EIY]+H?',
		'F' => 'F+',
		'G' => '[JG]+U?',
		'H' => '[HR]*',
		'I' => 'H?[EIY]+H?',
		'J' => '[JG]+U?',
		'K' => '[CKQ]+',
		'L' => 'L+',
		'M' => '[MN]+',
		'N' => '[MN]+',
		'O' => 'H?[OU]+H?',
		'P' => '[BP]+',
		'Q' => '[CKQ]+',
		'R' => '[HR]+',
		'S' => '[CSZ]+',
		'T' => 'T+',
		'U' => 'H?[OU]+H?',
		'V' => '[WVU]+',
		'W' => '[WVU]+',
		'X' => '[CSX]+H?',
		'Y' => 'H?[EIY]+H?',
		'Z' => '[CSZ]+',
		' ' => '.*'
	);
	$ret = '';
	$tmp = strtoupper(strtr($str, "áàãâäÁÀÃÂÄéèêëÉÈÊËíìîïÍÌÎÏóòõôöÓÒÕÔÖúùûüÚÙÛÜçÇñÑýÿÝ", "AAAAAAAAAAEEEEEEEEIIIIIIIIOOOOOOOOOOUUUUUUUUCCNNYYY"));
	for ($i = 0; $i < strlen($tmp); $i++) {
		if (!empty($tr[$tmp{$i}])) {
			$ret .= $tr[$tmp{$i}];
		}
	}
	return '"^'.$ret.' | '.$ret.' | '.$ret.'$|^'.$ret.'$"';
}
$dataBr = "d/m/Y";
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
$idCurrent = "Consultar";
require('includes/menu.php');
?>
<div class="container">
  <div class="content">
    <img src="img/logo_snaids.gif"/><br/><br/>
    <form name="frm" id="frm" action="consulta.php" method="post">
    <input type="hidden" id="pg" name="pg" value="<?php echo isset($_REQUEST['pg'])?$_REQUEST['pg']:1; ?>" />
    <script language="javascript">
	function limpar() {
		document.forms['frm'].nome.value = '';
		document.forms['frm'].mae.value = '';
		document.forms['frm'].nd.selectedIndex = 0;
		document.forms['frm'].nm.selectedIndex = 0;
		document.forms['frm'].na.value = '';
		document.forms['frm'].nd2.selectedIndex = 0;
		document.forms['frm'].nm2.selectedIndex = 0;
		document.forms['frm'].na2.value = '';
	}
    </script>
    <p>Procurar por:</p>
    <p><label for="nome">Nome do paciente:</label><input type="text" class="search" name="nome" size="50" value="<?php if (isset($_REQUEST['nome'])) echo $_REQUEST['nome']; ?>" /></p>
    <p><label for="mae">Nome da mãe:</label><input type="text" class="search" name="mae" size="50" value="<?php if (isset($_REQUEST['mae'])) echo $_REQUEST['mae']; ?>" /></p>
    <p><label for="nd">Data de nascimento entre </label>
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
    <p><input type="submit" value="Procurar" /> <input type="button" value="Nova Busca" onClick="limpar();" /></p>
    </form><br/><br/>
<?php
	if (!empty($_REQUEST['nome']) || !empty($_REQUEST['mae']) || (!empty($_REQUEST['nd']) && !empty($_REQUEST['nm']) && !empty($_REQUEST['na']))) {
		$find = '';
		$log = '';
		if (!empty($_REQUEST['nome'])) {
			$nome = str2query($_REQUEST['nome']);
			$find .= (empty($find)?'':' AND ')."`nm_pacient` REGEXP $nome";
			$log .= "Nome do paciente: {$_REQUEST['nome']}\n";
		}
		if (!empty($_REQUEST['mae'])) {
			$mae = str2query($_REQUEST['mae']);
			$find .= (empty($find)?'':' AND ')."`nm_mae_pac` REGEXP $mae";
			$log .= "Mãe do paciente: {$_REQUEST['mae']}\n";
		}
		if (!empty($_REQUEST['nd']) && !empty($_REQUEST['nm']) && !empty($_REQUEST['na'])) {
			if (is_numeric($_REQUEST['na'])) {
				$d = intval($_REQUEST['nd'], 10);
				$m = intval($_REQUEST['nm'], 10);
				$a = intval($_REQUEST['na'], 10);
				if ($a < 100) {
					$a += 1900;
				}
				$dt = sprintf("%04d%02d%02d", $a, $m, $d);
				if (!empty($_REQUEST['nd2']) && !empty($_REQUEST['nm2']) && !empty($_REQUEST['na2'])) {
					if (is_numeric($_REQUEST['na2'])) {
					}
					$d2 = intval($_REQUEST['nd2'], 10);
					$m2 = intval($_REQUEST['nm2'], 10);
					$a2 = intval($_REQUEST['na2'], 10);
					if ($a2 < 100) {
						$a2 += 1900;
					}
					$dt2 = sprintf("%04d%02d%02d", $a2, $m2, $d2);
					$find .= (empty($find)?'':' AND ')."(`dt_nasc` >= '$dt' AND `dt_nasc` <= '$dt2') ";
					$log .= "Data de nascimento entre $d/$m/$a e $d2/$m2/$a2\n";
				} else {
					$find .= (empty($find)?'':' AND ')."`dt_nasc` = '$dt'";
					$log .= "Data de nascimento: $d/$m/$a\n";
				}
			}
		}
		$db->Query("INSERT INTO tb_log (ev_id, usu_id, log_det) VALUES (3, {$_SESSION['user']['usu_id']}, '".$db->AddSlashes($log)."')");
		$nt_n = '';
		$nt_w = '';
		$found = false;
		$qfind = $db->AddSlashes($find);
		$rs = $db->Query("SELECT * FROM tb_cache WHERE hash = MD5('$qfind')");
		if ($rs->RowCount()) {
			$row = $rs->Row();
			if ($row['filter'] == $find) {
				$nt_n = $row['nt_n'];
				$nt_w = $row['nt_w'];
				$found = true;
			}
		}
		if (!$found) {
			$rs = $db->Query("SELECT id FROM `tb_net` WHERE $find ORDER BY nm_pacient, nm_mae_pac");
			$tmp = array();
			while ($row = $rs->Row()) {
				$tmp[] = $row['id'];
			}
			$nt_n = implode(', ', $tmp);
			$rs = $db->Query("SELECT id FROM `tb_windows` WHERE $find ORDER BY nm_pacient, nm_mae_pac");
			$tmp = array();
			while ($row = $rs->Row()) {
				$tmp[] = $row['id'];
			}
			$nt_w = implode(', ', $tmp);
			$db->Query("INSERT INTO tb_cache (hash, filter, nt_n, nt_w) VALUES (MD5('$qfind'), '$qfind', '$nt_n', '$nt_w')");
		}
		if (empty($nt_n)) {
			$nt_n = '-1';
		}
		if (empty($nt_w)) {
			$nt_w = '-1';
		}
		$regs = count(explode(',', $nt_n)) + count(explode(',', $nt_w));
		if ($nt_n == '-1') {
			$regs--;
		}
		if ($nt_w == '-1') {
			$regs--;
		}
		$pgs = ceil($regs/40);
		$pg = 40 * min(max(0, (intval($_REQUEST['pg'])-1)), $pgs - 1);
                if ($nt_n=='-1'){
                    $pg=1;
                }
                if ($nt_w=='-1'){
                    $pg=1;
                }                
		$rs = $db->Query("SELECT id,`nu_notific`, `dt_notific`, `nm_pacient` as nome, `dt_nasc` as dtnasc, `nm_mae_pac` as mae, `criterio`, `nu_idade_n` AS idade, 'N' AS base FROM `tb_net` WHERE id IN ($nt_n) UNION SELECT id, `nu_notific`, `dt_notific`, `nm_pacient`, `dt_nasc`, `nm_mae_pac`, `criterio`, `nu_idade`, 'W' FROM `tb_windows` WHERE id IN ($nt_w) ORDER BY nome, mae LIMIT $pg, 40");
		if ($rs->RowCount()) { ?>
    <p id="minitabs">QUANTIDADE DE REGISTROS ENCONTRADOS: <b><?php echo $regs;?></b>.</p>
    <table width="100%" id="resultado" cellpadding="5">
    <thead>
    	<tr style="background: #C3DAE2">
        	<th align="left">N&ordm;. Notificação</th>
                <th align="left">Data Notificação</th>
        	<th align="left">Nome</th>
        	<th align="left">Data de nasc.</th>
        	<th align="left">Idade</th>
        	<th align="left">Nome da Mãe</th>
        	<th align="left">Critério</th>
        	<th align="left">Base</th>
        </tr>
    </thead>
    <tbody>
<?php
			while ($row = $rs->Row()) { ?>
    	<tr>
                <td align="left"><?php echo $row['nu_notific']; ?></td>
        	<td align="left"><?php echo dateFormat($dataBr, $row['dt_notific']); ?></td>
        	<td align="left"><a href="detalhe.php?n=<?php echo $row['id']; ?>&amp;b=<?php echo $row['base']; ?>" target="_blank"><?php echo $row['nome']; ?></a></td>
                <td align="left"><?php echo dateFormat($dataBr, $row['dtnasc']); ?></td>
        	<td align="left"><?php echo $row['idade']; ?></td>
        	<td align="left"><?php echo $row['mae']; ?></td>
        	<td align="left"><?php echo $row['criterio']; ?></td>
        	<td align="left"><?php echo ($row['base'] == 'N')?'Net':'Windows'; ?></td>
        </tr>
<?php		} ?>
	</tbody>
    </table>
    

<?php if ($pgs > 1) { ?>
    <div id="pag" style="text-align:center;border: dotted 1px #bdb;padding: 15px 0;">           
        <input id="anterior" type="button" value="&laquo;"  style="font-family: verdana,arial; font-size: 14px; font-weight: bold; text-align: center;border: solid 1px #808080" />
          P&aacute;gina <input style="text-align: center;border: solid 1px #808080" id="pagina" type="text" title="digite o n&uacute;mero da p&aacute;gina" value="<?php echo $_REQUEST['pg']?>" size="3" /> de <input id="paginas" style="text-align: center;border: solid 1px #808080;font-weight: bold" type="text" disabled value="<?php echo $pgs ?>" size="3" />
          <input id="proximo" style="font-family: verdana,arial; font-size: 14px; font-weight: bold; text-align: center;border: solid 1px #808080" type="button" value="&raquo;" />

    </div>
</div>
<?php } ?>
<?php	} else { ?>
	<p><b>Nenhum resultado encontrado.</b></p>
<?php	}
	}
?>
    
  </div>
<?php require('includes/rodape.php');?>
       <script type="text/javascript">

        $(document).ready(function() {
            
            function IsNumeric(input){
                var RE = /^-{0,1}\d*\.{0,1}\d+$/;
                return (RE.test(input));
            }

            var paginas = <?php echo $pgs;?>;
            $('#proximo').click(function() {
                valor =eval($("#pagina").val())+1;
                if(valor >= paginas) valor=paginas;
                $("#pg").attr('value',valor);
                $("#pagina").attr('value',valor);
                $("#frm").submit();
            });

            $('#anterior').click(function() {
                valor = eval($("#pagina").val())-1;
                if(valor==0) valor=1;
                $("#pg").attr('value',valor);
                $("#pagina").attr('value',valor);
                $("#frm").submit();
            });



            $('#pagina').keypress(function(event) {
                if (event.which == '13') {
                    digitado = $('#pagina').val();
                    if(IsNumeric(digitado)){
                        if( digitado < 1 || digitado > paginas){
                          alert("O intervalo deve ser entre  1 e " + paginas +".");
                        }else{
                           $("#pg").attr('value',digitado);
                           $("#pagina").attr('value',digitado);
                           $("#frm").submit();
                        }
                    }else{
                         alert("O intervalo deve ser entre  1 e " + paginas +".");
                    }

                }
            });








         });
        </script>
</body>

</body>
</html>