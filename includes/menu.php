<?php if (!empty($_SESSION['user'])){?>
<p style="text-align: right;padding: 5px 105px;font-family:verdana,arial;font-size:11px;font-weight:normal">Bem-vindo <?php echo $_SESSION['user']['usu_nome'];?>. Seu último acesso foi em <?php echo date('d/m/Y H:i:s', strtotime($_SESSION['user']['dt_ult_login']));?></p>
<?php } ?>
<?php
if (empty($_SESSION['user'])) {
	$arrMenu = array("Entrar"=>"index.php");
} elseif ($_SESSION['user']['usu_nivel'] < 2) {
	$arrMenu = array("Entrar"=>"index.php", "Consultar"=>"consulta.php", "Trocar senha"=>"senha.php", "Administrar"=>"admin.php", "Sair"=>"index.php?logout=1");
} else {
	$arrMenu = array("Entrar"=>"index.php", "Consultar"=>"consulta.php", "Trocar senha"=>"senha.php", "Sair"=>"index.php?logout=1");
}
?>
<ul id="minitabs">
<?php    
foreach($arrMenu as $item => $url){
	if($item == $idCurrent){
		echo "<li><a id=\"current\" href=\"{$url}\">{$item}</a></li>";
	}else{
		echo "<li><a href=\"{$url}\">{$item}</a></li>";
	}
}?>
</ul>
