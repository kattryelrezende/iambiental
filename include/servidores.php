<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require('../../conexao/conexao.php');

$conexao = new Conexao();
$conn = $conexao->conecta('pmsc_producao');

$term = strtoupper($_REQUEST["term"]);
if (!$term) return;

$sql = "SELECT id, matricula, nome FROM dti.servidores WHERE nome ILIKE '%".$term."%' AND data_demissao IS NULL ORDER BY nome";
$result = $conexao->consulta($sql);

$json=array();

if ($result && ((pg_num_rows($result))>0))
{
	while ($row=pg_fetch_row($result))
	{
		$elemento = array(
			"id" => intval($row[0]),
			"value" => $row[1]." - ".$row[2],
		);
		array_push ( $json , $elemento );
	}
}
echo json_encode($json);
?>
