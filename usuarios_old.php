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

function GeraSenha() {
	$charsenha = "ABCDEFGHJKLMNPRSTUVWXYZ23456789";
	$novasenha = '';
	for ($i = 0; $i < 8; $i++) {
		$novasenha .= $charsenha{rand(0, strlen($charsenha) - 1)};
	}
	return $novasenha;
}

function EnviaEmail($email, $login, $senha) {
	$mail_body = <<< EOF
Seu cadastro no SNAIDS:
Usuário: $login
Senha: $senha

TERMO DE RESPONSABILIDADE
Comprometo-me a:
Não revelar fora do âmbito profissional, fato ou informações de qualquer natureza que tenha conhecimento por força de minhas atribuições, salvo em decorrência de decisão competente na esfera legal ou judicial, bem como de autoridade superior.
Acessar as informações somente por necessidade de serviço e por determinação expressa de superior hierárquico.
Não me ausentar do local de trabalho sem encerrar a sessão de uso do Sistema, evitando assim o acesso por pessoas não autorizadas.
Observar rigorosamente os procedimentos de segurança estabelecidos quanto à confidencialidade de minha senha, através dos quais posso efetuar operações a mim designadas nos recursos computacionais que acesso, procedendo a:
7.1.	Substituir a senha inicial gerada pelo sistema, por outra secreta, pessoal e intransferível;
7.2.	Não divulgar a minha senha a outras pessoas;
7.3.	Somente utilizar o meu acesso para os fins designados e para os quais estiver devidamente autorizado, em razão de minhas funções;
7.4.	Reportar imediatamente à minha chefia ou ao Administrador de Segurança, em caso de violação, acidental ou não, da minha senha, e providenciar a sua substituição;
7.5.	Solicitar o cancelamento de minha senha quando não for mais de minha utilização;
EOF;
	mail($email, "Cadastro no Consulta de Notifica��o de Caso", $mail_body, "From: SNAIDS <snaids@r4m0n.net>\r\n");
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
<?php
	if (!empty($_REQUEST['usu_nome'])) {
		if (!empty($_REQUEST['usu_id'])) {
			$db->Query("INSERT INTO tb_log (ev_id, usu_id, log_det) VALUES (6, {$_SESSION['user']['usu_id']}, '".$db->AddSlashes("Usuário alterado: ".$_REQUEST['usu_nome'])."')");
			$db->Query("UPDATE tb_usuario SET usu_nome = '".$db->AddSlashes($_REQUEST['usu_nome'])."', usu_email = '".$db->AddSlashes($_REQUEST['usu_email'])."', usu_login = '".$db->AddSlashes($_REQUEST['usu_login'])."', usu_cpf = '".$db->AddSlashes($_REQUEST['usu_cpf'])."', usu_local = '".$db->AddSlashes($_REQUEST['usu_local'])."', usu_fun = '".$db->AddSlashes($_REQUEST['usu_fun'])."', usu_reg = '".$db->AddSlashes($_REQUEST['usu_reg'])."', usu_mun = '".$db->AddSlashes($_REQUEST['usu_mun'])."', usu_fone = '".$db->AddSlashes($_REQUEST['usu_fone'])."' WHERE usu_id = '".$db->AddSlashes($_REQUEST['usu_id'])."'");
			echo "<p>Usuário alterado!</p>";
		} else {
			$novasenha = GeraSenha();
			$db->Query("INSERT INTO tb_log (ev_id, usu_id, log_det) VALUES (6, {$_SESSION['user']['usu_id']}, '".$db->AddSlashes("Usuário criado: ".$_REQUEST['usu_nome'])."')");
			$db->Query("INSERT INTO tb_usuario (usu_nome, usu_email, usu_login, usu_password, usu_cpf, usu_local, usu_fun, usu_reg, usu_mun, usu_fone, usu_nivel, usu_ativo) VALUES ('".$db->AddSlashes($_REQUEST['usu_nome'])."', '".$db->AddSlashes($_REQUEST['usu_email'])."', '".$db->AddSlashes($_REQUEST['usu_login'])."', MD5('$novasenha'), '".$db->AddSlashes($_REQUEST['usu_cpf'])."', '".$db->AddSlashes($_REQUEST['usu_local'])."', '".$db->AddSlashes($_REQUEST['usu_fun'])."', '".$db->AddSlashes($_REQUEST['usu_reg'])."', '".$db->AddSlashes($_REQUEST['usu_mun'])."', '".$db->AddSlashes($_REQUEST['usu_fone'])."', ".($_SESSION['user']['usu_nivel'] + 1).", 1)");
			EnviaEmail($_REQUEST['usu_email'], $_REQUEST['usu_login'], $novasenha);
			echo "<p>Usuário criado e senha enviada por email!</p>";
		}
	}
	if (isset($_REQUEST['ativar'])) {
		$a = intval($_REQUEST['ativar']);
		if ($a) {
			$log = "Ativando usuário: ".$_REQUEST['usu_id'];
		} else {
			$log = "Desativando usuário: ".$_REQUEST['usu_id'];
		}
		$db->Query("INSERT INTO tb_log (ev_id, usu_id, log_det) VALUES (6, {$_SESSION['user']['usu_id']}, '".$db->AddSlashes($log)."')");
		$db->Query("UPDATE tb_usuario SET usu_ativo = $a WHERE usu_id = '".$db->AddSlashes($_REQUEST['usu_id'])."'");
	}
	if (!empty($_REQUEST['enviasenha'])) {
		$novasenha = GeraSenha();
		$db->Query("INSERT INTO tb_log (ev_id, usu_id, log_det) VALUES (6, {$_SESSION['user']['usu_id']}, '".$db->AddSlashes("Senha do usuário re-enviada: ".$_REQUEST['usu_id'])."')");
		$db->Query("UPDATE tb_usuario SET usu_password = MD5('$novasenha') WHERE usu_id = '".$db->AddSlashes($_REQUEST['usu_id'])."'");
		$rs = $db->Query("SELECT usu_email, usu_login FROM tb_usuario WHERE usu_id = '".$db->AddSlashes($_REQUEST['usu_id'])."'");
		$row = $rs->Row();
		EnviaEmail($row['usu_email'], $row['usu_login'], $novasenha);
		echo "<p>Senha re-enviada por email!</p>";
	}
?>
	<script language="javascript">
	function valida(form) {
		var erros = '';
		var campos = {
			'usu_nome':'Nome',
			'usu_email':'Email',
			'usu_login':'Login',
			'usu_cpf':'CPF',
			'usu_local':'Local de trabalho',
			'usu_fun':'Função',
			'usu_reg':'GVE',
			'usu_mun':'Município',
			'usu_fone':'Telefone'
		};
		var pri_erro = '';
		for (var campo in campos) {
			if (form[campo].value == '') {
				if (pri_erro == '') {
					pri_erro = campo;
				}
				erros += ((erros == '')?'':', ')+campos[campo];
			}
		}
		if (erros != '') {
			alert("Os seguintes campos são de preenchimento obrigatório: "+erros);
			form[pri_erro].focus();
			return false;
		}
		return true;
	}
	</script>
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
    <form action="usuarios.php" method="post" onSubmit="return valida(this);">
<?php
if (!empty($_REQUEST['editar']) && !empty($_REQUEST['usu_id'])) {
	$rs = $db->Query("SELECT * FROM tb_usuario WHERE usu_id = '".$db->AddSlashes($_REQUEST['usu_id'])."'");
	if ($rs->RowCount()) {
		$row = $rs->Row();
		if ($row['usu_nivel'] != $_SESSION['user']['usu_nivel'] + 1) {
			$row = array();
		}
	}
}
if (!empty($row)) { ?>
    <p align="center">Alterar usu&aacute;rio</p>
    <input type="hidden" name="usu_id" value="<?php echo $row['usu_id']; ?>" />
<?php } else { ?>
    <p align="center"><b>CADASTRAR USU&Aacute;RIO</b></p>
<?php } ?>
    <div id="cadusu">
    <p><label for="usu_nome">Nome:</label><input type="text" name="usu_nome" size="50" value="<?php if (!empty($row)) echo $row['usu_nome']; ?>" /></p>
    <p><label for="usu_email">Email:</label><input type="text" name="usu_email" size="50" value="<?php if (!empty($row)) echo $row['usu_email']; ?>" /></p>
    <p><label for="usu_login">Usu&aacute;rio:</label><input type="text" name="usu_login" size="50" value="<?php if (!empty($row)) echo $row['usu_login']; ?>" /></p>
    <p><label for="usu_cpf">CPF:</label><input type="text" name="usu_cpf" size="15" value="<?php if (!empty($row)) echo $row['usu_cpf']; ?>" /></p>
    <p><label for="usu_local">Local de trabalho:</label><input type="text" name="usu_local" size="50" value="<?php if (!empty($row)) echo $row['usu_local']; ?>" /></p>
    <p><label for="usu_fun">Fun&ccedil;&atilde;o:</label><input type="text" name="usu_fun" size="50" value="<?php if (!empty($row)) echo $row['usu_fun']; ?>" /></p>
    <p><label for="usu_reg">GVE:</label><input type="text" name="usu_reg" size="50" value="<?php if (!empty($row)) echo $row['usu_reg']; ?>" /></p>
    <p><label for="usu_mun">Munic&iacute;pio:</label><input type="text" name="usu_mun" size="50" value="<?php if (!empty($row)) echo $row['usu_mun']; ?>" /></p>
    <p><label for="usu_fone">Telefone:</label><input type="text" name="usu_fone" size="30" value="<?php if (!empty($row)) echo $row['usu_fone']; ?>" /></p>
    <p><i>todos os campos acima s&atilde;o de preenchimento obrigat&oacute;rio</i>.</p>
<?php if (!empty($row)) { ?>
    <p><input type="submit" value="Gravar" /> <input type="button" value="Cancelar" onClick="window.location = 'usuarios.php';" /></p>    
<?php } else { ?>
    <p><input type="submit" value="Criar" /></p>
<?php } ?>
    </form> </div>
<?php
$rs = $db->Query("SELECT COUNT(*) AS cnt FROM tb_usuario WHERE usu_nivel = ".($_SESSION['user']['usu_nivel'] + 1));
$row = $rs->Row();
$regs = $row['cnt'];
$pgs = ceil($regs/20);
$pg = 20 * min(max(0, (intval(isset($_REQUEST['pg'])?$_REQUEST['pg']:1)-1)), $pgs - 1);
$rs = $db->Query("SELECT usu_id, usu_nome, usu_ativo, dt_ult_login FROM tb_usuario WHERE usu_nivel = ".($_SESSION['user']['usu_nivel'] + 1)." ORDER BY usu_ativo DESC, usu_nome LIMIT $pg, 20");
?>
    <table width="100%" id="resultado" cellpadding="5">
    <thead>
    <tr style="background: #C3DAE2">
    	<th align="left">Usu&aacute;rio</th>
    	<th align="left">&Uacute;ltimo acesso</th>
        <th align="left">A&ccedil;&otilde;es</th>
    </tr>
    </thead>
    <tbody>
<?php while ($row = $rs->Row()) { ?>
    	<tr>
        	<td align="left"><a href="usuarios.php?usu_id=<?php echo $row['usu_id']; ?>&amp;editar=1"><?php echo $row['usu_nome']; ?></a></td>
        	<td align="left"><?php echo date('d/m/Y H:i:s', strtotime($row['dt_ult_login'])); ?></td>
        	<td align="left">
<?php if ($row['usu_ativo'] == 1) { ?>
				<a href="usuarios.php?usu_id=<?php echo $row['usu_id']; ?>&amp;ativar=0">Desativar</a>&nbsp;
<?php } else { ?>
				<a href="usuarios.php?usu_id=<?php echo $row['usu_id']; ?>&amp;ativar=1">Ativar</a>&nbsp;
<?php } ?>
				<a href="usuarios.php?usu_id=<?php echo $row['usu_id']; ?>&amp;enviasenha=1">Re-enviar senha</a>
            </td>
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
			echo '<a href="usuarios.php?pg='.$i.'">'.$i.'</a>&nbsp;';
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