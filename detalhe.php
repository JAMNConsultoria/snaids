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

if (strtolower($_REQUEST['b']) == 'n') {
	$rs = $db->Query("SELECT * FROM ((((tb_net LEFT JOIN tb_municnet USING (id_municip)) LEFT JOIN tb_regionet ON (tb_regionet.id_regiona = tb_net.id_regiona)) LEFT JOIN tb_unidade USING (id_unidade)) LEFT JOIN tb_ocupanet USING (id_ocupa_n)) WHERE id = '".$db->AddSlashes($_REQUEST['n'])."'");
	$log = "Base: SINAN NET\n";
} else {
	$rs = $db->Query("SELECT * FROM (((tb_windows LEFT JOIN tb_municipi USING (id_municip)) LEFT JOIN tb_unidade USING (id_unidade)) LEFT JOIN tb_ocupacao USING (id_ocupaca)) WHERE id = '".$db->AddSlashes($_REQUEST['n'])."'");
	$log = "Base: SINAN Windows\n";
}
$row = $rs->Row();
$log .= "Número da Notificação: {$row['nu_notific']}\nNome: {$row['nm_pacient']}\n";
$db->Query("INSERT INTO tb_log (ev_id, usu_id, log_det) VALUES (4, {$_SESSION['user']['usu_id']}, '".$db->AddSlashes($log)."')");
$dataBr = "d/m/Y";

?>
<?php
//barra do governo full - largura 100%
header('Content-type: text/html; charset=utf-8'); 
#require_once ('http://www.saopaulo.sp.gov.br/barrasp/barrasp_full.htm');
require_once("includes/cabecalho.php");
?>
<body>
<?php if (!empty($_SESSION['user'])){?>
<p style="text-align: left;"><a href="javascript:print();">Imprimir esta página</a></p><p style="text-align: right;padding: 5px 105px;font-family:verdana,arial;font-size:11px;font-weight:normal">Bem-vindo <?php echo $_SESSION['user']['usu_nome'];?>. Seu último acesso foi em <?php echo date('d/m/Y H:i:s', strtotime($_SESSION['user']['dt_ult_login']));?></p>
<?php } ?>
<div class="container">
  <div class="content">
          <img src="img/logo_snaids.gif"></img>
    <h3>Detalhe</h3>
	<table width="100%">
    <tbody>
