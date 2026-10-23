<?php
header('Content-Type: application/json; charset=utf-8');

function mask($val, $mask){
    $maskared = '';
    $k = 0;
    for ($i = 0; $i <= strlen($mask) - 1; ++$i) {
        if ($mask[$i] == '#') {
            if (isset($val[$k])) {
                $maskared .= $val[$k++];
            }
        } else {
            if (isset($mask[$i])) {
                $maskared .= $mask[$i];
            }
        }
    }
    return $maskared;
}

if(session_status() === PHP_SESSION_NONE) session_start();
$nome_sessao = (md5('geral_'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']));

if(!isset($_SESSION[$nome_sessao]['servidor'])&&($_REQUEST['table']==1)) die();

include_once('../../conexao/conexao.php');
$conexao = new Conexao();
$conn = $conexao->conecta('pmsc_producao');
	
$json = array();

$sql = "SELECT 	a.id, a.inscricao_imobiliaria, initcap(a.nome), a.cpf,
				a.ddd, a.tel, a.email, a.descontoarvore, a.testada, a.qtdarvores, a.descontopermeavel, 
				a.areaterreno, a.areapermeavel, a.autorizacao, a.protocolo,
				to_char(a.data,'DD/MM/YYYY'), 
				a.status, a.verificado, a.descontosistemas, a.captacao, a.reuso, 
				a.aquecimento, a.solar, a.eolica, a.telhado, a.construcoes, 
				a.retificacao, a.data_hora, a.id_entidade
		FROM fazenda.ia_adesoes a
		Left Join dti.entidades e on a.id_entidade = e.id 
		Where true";
		
if(intval($_REQUEST['table'])==0) {
	$sql.= " And a.cpf = regexp_replace('".$_SESSION[$nome_sessao]['cidadao']['cpf_rne_cnpj']."','\D','','g') ";
	$sql.= " And extract(year from a.data) >= extract(year from current_date)-1 " ;
}
elseif(intval($_REQUEST['table'])==1) 	$sql.= " And  (extract(year from a.data_hora)) = ".intval($_REQUEST['ano']);

$sql.= " AND (a.inscricao_imobiliaria, a.id) IN (SELECT inscricao_imobiliaria, max(id) 
												FROM fazenda.ia_adesoes 
												WHERE (inscricao_imobiliaria, data) IN (SELECT inscricao_imobiliaria, max(data) 
																						FROM fazenda.ia_adesoes GROUP BY inscricao_imobiliaria) GROUP BY inscricao_imobiliaria)";
$sql.= " Order by a.data desc ";

$res = $conexao->consulta($sql);
if ($res && ((pg_num_rows($res))>0)) {
	while ($row = pg_fetch_row($res)){
		$data = array(
			"id"					=>$row[0], 
			"inscricao_imobiliaria" =>mask($row[1], '01.##.###.###.###'),
			"nome"					=>$row[2],
			"cpf"					=>mask($row[3], '###.###.###-##'),
			"ddd"					=>$row[4],
			"telefone"				=>$row[5],
			"email"					=>$row[6],
			"descontoarvore"		=>$row[7],
			"testada"				=>$row[8],
			"qtdarvores"			=>$row[9],
			"descontopermeavel"		=>$row[10],
			"areaterreno"			=>$row[11],
			"areapermeavel"			=>$row[12],
			"autorizacao"			=>$row[13],
			"protocolo"				=>$row[14],
			"data"					=>$row[15],
			"status"				=>$row[16],
			"verificado"			=>$row[17],
			"descontosistemas"		=>$row[18],
			"captacao"				=>$row[19],
			"reuso"					=>$row[20],
			"aquecimento"			=>$row[21],
			"solar"					=>$row[22],
			"eolica"				=>$row[23],
			"telhado"				=>$row[24],
			"construcoes"			=>$row[25],
			"retificacao"			=>$row[26],
			"data_hora"				=>$row[27],
			"id_entidade"			=>$row[28]
			);
		array_push($json, $data);
	}
}

$conexao->desconecta();
echo json_encode($json);

?>
