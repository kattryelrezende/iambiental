<?php
if(session_status() === PHP_SESSION_NONE) session_start();
$nome_sessao = (md5('geral_'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']));
if(!isset($_SESSION[$nome_sessao]['cidadao'])) die();

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require('../../conexao/conexao.php');

$conexao = new Conexao();
$conn = $conexao->conecta('pmsc_producao');

$term = strtoupper($_REQUEST["term"]);
if (!$term) return;

$aux = preg_replace("/[^0-9]/", "",$term);

$sql = "Select id, areaterreno, testada, proprietario, logradouro, numero, bairro, cep
		From fazenda.ia_imoveis
		Where inscricao = '$aux'";

$result = $conexao->consulta($sql);

$json=array();

if ($result && ((pg_num_rows($result))>0)){
	while ($row=pg_fetch_row($result)){
		$elemento = array(
			"id" 			=> intval($row[0]),
			"areaterreno" 	=> $row[1],
			"testada"		=> $row[2],
			"proprietario" 	=> $row[3],
			"logradouro"	=> $row[4],
			"numero"		=> $row[5],
			"bairro"		=> $row[6],
			"cep"			=> $row[7]
		);
		array_push ( $json , $elemento );
	}
	echo json_encode($json);
}
?>