<?php if (strtolower($_REQUEST['b']) == 'n') { ?>
    	<tr>
        	<th align="right">Número da Notifica&ccedil;&atilde;o</th>
        	<td align="left"><?php echo $row['nu_notific']; ?></td>
        </tr>
    	<tr>
        	<th align="right">Data da Notifica&ccedil;&atilde;o</th>
        	<td align="left"><?php echo dateFormat($dataBr,$row['dt_notific']); ?></td>
        </tr>
    	<tr>
        	<th align="right">Município da Notifica&ccedil;&atilde;o</th>
        	<td align="left"><?php echo $row['id_municip'].' - '.$row['nm_municip']; ?></td>
        </tr>
    	<tr>
        	<th align="right">Unidade da Notifica&ccedil;&atilde;o</th>
        	<td align="left"><?php echo $row['id_unidade'].' - '.$row['nm_unidade']; ?></td>
        </tr>
    	<tr>
        	<th align="right">Data do Diagnóstico</th>
        	<td align="left"><?php echo dateFormat($dataBr,$row['dt_diag']); ?></td>
        </tr>
    	<tr>
        	<th align="right">Critério</th>
        	<td align="left"><?php echo $row['criterio']; ?></td>
        </tr>
    	<tr>
        	<th align="right">Nome</th>
        	<td align="left"><?php echo $row['nm_pacient']; ?></td>
        </tr>
    	<tr>
        	<th align="right">Data de Nascimento</th>
        	<td align="left"><?php echo dateFormat($dataBr,$row['dt_nasc']); ?></td>
        </tr>
    	<tr>
        	<th align="right">Nome da Mãe</th>
        	<td align="left"><?php echo $row['nm_mae_pac']; ?></td>
        </tr>
    	<tr>
        	<th align="right">Sexo</th>
        	<td align="left"><?php echo $row['cs_sexo']; ?></td>
        </tr>
    	<tr>
        	<th align="right">Idade</th>
        	<td align="left"><?php echo $row['nu_idade_n']; ?></td>
        </tr>

        <tr>
        	<th align="right">Categoria de Exposição</th>
        	<td align="left"><?php echo $row['ant_rel_ca']; ?></td>
        </tr>

        <tr>
        	<th align="right">Ocupação</th>
                <td align="left"><?php echo (strlen(trim($row['id_ocupa_n']))> 1 ? $row['id_ocupa_n'] : "-" ); ?></td>
        </tr>


    	<tr>
        	<th align="right">Evolução</th>
        	<td align="left"><?php echo $row['evolucao']; ?></td>
        </tr>
    	<tr>
        	<th align="right">Data do Óbito</th>
        	<td align="left"><?php echo dateFormat($dataBr,$row['dt_obito']); ?></td>
        </tr>
    	<tr>
        	<th align="right">Base de Dados</th>
        	<td align="left">SINAN-NET</td>
        </tr>
<?php } else { ?>
    	<tr>
        	<th align="right">Número da Notificação</th>
                <td align="left"><?php echo $row['nu_notific']; ?></td>
        </tr>
    	<tr>
        	<th align="right">Data da Notificação</th>
        	<td align="left"><?php echo dateFormat($dataBr, $row['dt_notific']); ?></td>
        </tr>
    	<tr>
        	<th align="right">Município da Notifica&ccedil;&atilde;o</th>
        	<td align="left"><?php echo $row['id_municip'].' - '.$row['nm_municip']; ?></td>
        </tr>
    	<tr>
        	<th align="right">Unidade da Notifica&ccedil;&atilde;o</th>
        	<td align="left"><?php echo $row['id_unidade'].' - '.$row['nm_unidade']; ?></td>
        </tr>
    	<tr>
        	<th align="right">Data do Diagnóstico</th>
        	<td align="left"><?php echo dateFormat($dataBr, $row['dt_diag']); ?></td>
        </tr>
    	<tr>
        	<th align="right">Critério</th>
        	<td align="left"><?php echo $row['criterio']; ?></td>
        </tr>
    	<tr>
        	<th align="right">Nome</th>
        	<td align="left"><?php echo $row['nm_pacient']; ?></td>
        </tr>
    	<tr>
        	<th align="right">Data de Nascimento</th>
        	<td align="left"><?php echo dateFormat($dataBr, $row['dt_nasc']); ?></td>
        </tr>
    	<tr>
        	<th align="right">Nome da Mãe</th>
        	<td align="left"><?php echo $row['nm_mae_pac']; ?></td>
        </tr>
    	<tr>
        	<th align="right">Sexo</th>
        	<td align="left"><?php echo $row['cs_sexo']; ?></td>
        </tr>
    	<tr>
        	<th align="right">Idade</th>
        	<td align="left"><?php echo $row['nu_idade']; ?></td>
        </tr>

        <tr>
        	<th align="right">Categoria de Exposição</th>
        	<td align="left"><?php echo $row['ant_rel_ca']; ?></td>
        </tr>

        <tr>
        	<th align="right">Ocupação</th>
                <td align="left"><?php echo (strlen(trim($row['id_ocupaca'])) > 1 ? $row['id_ocupaca'] : "-" ); ?></td>
        </tr>

    	<tr>
        	<th align="right">Evolução</th>
        	<td align="left"><?php echo $row['evo_situac']; ?></td>
        </tr>
    	<tr>
        	<th align="right">Data do Óbito</th>
        	<td align="left"><?php echo dateFormat($dataBr, $row['evo_dt']); ?></td>
        </tr>
    	<tr>
        	<th align="right">Base de Dados</th>
        	<td align="left">SINAN-Windows</td>
        </tr>
<?php } ?>
	</tbody>
    </table>
  </div>
<?php require('includes/rodape.php');?>
</div>
</body>
</html>