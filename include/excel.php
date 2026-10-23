<?php
if(session_status() === PHP_SESSION_NONE) session_start();
$nome_sessao = (md5('geral_'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']));
if(!isset($_SESSION[$nome_sessao]['servidor'])) die();

// Inclui a classe de conexão com o banco de dados
include_once('../../conexao/conexao.php');

// Escolher o formato do arquivo
header("Content-type: application/octet-stream");

// Nome que arquivo será salvo
header("Content-Disposition: attachment; filename=iptu_verde_".$_REQUEST['ano'].".xls");
if(!$_POST['acao']) die();

extract($_REQUEST);

$conexao = new Conexao();
$conn = $conexao->conecta('pmsc_producao');


$sql = "SELECT inscricao_imobiliaria, to_char(data, 'DD/MM/YYYY'), testada, qtdarvores, areaterreno, areapermeavel, captacao, reuso, aquecimento, solar, eolica, telhado, construcoes
		FROM fazenda.ia_adesoes
		WHERE date_part('year', data) = ".$_REQUEST['ano']." 
		AND (inscricao_imobiliaria, id) IN (SELECT inscricao_imobiliaria, max(id) FROM fazenda.ia_adesoes WHERE (inscricao_imobiliaria, data) IN (SELECT inscricao_imobiliaria, max(data) FROM fazenda.ia_adesoes GROUP BY inscricao_imobiliaria) GROUP BY inscricao_imobiliaria)
		ORDER BY data";

// Executa a consulta
$result = $conexao->consulta($sql);

// Verifica se foram encontrados resultados
if ( $result && ((pg_num_rows($result))>0) )
{
	echo utf8_decode('
		<table>
			<tr>
				<th>Inscrição</th>
				<th>Data</th>
				<th>Testada</th>
				<th>Árvores</th>
				<th>Área Terreno</th>
				<th>Área Permeável</th>
				<th>Captação</th>
				<th>Reuso</th>
				<th>Aquecimento</th>
				<th>Solar</th>
				<th>Eólica</th>
				<th>Telhado verde</th>
				<th>Sustentável</th>
				<th>Desconto</th>
			</tr>');
	while ($row = pg_fetch_row($result))
	{
		echo '<tr>';
		echo '<td>' . $row[0] . '</td>';
		echo '<td>' . $row[1] . '</td>';
		echo '<td>' . $row[2] . '</td>';
		echo '<td>' . $row[3] . '</td>';
		echo '<td>' . $row[4] . '</td>';
		echo '<td>' . $row[5] . '</td>';
		for ($i = 6; $i<13; $i++)
			if ($row[$i]=='t')
				echo '<td>Sim</td>';
			else
				echo utf8_decode('<td>Não</td>');

		$testada = floatval($row[2]);
		$arvores = intval($row[3]);
		$terreno = floatval($row[4]);
		$permeavel = floatval($row[5]);

		$desconto=0;

		if (($row[6]=='t')||($row[7]=='t')||($row[8]=='t')||($row[9]=='t')||($row[10]=='t')||($row[11]=='t')||($row[12]=='t'))
			$desconto = 2;
		else
			if ($arvores >= 1)
				$desconto = 1;

		if ($terreno>0)
			$porcpermeavel = $permeavel/$terreno;
		else
			$porcpermeavel = 0;

		if ( ($terreno<=250) && ($porcpermeavel >= 0.05) && ($porcpermeavel < 0.08) )
		{
			 $desconto = $desconto + 1;
		}
		else
		{
			if ( ($terreno<=250) && ($porcpermeavel >= 0.08) )
			{
			   $desconto = $desconto + 2;
			}
			else
			{
				if ( ($terreno>250) && ($porcpermeavel >= 0.08) && ($porcpermeavel < 0.1) )
				{
					$desconto = $desconto + 1;
				}
				else
				{
					if ( ($terreno>250) && ($porcpermeavel >= 0.1) )
					{
						$desconto = $desconto + 2;
					}
				}
			}
		}

		echo '<td>' . intval($desconto) . '</td>';
		echo '</tr>';
	}
?>
		</table>
<?
}
?>
