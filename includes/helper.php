<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function dateFormat($dtFormat,$dataN){
    if ($dataN > 0) {
        $dataF = strtotime($dataN);
        $dataF = date($dtFormat,$dataF);
    }else{
        $dataF ="N&atilde;o Dispon&iacute;vel";
    }
    return $dataF;
}

function upload($arquivo,$caminho){
	if(!(empty($arquivo))){
		$arquivo1 = $arquivo;
		$arquivo_minusculo = strtolower($arquivo1['name']);
		$caracteres = array("ç","~","^","]","[","{","}",";",":","´",",",">","<","-","/","|","@","$","%","ã","â","á","à","é","è","ó","ò","+","=","*","&","(",")","!","#","?","`","ã"," ","©");
		$arquivo_tratado = str_replace($caracteres,"",$arquivo_minusculo);
		$destino = $caminho.$arquivo_tratado;
		if(move_uploaded_file($arquivo1['tmp_name'],$destino)){
			echo "<script>window.alert('Arquivo enviado com sucesso.');</script>";
			return $arquivo_tratado;
		}else{
			echo "<script>window.alert('Erro ao enviar o arquivo');</script>";
			return false;
		}
	}
}
?>