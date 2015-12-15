<?php
session_start();
#set o tempo de execução do script para o tempo maximo
#amplia em tempo de execução o tamanho permitido(php.ini) para upload de arquivo via formulario
if(!ini_get('safe_mode')) {
    set_time_limit(0);
    ini_set('memory_limit', '64M'); //max. mem. alocada no processo
    ini_set('upload_max_filesize', '100M'); //tam. max. do arquivo
    ini_set('post_max_size', '100M'); // tam. max. do arquivo
    ini_set('max_input_time', 600); //10 min p/ envio do arquivo
}
#verifica se usuario está autenticado, caso contrário envia para pagina de login
if (empty($_SESSION['user']) || empty($_SESSION['ip']) || ($_SESSION['ip'] != $_SERVER['REMOTE_ADDR']) || ($_SESSION['user']['usu_nivel'] > 1)) {
	header("Location: index.php");
	exit();
}
header('Content-type: text/html; charset=utf-8');
require_once('includes/pclzip.lib.php');
require_once('includes/dbf_class.php');
require_once('includes/phpvortex/DB_MySQL.class.php');
require_once('includes/conn.php');
require_once('includes/helper.php');

#verifica se setado o tipo de arquivo
 if(isset($_REQUEST['sistema'])){
   $tipoTab = $_REQUEST['sistema'];
 }else{
   $tipoTab = 'N';
 }
 $tabelas = array('N'=>'tb_net','W'=>'tb_windows');
 $tabelaNome = $tabelas[$tipoTab];
 
########## rota para o arquivo ####################
 //define a rota de gravacao do arquivo .zip ou .dbf
 //deve ter permissão de leitura e escrita
 $uploaddir = './tmp/';
####################################################
 
 $uploadfile = $_FILES['arq'];
 $filename="";
 $fileDbf="";
 $valores=""; 
 ob_start();
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
<p>Carregando o arquivo <?php echo $uploadfile['name']; ?> na tabela <b><?php echo $tabelaNome; ?></b></p>
<?php
//efetua a copia
 $filename = upload($uploadfile,$uploaddir);
 $ext = explode (".",$filename);
 
 echo "<h2>EFETUANDO A CARGA DO ARQUIVO, POR FAVOR AGUARDE...</h2>";
 ob_flush();
 flush();

 echo "<h2>".strtoupper($ext[1])."</h2>";
  ob_flush();
 flush();
 //se o arquivo enviado for um arquivo ZIP
 if(strtoupper($ext[1])=="ZIP"){
   //descompactando o arquivo
   $archive = new PclZip($uploaddir.$filename);
   $listFiles = $archive->extract(PCLZIP_OPT_PATH, $uploaddir);
   if($listFiles==0){
   	  die("Error : ".$archive->errorInfo(true));
   }else{
   	//exclue o arquivo .zip enviado via upload
        $fileDbf = $uploaddir.$listFiles[0]['stored_filename'];
        echo "<b>ARQUIVO =".$listFiles[0]['filename']."</b>";
   	unlink($uploaddir.$filename);
   }
 }elseif(strtoupper($ext[1])=="DBF"){
     $fileDbf=$uploaddir.$filename;
 }
 ob_flush();
 flush();
$arrCampoData= array("DT_NOTIFIC","DT_NASC","DT_DIAG","EVO_DT");

$db = new DB_MySQL($db_conn);
$db->Connect();
$db->Query("SET NAMES 'utf8'");
$db->Query("SET CHARACTER SET utf8");
$db->Query("truncate table {$tabelaNome}");



$dbf = new dbf_class($fileDbf);
$num_rec=$dbf->dbf_num_rec;
$num_field=$dbf->dbf_num_field;
#$num_rec =5620;
$qtdeErr =0;
$field_names = $dbf->dbf_names;


foreach($field_names as $indice => $campo){
	$campos.= $campo['name'].',';
}
$campos= substr($campos,0,strlen($campos)-1);

for($i=0; $i<$num_rec; $i++){
    $row = $dbf->getRowAssoc($i);
	foreach($field_names as $ind => $campo){
		if (in_array($campo['name'],$arrCampoData)){		
		     $valores.= "'".implode('',array_reverse(explode("/",$row[$campo['name']])))."',";
		}else{
			 $valores.= "'".addslashes($row[$campo['name']])."',";
		}
	}
        $valores = substr($valores, 0, strlen($valores)-1);
        $sqlInsert="";
        $sqlInsert = "insert into {$tabelaNome} ({$campos}) value ({$valores});";
        
        #inclui os registros na respectiva tabela do BD
        $inserido=$db->Query($sqlInsert);
        if(!$inserido){
            $qtdeErr++;
            $arrValores[] = $valores;
        }
        $valores ="";
        $data = date('d/m/Y H:i:s', strtotime('now'));
}
if ($qtdeErr>0){
    echo "<h2>Foram encontrado {$qtdeErr} ao carregar o banco de dados, verifique o arquivo e carregue-o novamente.</h2>";
    echo "<pre>";
    print_r($arrValores);
    echo "</pre>";
}else{
    echo "<h2>Tabela carregada com sucesso!</h2>";
}
## apaga o arquivo .dbf
unlink($fileDbf);
$db->Query("INSERT INTO tb_log (ev_id, usu_id, log_det) VALUES (5, {$_SESSION['user']['usu_id']}, 'Carga do arquivo: {$fileDbf}')")
?>
  </div>
    <?php require('includes/rodape.php');?>
</div>
</body>
</html>