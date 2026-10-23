<?php
mb_internal_encoding('UTF8'); 

// Inclui a classe de conexão com o banco de dados
include_once('../conexao/conexao.php');

// Importa as variáveis $_REQUEST, possibilitando o uso de $variavel ao invés de $_REQUEST['variavel']
extract($_REQUEST);

/**
 * $conexao - variável onde é armazenada uma instância da classe Conexão
 * @var class
 */
$conexao = new Conexao();

/**
 * $conn - variável onde é armazenada a conexão atual com o banco requerimento
 * @var resource
 */
$conn = $conexao->conecta('pmsc_producao');
?>
<html>
	<head>
		<title>Prefeitura Municipal de S&atilde;o Carlos</title>
		<link href="estilo.css" rel="stylesheet" type="text/css"> 	
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	</head>
	<body>
		<center>
			<table cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td valign="top">
						<div id="div_index">	
							<div id="div_principal">
<?php
if (!(isset($acao)))
{
			echo '<p class="titulo">Importar Inscricoes Imobiliarias</p>';
?>
								<form method='POST' action='importar.php' name='importar' enctype="multipart/form-data">
									<input type="hidden" name="acao" value="22">
									<table width="100%" cellspacing="5" border="0" cellpadding="2">
										<tr>
											<td width="50%"><b>Arquivo CSV:</b><br>
											<input type='file' name='arquivo' accept='.csv' style='width:430px;' REQUIRED></td>
											<td>&#160;</td>
										</tr>
										<tr>
 											<td align="center" colspan="2"><input type="submit" value="ENVIAR" style="width:300px;"/></td>
										</tr>
									</table>
								</form><br>
<?php
}
else
{
	switch ($acao)
	{
		// Ação = 22 -> Importa os pontos
		case '22':
			echo '<p class="titulo">Importar Inscriçoes Imobiliarias</p>';
			if (isset($_FILES["arquivo"]) && ($_FILES["arquivo"]!=null) )
			{
				$contador= 0;
				$inseridos=0;
				
				$sqli = "INSERT INTO fazenda.ia_imoveis (inscricao,areaterreno,testada,proprietario,logradouro,numero,bairro,cep) VALUES ";
				
				$arquivo = $_FILES["arquivo"];

				if(($arquivo)&&($arquivo["error"]!=4))
				{
					$nomeArquivo = $_FILES["arquivo"]['tmp_name'];
					if(($handle = fopen($nomeArquivo, 'r')) !== FALSE)
					{
						fgetcsv($handle, 0, ';'); 
						while (($data = fgetcsv($handle, 0, ';')) !== FALSE)
						{
							$contador++;
							$inscricao    = preg_replace("/[^0-9]/", "", $data[3]);
							$areaterreno  = str_replace(',', '.', $data[2]);
							$areaterreno  = ($areaterreno === '' || $areaterreno === null) ? 0 : $areaterreno;
							$testada      = str_replace(',', '.', $data[1]);
							$testada	  = ($testada === '' || $testada === null) ? 0 : $testada;
							$proprietario = pg_escape_string($conn, $data[4]);
							$logradouro   = pg_escape_string($conn, $data[7]);
							$numero       = pg_escape_string($conn, $data[8]);
							$bairro       = pg_escape_string($conn, $data[9]);
							$cep          = pg_escape_string($conn, $data[10]);
							$sql = "SELECT id FROM fazenda.ia_imoveis WHERE inscricao = '$inscricao'";
							$res = pg_query($conn, $sql);
							if(!($res && ((pg_num_rows($res))>0))) {
								$inseridos++;
								$sqli.= "('$inscricao', $areaterreno, $testada, '$proprietario', '$logradouro', '$numero', '$bairro', '$cep'),";
							}
						}
						if($inseridos>0){
							$sqli = rtrim($sqli, ',');

							// Executa o INSERT massivo
							$res = pg_query($conn, $sqli);

							if ($res) {
								// 3. SE TUDO DEU CERTO, CONFIRMA NO BANCO DE DADOS
								pg_query($conn, "COMMIT");
								echo "<p align='center'><font color='#FF0000'><b>$contador : registros lidos / $inseridos : registros novos inseridos com sucesso!</b>.</font></p>";
							} else {
								// Se o INSERT massivo falhar (ex: erro de tipo de dado ou tamanho de campo)

								pg_query($conn, "ROLLBACK");
								echo "<p align='center'><font color='#FF0000'><b>ERRO: Falha ao processar os dados. Nenhuma alteração foi salva no banco.</b></font></p>";
							}
					
						}else echo "<p align='center'><font color='#FF0000'><b>ERRO: Nenhum registro foi importado</b>.</font></p>";

					}
					else
						echo "<p align='center'><font color='#FF0000'><b>ERRO: Não foi possível ler o arquivo CSV. A importação não foi efetuada</b>.</font></p>";
				}
				else
					echo "<p align='center'><font color='#FF0000'><b>ERRO: Não foi possível gravar o arquivo CSV. A importação não foi efetuada</b>.</font></p>";
			}
			else
				echo "<p align='center'><font color='#FF0000'><b>ERRO: Nenhum arquivo foi enviado</b>.</font></p>";

			echo "<p align='center'><a href='importar.php'>Voltar</a></p>";
			break;
	}
}
?>
							</div>
						</div>
					</td>
				</tr>
			</table>
		</center>
	</body>
</html>
