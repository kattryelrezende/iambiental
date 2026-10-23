<?php
if(session_status() === PHP_SESSION_NONE) session_start();
$nome_sessao = (md5('geral_'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']));
if(!isset($_SESSION[$nome_sessao]['servidor'])) die();

include_once('../../conexao/conexao.php');
$conexao = new Conexao();
$conn = $conexao->conecta('pmsc_producao');
	
$json = array();

$sql = 'Select 	p."pacId", p."pacMatricula", to_char(p."pacDataCadastro", \'DD/MM/YYYY\'), initcap(p."pacNomeSocial"), initcap(p."pacNomeCivil"), 
				initcap(p."pacNomeMae"), to_char(p."pacDataNascimento",\'DD/MM/YYYY\'), 
				coalesce(p."pacOrientacaoSexual",0), 
				coalesce(p."pacIdentidadeGenero",0), 
				coalesce(p."pacRacaCor",0), 
				initcap(p."pacEndereco"), p."pacEnderecoNro", initcap(p."pacBairro"), initcap(p."pacMunicipio"), 
				p."pacUF", p."pacCEP", p."pacTipoContatoDesc", p."pacRG", p."pacCPF", p."pacCNS", 
				p."pacCROSS", p."pacNatural", p."pacUFNatural", p."pacObservacoes", p."pacSysdate", 
				p."usuId", extract(year from age(p."pacDataNascimento")),
				case
					when extract(year from age(p."pacDataNascimento")) <= 12 Then
						true
					Else
						false
				End,
				array_to_string("pacCIDS",\',\'), to_char(p."pacDataObito",\'DD/MM/YYYY\'), to_char(p."pacDataGestante",\'DD/MM/YYYY\'), p."pacSemGestante", p."pacFimTrat", p."pacMudou",
				coalesce((( to_char((current_date::date), \'IW\')::int - to_char((p."pacDataGestante"::date),\'IW\')::int )+p."pacSemGestante"::int),0)
		From saude."tbPaciente" p
		Order by p."pacSysdate" DESC ';

$res = $conexao->consulta($sql);
if ($res && ((pg_num_rows($res))>0)) {
	while ($row = pg_fetch_row($res)){

		$data = array(
			"pacId"					=>$row[0],			
			"pacMatricula"			=>$row[1],
			"pacDataCadastro"		=>$row[2],	
			"_nomecivilsocial"		=>$row[4].(($row[3]!='')?' (<em><strong>'.$row[3].'</strong></em>)':null),
			"pacDataNascimento"		=>$row[6],
			"pacNomeMae"			=>$row[5],
			"_natural"			    =>$row[21].'/'.$row[22],
			"pacDataObito"			=>$row[29],
			"pacCPF"				=>$row[18],
			"pacRG"					=>$row[17],
			"pacCNS"				=>$row[19],
			"pacCROSS"				=>$row[20],
			"pacIdentidadeGenero"	=>$row[8],
			"pacRacaCor"			=>$row[9],
			"pacOrientacaoSexual"	=>$row[7],
			"_endereco"				=>$row[10].','.$row[11].' '.$row[12].' - '.$row[13].'/'.$row[14].' - '.$row[15],
			"pacTipoContatoDesc"	=>$row[16],
			"pacObservacoes"		=>$row[23],
			"pacSysdate"			=>$row[24],
			"usuId"					=>$row[25],
			"pacIdade"				=>$row[26],
			'_pediatrico'			=>$row[27],
			"pacCIDS"				=>$row[28],
			"_temcids"				=>(($row[28]=='')?'f':'t'),
			"pacFimTrat"			=>$row[32],
			"pacMudou"				=>$row[33],
			"_gestacao"				=> (($row[30]!='')?('Data apontamento gravidez: '.$row[30].' com '.$row[31].' semanas').  (((intval($row[34])>=1)&&(intval($row[34])<=40))?' , atualmente com '.$row[34].' semanas.':'. Gestação finalizada.'):''),    
			"_gestante"				=>(($row[34]>0)?'t':'f'),
			"_falecido"				=>(($row[29]=='')?'f':'t')
			);
		array_push($json, $data);
	}
}

$conexao->desconecta();
echo json_encode($json);
?>
