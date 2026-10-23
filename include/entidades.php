<?php
if(session_status() === PHP_SESSION_NONE) session_start();
$nome_sessao = (md5('geral_'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']));
if(!isset($_SESSION[$nome_sessao]['servidor'])) die();

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require('../../conexao/conexao.php');

$conexao = new Conexao();
$conn = $conexao->conecta('pmsc_producao');

$term = strtoupper($_REQUEST["term"]);
$serv = intval($_REQUEST["serv"]);
if (!$term) return;

/*
SELECT "pacId", "pacMatricula", "pacDataCadastro", "pacNomeSocial", "pacNomeCivil", "pacNomeMae", "pacDataNascimento", "pacOrientacaoSexual", "pacIdentidadeGenero", "pacRacaCor", "pacEndereco", "pacEnderecoNro", "pacBairro", "pacMunicipio", "pacUF", "pacCEP", "pacTipoContatoDesc", "pacRG", "pacCPF", "pacCNS", "pacCROSS", "pacNatural", "pacUFNatural", "pacObservacoes", "pacSysdate", "usuId"
	FROM saude."tbPaciente";
*/
$aux = preg_replace("/[^0-9]/", "",$term);

$sql0='';
if($serv==0) $sql0 = 'SELECT  p."pacId", p."pacCPF", p."pacNomeCivil",
							  p."pacDataNascimento", p."pacCEP", p."pacEndereco", p."pacEnderecoNro",
							  p."pacBairro", p."pacMunicipio", p."pacUF", \'\', \'\', p."pacId"
					  FROM saude."tbPaciente" p
					  Where regexp_replace(p."pacCPF", '."'[^0-9]', '', 'gi') like '$aux' || '%'" . '  
					  and p."pacCPF" not in (
						Select x.cpf_rne_cnpj from dti.entidades x where x.cpf_rne_cnpj = p."pacCPF"
					  )
					  union all ';
$sql = (($sev==0)?$sql0:null)."Select ".(($serv!=0)?'distinct':null)." e.id, e.cpf_rne_cnpj, e.nome_razao,
		e.data_nascimento, e.cep, e.endereco, e.endereco_nro,
		e.bairro, e.cidade, e.uf, e.fone, e.email, p.\"pacId\"
		from dti.entidades e ";
if($serv!=0) $sql.=" Inner Join dti.vw_servidores s on s.cpf = e.cpf_rne_cnpj and s.data_demissao is null ";
$sql.=' Left Join saude."tbPaciente" p on p."pacCPF" = e.cpf_rne_cnpj ';
$sql.= "Where";

if(intval($aux)>0) 	$sql.= " regexp_replace(cpf_rne_cnpj, '[^0-9]', '', 'gi') like '$aux' || '%' ";
else				$sql.= " nome_razao ilike '%$term%' ";

$sql.= "Order by 3
		Limit 20";		
//echo $sql;
$result = $conexao->consulta($sql);

$json=array();

if ($result && ((pg_num_rows($result))>0))
{
	while ($row=pg_fetch_row($result))
	{
		$elemento = array(
			"id" 	=> intval($row[0]),
			"value" => $row[1]." - ".$row[2],
			"cpf"	=> $row[1],
			"nome" 	=> $row[2],
			"dnasc"	=> $row[3],
			"cep"	=> $row[4],
			"end"	=> $row[5],
			"endn"	=> $row[6],
			"bair"	=> $row[7],
			"cid"	=> $row[8],
			"uf"	=> $row[9],
			"fone"	=> $row[10],
			"mail"	=> $row[11],
			"pid"	=> $row[12]
		);
		array_push ( $json , $elemento );
	}
}
echo json_encode($json);
?>
