<!DOCTYPE html>
<?php
/**
 * Prefeitura Municipal de São Carlos
 * Departamento de Tecnologia da Informação
 *
 * Projeto: Adesão ao Programa Incentivo Ambiental
 * Arquivo: index.php
 *
 * Autor: Anderson
 * Data:  2023
 *
 * Comentários:
 *
 */
?>
<html>

<head>
	<title>Prefeitura Municipal de São Carlos</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="robots" content="noindex, nofollow">
	<link rel="icon" type='image/x-icon' href="imagens/favicon.ico">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
		crossorigin="anonymous" referrerpolicy="no-referrer">
	<link rel="stylesheet"
		href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css"
		crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet"
		href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta3/css/bootstrap-select.min.css"
		crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
		crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" crossorigin="anonymous"
		referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.22.0/bootstrap-table.min.css"
		crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css"
		crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="css/style.css">
</head>
<?php
if (!isset($_SESSION))
	session_start();

$nome_sessao = (md5('geral_' . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']));
$nome_sistema = (md5('sistema_' . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']));
$_SESSION[$nome_sistema] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$clog = '/login';

if (isset($_POST['sair'])) {
	unset($_SESSION[$nome_sessao]);
	header("Location: " . $clog);
}

include_once('../conexao/conexao.php');











/**
 * $conexao - variável onde é armazenada uma instância da classe Conexão
 * @var class
 */
$conexao = new Conexao();
$conn = $conexao->conecta('pmsc_producao');

$arr_periodo = array(0 => date('Y') . '-01-07', 1 => date('Y') . '-10-31');
$arr_perfil = array(0 => 'Desabilitado', 1 => 'Administrador', 2 => 'Usuário', 3 => 'Atendente');

function focoTab($atv)
{
	echo "<script>";
	$scr = '';
	for ($i = 0; $i < $atv; $i++) {
		$scr .= "if(document.getElementById('nav-$i-tab'))
				 document.getElementById('nav-$i-tab').classList.remove('active');
				if(document.getElementById('nav-$i'))
				 document.getElementById('nav-$i').classList.remove('show','active');";
	}
	echo $scr;
	echo "if(document.getElementById('nav-$i-tab'))
			document.getElementById('nav-$i-tab').classList.add('active');";
	echo "if(document.getElementById('nav-$i'))
			document.getElementById('nav-$i').classList.add('show','active');";
	echo "</script>";
}

function verificaPermissao($nome_sessao, $conexao)
{
	$sql = "SELECT u.id, u.permissao 
			  FROM dti.permissoes u 
			  JOIN dti.vw_servidores s ON s.id = u.servidor And s.data_demissao is null
			  WHERE u.sistema = 'IAMBIENTAL' 
			  AND s.cpf = '" . $_SESSION[$nome_sessao]['cidadao']['cpf_rne_cnpj'] . "'";

	// Verifica se o usuário tem permissão de acesso à aplicação	
	$result = $conexao->consulta($sql);

	if (!($result && ((pg_num_rows($result)) > 0)))
		return false;
	else {
		$row = pg_fetch_row($result);
		if ($row[1] != '0')
			return ($row[1]);
		else
			return false;
	}
}

?>

<body class='d-print-none'>

	<div class="container pt-3 mt-3 bg-light bg-gradient border border-2 rounded">
		<?
		//Se estiver logado, exibe o topo da aplicação
		if (isset($_SESSION[$nome_sessao]['cidadao'])) {
			$hr = date(" H ");
			if ($hr >= 12 && $hr < 18) {
				$saudacao = "Boa tarde, ";
			} else if ($hr >= 0 && $hr < 12) {
				$saudacao = "Bom dia, ";
			} else {
				$saudacao = "Boa noite, ";
			}
			$usr = explode(' ', strval($_SESSION[$nome_sessao]['cidadao']['nome_razao']));
			$usr = ucfirst(strtolower($usr[0]));
			?>
			<div class="row justify-content-center">
				<div class='col-auto'><img src='./imagens/brasao.png'></div>
			</div>
			<div class="row justify-content-center pt-2">
				<div class='col-auto'>
					<h3>Programa de Incentivo Ambiental</h3>
				</div>
			</div>
			<div class="row justify-content-end pb-2">
				<div class='col-auto'>
					<p><small><span class='pe-2'>
								<? echo ($saudacao); ?><strong>
									<a href="<? echo ($clog); ?>/index.php?alterar=2">
										<? echo ($usr); ?></strong>
							</span><i class="fa-solid fa-address-card pe-2"></i></a>
							<a href="<? echo ($clog); ?>/index.php?sair=1"><i
									class="fa-solid fa-door-open pe-2"></i></a></small></p>
				</div>
			</div>
			<?
		} else
			echo "<script>location.href = '" . $clog . "/index.php';</script>";

		if (intval($_SESSION[$nome_sessao]['cidadao']['tipo']) != 1) {
			echo "	<div class='alert alert-danger alert-dismissible fade show' role='alert'>
			  <strong>ERRO!</strong> Serviço disponível apenas para Pessoa Física
			  <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
			</div>";
			unset($_SESSION[$nome_sessao]);
		} else {

			extract($_REQUEST);
			$permissao = verificaPermissao($nome_sessao, $conexao);

			if (($_REQUEST) && (isset($salvar))) {
				unset($err);
				//SE O USUÁRIO LOGADO FOR UM ATENDENTE, PORTANTO QUE ESTEJA CADASTRANDO TERCEIROS, OS DADOS PARA A MONTAGEM DA STRING SQL PRECISAM SER DIFERENTES
				if ($permissao == 3) {
					//REMOVENDO CARACTERES ESPECIAIS DO CPF
					$cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']);
					$sql = "INSERT INTO fazenda.ia_adesoes(inscricao_imobiliaria, nome, cpf, ddd, tel, email, 
					testada, descontoarvore, qtdarvores, 
					descontopermeavel, areaterreno, areapermeavel, 
					autorizacao, protocolo, data, status, 
					descontosistemas, captacao, reuso, aquecimento, solar, eolica, telhado, construcoes, 
					retificacao, data_hora, id_entidade, id_entidade_logado)
SELECT '" . preg_replace("/[^0-9]/", "", $inscricao_imobiliaria) . "','" . $_POST['nome'] . "','" . $cpf . "','','" . $_POST['fone1'] . "','" . $_POST['email1'] . "',
$testada," . (isset($desc_1) ? 'true,' . intval($qtd_arvores) : 'false,0') . ", "
						. (isset($desc_3) ? 'true,' . ($areaterreno) . ',' . ($areapermeavel) : 'false,0,0') . ", " .
					(($check1 == 'on') ? 'true' : 'false') . ", '" . substr(chunk_split(time(), 3, '.'), 0, 13) . "', now(), false,";
				}
				//SE O USUÁRIO LOGADO NÃO FOR UM ATENDENTE...
				else {
					$sql = "INSERT INTO fazenda.ia_adesoes(inscricao_imobiliaria, nome, cpf, ddd, tel, email, 
										   testada, descontoarvore, qtdarvores, 
										   descontopermeavel, areaterreno, areapermeavel, 
										   autorizacao, protocolo, data, status, 
										   descontosistemas, captacao, reuso, aquecimento, solar, eolica, telhado, construcoes, 
										   retificacao, data_hora, id_entidade)
			SELECT '" . preg_replace("/[^0-9]/", "", $inscricao_imobiliaria) . "','" . ($_SESSION[$nome_sessao]['cidadao']['nome_razao']) . "','" . preg_replace("/[^0-9]/", "", $_SESSION[$nome_sessao]['cidadao']['cpf_rne_cnpj']) . "','','$fone','$email',
					$testada," . (isset($desc_1) ? 'true,' . intval($qtd_arvores) : 'false,0') . ", "
						. (isset($desc_3) ? 'true,' . ($areaterreno) . ',' . ($areapermeavel) : 'false,0,0') . ", " .
						(($check1 == 'on') ? 'true' : 'false') . ", '" . substr(chunk_split(time(), 3, '.'), 0, 13) . "', now(), false,";
				}
				//FIM - SE O USUÁRIO LOGADO NÃO FOR UM ATENDENTE...
		

				if (isset($desc_2)) {
					$sql .= 'true,' . (isset($captacao_chuva) ? 'true' : 'false') . ',';
					$sql .= (isset($reuso) ? 'true' : 'false') . ',';
					$sql .= (isset($aquecimento) ? 'true' : 'false') . ',';
					$sql .= (isset($solar) ? 'true' : 'false') . ',';
					$sql .= (isset($eolica) ? 'true' : 'false') . ',';
					$sql .= (isset($telhado_verde) ? 'true' : 'false') . ',';
					$sql .= (isset($sustentavel) ? 'true' : 'false') . ',';
				} else
					$sql .= 'false,false,false,false,false,false,false,false,';

				$sqlx = "SELECT TRUE from fazenda.ia_adesoes a
			where a.inscricao_imobiliaria='" . preg_replace("/[^0-9]/", "", $inscricao_imobiliaria) . "'
			and a.cpf = '" . preg_replace("/[^0-9]/", "", $_SESSION[$nome_sessao]['cidadao']['cpf_rne_cnpj']) . "'
			and (extract(year from a.data)) = (extract(year from now()))
			and retificacao is FALSE";
				$resx = pg_query($conn, $sqlx);

				$sql .= (pg_result($resx, 0) ? 'true' : 'false');

				//SE O USUÁRIO LOGADO FOR UM ATENDENTE, PORTANTO QUE ESTEJA CADASTRANDO TERCEIROS, OS DADOS PARA A MONTAGEM DA STRING SQL PRECISAM SER DIFERENTES
				if ($permissao == 3) {
					$sql .= ', now(), ' . $_POST['id'] . ', ' . intval($_SESSION[$nome_sessao]['cidadao']['id']);
				} else {
					$sql .= ',now(), ' . intval($_SESSION[$nome_sessao]['cidadao']['id']);
				}

				$sql.= " Where now() between '". $arr_periodo[0] . "' and ('". $arr_periodo[1] . "'::date + 1)";

				$conexao->consulta("BEGIN TRANSACTION");
				//echo($sql);
				$res = $conexao->consulta($sql);

				if ($res) {


					if (isset($salvar)) {
						if ($permissao != 3) {
							//atualizo telefone/email da entidade
							$sql = "UPDATE dti.entidades SET 
					fone = '" . $fone . "',
					email  = '" . $email . "'
					WHERE id = " . intval($_SESSION[$nome_sessao]['cidadao']['id']) . "
					AND NOT EXISTS (Select 1 from dti.entidades e2 Where e2.email = '$email' And e2.id <> " . intval($_SESSION[$nome_sessao]['cidadao']['id']) . ")";
							$res = $conexao->consulta($sql);
							if (!$res)
								$err = "Falha atualizando dados pessoais; este e-mail já está em uso.";
							else {
								//atualizo a session
								$_SESSION[$nome_sessao]['cidadao']['fone'] = $fone;
								$_SESSION[$nome_sessao]['cidadao']['email'] = $email;
							}
						}
					}
				} else
					$err = "Falha salvando solicitação.";

				if (isset($err)) {
					$conexao->consulta("ROLLBACK TRANSACTION");
					echo "	<div class='alert alert-danger alert-dismissible fade show' role='alert'>
				  <strong>ERRO!</strong> $err
				  <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
				</div>";
				} else {
					$conexao->consulta("COMMIT TRANSACTION");
					pg_query($conn, "COMMIT TRANSACTION");
					echo "	<div class='alert alert-success alert-dismissible fade show' role='alert'>
				  <strong>Ok!</strong> Solicitação enviada com sucesso!
				  <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
				</div>";
				}
			}

			?>
			<nav>
				<div class="nav nav-pills flex-column flex-sm-row py-1 px-2" id="nav-tab" role="tablist">
					<button class="nav-link flex-sm-fill text-sm-start active" id="nav-0-tab" data-bs-toggle="tab"
						data-bs-target="#nav-0" type="button" role="tab" aria-controls="nav-0" aria-selected="true"><i
							class="fa-solid fa-leaf pe-2"></i>Adesão</button>
					<button class="nav-link flex-sm-fill text-sm-start" id="nav-1-tab" data-bs-toggle="tab"
						data-bs-target="#nav-1" type="button" role="tab" aria-controls="nav-1" aria-selected="true"><i
							class="fa-solid fa-list-check pe-2"></i>Minhas solicitações</button>
					<? if (intval($permissao) >= 1) { ?>
						<button class="nav-link flex-sm-fill text-sm-start" id="nav-2-tab" data-bs-toggle="tab"
							data-bs-target="#nav-2" type="button" role="tab" aria-controls="nav-2" aria-selected="false"><i
								class="fa-solid fa-file-invoice pe-2"></i>Lista</button>
						<? if (intval($permissao) == 1) { ?>
							<button class="nav-link flex-sm-fill text-sm-start" id="nav-3-tab" data-bs-toggle="tab"
								data-bs-target="#nav-3" type="button" role="tab" aria-controls="nav-3" aria-selected="false"><i
									class="fa-solid fa-user-gear pe-2"></i>Permissões</button>
						<? }
					} ?>
				</div>
			</nav>

			<div class="tab-content" id="nav-tabContent">
				<div class="tab-pane fade show active" id="nav-0" role="tabpanel" aria-labelledby="nav-0-tab">
					<div class="alert alert-success m-2" role="alert">
						<span class='fs-12'>
							&bull;&nbsp;&nbsp;Os contribuintes que possuem imóveis edificados horizontais com <a
								href='http://www.saocarlos.sp.gov.br/index.php/iptu/154470-incentivo-ambiental-beneficios.html'
								target='_blank'>árvores</a> na calçada em frente ao imóvel ou sistemas de captação de água
							de chuva; de reuso de água; de aquecimento hidráulico solar; de energia solar; de utilização de
							energia eólica; telhado verde; ou construções com material sustentável, podem obter descontos de
							até <strong>2%</strong> no imposto de <strong>
								<?php echo (date('Y') + 1); ?>
							</strong>.<br>
							&bull;&nbsp;&nbsp;O valor do desconto será de <strong>1%</strong> aos imóveis que possuem
							árvores na calçada OU <strong>2%</strong> aos imóveis que atendem aos demais critérios citados
							acima.<br>
							&bull;&nbsp;&nbsp;Também para imóveis edificados horizontais que possuem <a
								href='http://www.saocarlos.sp.gov.br/index.php/iptu/154470-incentivo-ambiental-beneficios.html'
								target='_blank'>área permeável</a> serão concedidos descontos que podem chegar a
							<strong>2%</strong>.<br>
							&bull;&nbsp;&nbsp;Os descontos são cumulativos, podendo chegar a <strong>4%</strong>.<br>
							Estes benefícios, denominados <strong>incentivo ambiental</strong>, estão previstos na <a
								href='https://camarasaocarlos.sp.gov.br/lei/?id=7719#:~:text=Ementa%3A%20Institui%20a%20planta%20gen%C3%A9rica,urbano%2C%20e%20da%20outras%20providencias.'
								target="_blank">Lei 13.692/05</a>, Artigos 44 e 45, com alteração pela <a
								href='https://camarasaocarlos.sp.gov.br/lei/?ent=70792&id=49121' target='_blank'>Lei
								19.477/19</a> e disciplinados pelo Decreto nº 368/20.
						</span>
					</div>
					<?
					$hoje = date('Y-m-d');
					if (($hoje < $arr_periodo[0]) || ($hoje > $arr_periodo[1])) {
						echo "	<div class='alert alert-danger alert-dismissible fade show m-2' role='alert'>
			  <strong>ATENÇÃO!</strong>
			  <br>Período de adesão, de <strong>" . date('d/m/Y', strtotime($arr_periodo[0])) . "</strong> a <strong>" . date('d/m/Y', strtotime($arr_periodo[1])) . "</strong>
			  <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
			</div>";
					} else {
						//VARIÁVEL QUE CONTROLA SE O BOTÃO DE ADICIONAR MUNÍCIPE OU DE SELECIONAR MUNÍCIPE FOI CLICADO
				

						$post = false;
						if (isset($_POST["add_mun"]) || (isset($_POST["sel_mun"]))) {
							$post = true;
						}

						?>
						<fieldset class="form-group border m-2 p-2 rounded ">
							<form method='POST' id='form0' enctype='multipart/form-data'
								action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

								<?php
								if (($permissao == 3) && (!$post)) {
									/*SE NÃO HOUVE DEFINIÇÃO DOS DADOS DO SOLICITANTE AINDA*/
									?>
									<div class="form-group">

										<fieldset class="row g-2 px-2">
											<p>
												<span class="fw-bold fs-13">Dados do solicitante</span>
											</p>
											<div class='col-sm-3'>
												<label for='cpf'>CPF</label>
												<input type="text" class="form-control form-control-sm" name='cpf' id='cpf'
													onblur="isCPF(this.value);check_entidade(1);" required <? if ($row_entidade)
														echo "value='" . $row_entidade['cpf_rne_cnpj'] . "' readonly"; ?> />
											</div>
											<div class='col-sm-9'>
												<label for='nome'>Nome</label>
												<input type="text" class="form-control form-control-sm text-uppercase" name='nome'
													id='nome' required <? if ($row_entidade)
														echo "value='" . $row_entidade['nome_razao'] . "' readonly"; ?> />
											</div>

											<div class='col-sm-6'>
												<label for='fone1'>Fone</label>
												<input type="text" required class="form-control form-control-sm" id='fone1'
													name='fone1' <? if ($row_entidade)
														echo "value='" . $row_entidade['fone'] . "' "; ?> />
											</div>
											<div class='col-sm-6'>
												<label for='email1'>e-mail</label>
												<input type="email" class="form-control form-control-sm" id='email1' name='email1'
													onblur="check_entidade(0);" <? if ($row_entidade)
														echo "value='" . $row_entidade['email'] . "' "; ?> />
												<input type="hidden" id="id" name="id" value='<?php echo ($row_entidade['id']); ?>' />
											</div>
										</fieldset>
										<? if (!isset($row_entidade['id'])) { ?>
											<div class='row pt-3 px-3 gap-2'>
												<button type='button' id="btnclear" onclick='limpa_entidade();'
													class='col btn btn-md btn-secondary w-100'><i
														class="fa-solid fa-arrows-rotate pe-2"></i>Limpar</button>
												<button type='submit' name='add_mun' id='add_mun'
													class='col btn btn-md btn-warning w-100'><i
														class="fa-solid fa-person-circle-plus pe-2"></i>Adicionar munícipe</button>
												<button type='submit' name='sel_mun' id='sel_mun'
													class='col btn btn-md btn-primary w-100' hidden><i
														class="fa-solid fa-person-circle-check pe-2"></i>Selecionar munícipe</button>
											</div>
										<? } ?>
									</div>
									<?php
								}
								//SE É UM USUÁRIO ATENDENTE, PORTANTO DE PERMISSÃO = 3 E HOUVE CLIQUE EM ALGUM DOS BOTÕES
								else if (($permissao == 3) && ($post)) {
									//SE O BOTÃO CLICADO FOI O DE ADICIONAR MUNÍCIPE
									if (isset($_POST["add_mun"])) {
										if (isset($_POST['email1']) && (!empty($_POST['email1']))) {
											$emailMunicipe = $_POST['email1'];
										} else {
											//GERANDO O EMAIL RANDÔMICO CASO O MUNÍCIPE NÃO POSSUA E-MAIL
											$emailMunicipe = 'naoresponder_' . rand(100000000, 900000000) . '@emailrandomico.com.br';
										}

										// Verifica se o e-mail já existe
										$sql = "SELECT id FROM dti.entidades WHERE email = '".$emailMunicipe."'";
										$res = pg_query($conn, $sql);										
										if (!($res && ((pg_num_rows($res)) > 0)))
										{
											$sql = "INSERT INTO dti.entidades(cpf_rne_cnpj, tipo, status, nome_razao, fone, email, senha )
														VALUES ('$cpf', 1, 1, '" . strtoupper($nome) . "', '$fone1', '$emailMunicipe', 
																PGP_SYM_ENCRYPT('" . time() . "', 'AES_KEY')) Returning 
																id, cpf_rne_cnpj, nome_razao, fone, email;";
											
											$res = pg_query($conn, $sql);
											if (!$res) {

												unset($row_entidade);
												$row_entidade['cpf_rne_cnpj'] = $cpf;
												$row_entidade['nome_razao'] = $nome;
												$row_entidade['fone'] = $fone1;
												$row_entidade['email'] = $email1;
											} else {
												//$row_entidade = pg_fetch_assoc($res);						
												//$identidade = $row_entidade['id'];						
												//												$_POST = array(); 
												echo "<meta http-equiv='refresh' content='0;url=index.php?v=1'>";
												//header("Location:http://uol.com.br", true, 301);
												exit;


											}
										}
										else
										{
												unset($row_entidade);
												$row_entidade['cpf_rne_cnpj'] = $cpf;
												$row_entidade['nome_razao'] = $nome;
												$row_entidade['fone'] = $fone1;
												$row_entidade['email'] = $email1;
												echo "<meta http-equiv='refresh' content='0;url=index.php?v=2'>";
										
										}
									} else if (isset($_POST["sel_mun"])) {
										?>
												<div class="form-group">

													<fieldset class="row g-2 px-2">
														<p>
															<span class="fw-bold fs-12">Informações do solicitante</span>
														</p>
														<div class='col-sm-3'>
															<label for='cpf'>CPF</label>
															<input type="text" class="form-control form-control-sm" name='cpf' id='cpf' readonly
																value='<?php echo ($_POST['cpf']); ?>' />
														</div>
														<div class='col-sm-9'>
															<label for='nome'>Nome</label>
															<input type="text" class="form-control form-control-sm text-uppercase" name='nome'
																id='nome' value='<?php echo ($_POST['nome']); ?>' readonly />
														</div>

														<div class='col-sm-6'>
															<label for='fone1'>Fone</label>
															<input type="text" class="form-control form-control-sm" id='fone1' name='fone1'
																value='<?php echo ($_POST['fone1']); ?>' readonly />
														</div>
														<div class='col-sm-6'>
															<label for='email1'>e-mail</label>
															<input type="email" class="form-control form-control-sm" id='email1' name='email1'
																value='<?php echo ($_POST['email1']); ?>' readonly />
															<input type="hidden" id="id" name="id" value='<?php echo ($_POST['id']); ?>' />

														</div>
													</fieldset>
													<hr />
													<label for="inscricao_imobiliaria" class="form-label">Inscrição Imobiliária</label>
													<div class="input-group mb-2">
														<input type="text" class="form-control form-control-sm" id="inscricao_imobiliaria"
															name="inscricao_imobiliaria" minlength="14" required>
														<button class="btn btn-sm btn-outline-success" type="button" id="btnconsultaii2"><i
																class="fa-regular fa-circle-check ps-1 pe-3"></i>Pesquisar</button>
														<div class="invalid-feedback">Preenchimento obrigatório.</div>
													</div>
												</div>
										<?php
									}

								} else {
									/*SE JÁ HOUVE DEFINIÇÃO DOS DADOS DO SOLICITANTE*/
									?>

										<div>
											<p>
												<span class='fw-bold fs-12'>Informações do solicitante</span>
												<br>
												<span class='fs-12'>Nome: </span>
												<span class='fs-12 fw-bold'>
												<? echo $_SESSION[$nome_sessao]['cidadao']['nome_razao']; ?>
												</span>
												<span class='fs-12 ms-3'>CPF: </span>
												<span class='fs-12 fw-bold'>
												<? echo $_SESSION[$nome_sessao]['cidadao']['cpf_rne_cnpj']; ?>
												</span>
											</p>
										<? if ($row['adesao'] != '') { ?>
												<p><span
														class='<? echo (($row['adesao'] == 'f') ? 'bg-danger' : 'bg-success'); ?> text-white fw-bold p-2 rounded'>Autorização
														registrada em :
													<? echo ($row['data_hora_alteracao']) ?>
													</span></p>
										<? } ?>
										</div>
										<div class='row'>
											<div class="mb-2 col-sm-5">
												<label for="fone" class="form-label">Fone</label>
												<input type='text' class="form-control form-control-sm" id="fone" name="fone"
													value='<? echo $_SESSION[$nome_sessao]['cidadao']['fone'] ?>' required></input>
												<div class="invalid-feedback">Preenchimento obrigatório.</div>
											</div>
											<div class="mb-2 col-sm-7">
												<label for="email" class="form-label">e-mail</label>
												<input type='email' class="form-control form-control-sm" id="email" name="email"
													value='<? echo $_SESSION[$nome_sessao]['cidadao']['email'] ?>' required></input>
												<div class="invalid-feedback">Preenchimento obrigatório.</div>
											</div>
										</div>
										<label for="inscricao_imobiliaria" class="form-label">Inscrição Imobiliária</label>
										<div class="input-group mb-2">
											<input type="text" class="form-control form-control-sm" id="inscricao_imobiliaria"
												name="inscricao_imobiliaria" minlength="14" required>
											<button class="btn btn-sm btn-outline-success" type="button" id="btnconsultaii"><i
													class="fa-regular fa-circle-check ps-1 pe-3"></i>Pesquisar</button>
											<div class="invalid-feedback">Preenchimento obrigatório.</div>
										</div>

									<?php
								}
								?>
								<div id='info-imovel' class='d-none'>

									<fieldset class='border rounded p-2'>
										<p>
											<span class='fw-bold fs-12'>Informações do imóvel</span>
											<br>
											<span class='fs-12'>Nome contribuinte:</span>
											<span class='fw-bold fs-12' id='proprietario'></span>
											<br>
											<span class='fs-12'>Logradouro:</span>
											<span class='fw-bold fs-12' id='logradouro'></span>
											<br>
											<span class='fs-12'>Bairro:</span>
											<span class='fw-bold fs-12' id='bairro'></span>
											<span class='fs-12 ms-5'>CEP:</span>
											<span class='fw-bold fs-12' id='cep'></span>
										</p>

										<div class='row border rounded m-1'>
											<div class="col-sm-12">
												<input class="form-check-input" type="checkbox" value="" id="desc_1"
													name='desc_1'>
												<label class="label" for="desc_1">Desconto referente à existência de árvore(s)
													em frente ao imóvel.</label>
											</div>
											<div class="mb-2 col-sm-6">
												<label for="testada" class="form-label">Testada (m)</label>
												<input type='number' class="form-control form-control-sm" id="testada"
													name="testada" step='0.01' readonly></input>
												<div class="invalid-feedback">Preenchimento obrigatório.</div>
											</div>
											<div class="mb-2 col-sm-6">
												<label for="qtd_arvores" class="form-label">Qtd.árvores</label>
												<input type="text" class="form-control form-control-sm" id="qtd_arvores"
													name="qtd_arvores" maxlength='3' disabled
													oninput="this.value=this.value.replace(/[^0-9]/g,'');">
												<div class="invalid-feedback">Preenchimento obrigatório.</div>
											</div>
										</div>
										<div class='row border rounded m-1'>
											<div class="col-sm-12">
												<input class="form-check-input" type="checkbox" value="" id="desc_2"
													name='desc_2'>
												<label class="label" for="desc_2">Desconto referente à sistemas ecológicos do
													imóvel.</label>
											</div>
											<div class="col-sm-12">
												<input class="form-check-input ms-3" type="checkbox" value=""
													id="captacao_chuva" name="captacao_chuva" disabled>
												<label class="label" for="captacao_chuva">Sistema de captação da água da
													chuva</label>
											</div>
											<div class="col-sm-12">
												<input class="form-check-input ms-3" type="checkbox" value="" id="reuso"
													name="reuso" disabled>
												<label class="label" for="reuso">Sistema de reuso de água</label>
											</div>
											<div class="col-sm-12">
												<input class="form-check-input ms-3" type="checkbox" value="" id="aquecimento"
													name="aquecimento" disabled>
												<label class="label" for="aquecimento">Sistema de aquecimento hidráulico
													solar</label>
											</div>
											<div class="col-sm-12">
												<input class="form-check-input ms-3" type="checkbox" value="" id="solar"
													name="solar" disabled>
												<label class="label" for="solar">Sistema de energia solar</label>
											</div>
											<div class="col-sm-12">
												<input class="form-check-input ms-3" type="checkbox" value="" id="eolica"
													name="eolica" disabled>
												<label class="label" for="eolica">Sistema de uilização de energia eólica</label>
											</div>
											<div class="col-sm-12">
												<input class="form-check-input ms-3" type="checkbox" value="" id="telhado_verde"
													name="telhado_verde" disabled>
												<label class="label" for="telhado_verde">Instalação de telhado verde</label>
											</div>
											<div class="col-sm-12">
												<input class="form-check-input ms-3" type="checkbox" value="" id="sustentavel"
													name="sustentavel" disabled>
												<label class="label" for="sustentavel">Construções com material
													sustentável</label>
											</div>
										</div>
										<div class='row border rounded m-1'>
											<div class="col-sm-12">
												<input class="form-check-input" type="checkbox" value="" id="desc_3"
													name='desc_3'>
												<label class="label" for="desc_3">Desconto referente à existência de área(s)
													permeável(eis) no perímetro do terreno.</label>
											</div>
											<div class="mb-2 col-sm-6">
												<label for="areaterreno" class="form-label">Área terreno (m&sup2;)</label>
												<input type='number' class="form-control form-control-sm" id="areaterreno"
													name="areaterreno" step="0.01" readonly></input>
												<div class="invalid-feedback">Preenchimento obrigatório.</div>
											</div>
											<div class="mb-2 col-sm-6">
												<label for="areapermeavel" class="form-label">Área permeável (m&sup2;)</label>
												<input type="number" class="form-control form-control-sm" id="areapermeavel"
													name="areapermeavel" step='0.01' disabled></input>
												<div class="invalid-feedback">Preenchimento obrigatório.</div>
											</div>
										</div>
									</fieldset>
									<div class="form-check mb-2">
										<input type="checkbox" class="form-check-input" id="check1" name='check1'>
										<label class="form-check-label" for="check1">
											Autorizo a entrada de um fiscal da prefeitura em meu terreno a fim de efetuar a
											medição das áreas acima solicitadas.
										</label>
									</div>
									<div class="form-check mb-2">
										<input type="checkbox" class="form-check-input" id="check2" name='check2' required>
										<label class="form-check-label" for="check2">
											Declaro que as informações acima prestadas são verdadeiras, e assumo a inteira
											responsabilidade pelas mesmas na forma da lei.
										</label>
									</div>

									<div class='row mb-1 gap-2 mx-1'>
										<button type='submit' name='salvar' id='salvar' class='mt-2 col btn btn-primary w-100'
											disabled><i class="fa-regular fa-circle-check ps-1 pe-3"></i>Enviar
											autorização</button>
									</div>

								</div>

							</form>
						</fieldset>
					<? } ?>
				</div>
				<div class="tab-pane fade" id="nav-1" role="tabpanel" aria-labelledby="nav-1-tab">

					<fieldset class="form-group border rounded m-2 p-2" id='fstble0'>
						<table id="tble0" class='table-sm table-striped table-borderless fs-12'
							data-buttons-prefix="btn-sm btn" data-toggle="tble0" data-pagination='true'
							data-detail-view-by-click="false">
							<thead>
								<tr>
									<th data-field='id' data-visible="false">id</th>
									<th data-field='inscricao_imobiliaria'>Inscrição Imobiliária</th>
									<th data-field='protocolo'>Protocolo</th>
									<th data-field='data' data-formatter='true' class='text-center'>Data inclusão</th>
									<th data-field='verificado' data-formatter='_verifica' class='text-center'>Status</th>
								</tr>
							</thead>
						</table>
					</fieldset>


				</div>
				<div class="tab-pane fade" id="nav-2" role="tabpanel" aria-labelledby="nav-2-tab">

					<fieldset class="form-group border rounded m-2 p-2" id='fstble1'>
						<form method='POST' action='include/excel.php'>
							<button type='submit' name='gera' class='btn btn-md btn-success w-100 mb-2'><i
									class="fa-solid fa-table pe-2"></i>Exportar para Excel</button>
							<input type='hidden' name='acao' value="1" />
						<div class="row py-2">
							<div class='col-7'></div>
							<div class='col-2  d-flex justify-content-end align-items-center'>
								<label class="form-check-label pe-2" for="ano">Ano:</label>								
								<select class='form-select form-select-sm ano' id='ano' name='ano'>
<?
$res = pg_query($conn, "Select distinct (extract(year from data_hora::date)) From fazenda.ia_adesoes Where data_hora is not null Order by 1 Desc");
while ($row = pg_fetch_row($res)){ echo "<option value='".$row[0]."'>".$row[0].'</option>';}
?>
								</select>
							</div>
							
							<div class='col-3  d-flex justify-content-end align-items-center'>
								<input type="text" id="customSearch" class='form-control-sm' placeholder='Pesquisar...'>
							</div>
						</div>
						</form>

						<table id="tble1" class='table-sm table-striped table-borderless fs-12'
							data-buttons-prefix="btn-sm btn" data-toggle="tble1" data-search='true'
							data-search-selector="#customSearch" data-search-accent-neutralise="true" data-pagination='true'
							data-detail-view-by-click="false" data-detail-view-icon="true" data-detail-view="true"
							data-detail-formatter="_detail">
							<thead>
								<tr>
									<th data-field='id' data-visible="false">id</th>
									<th data-field='id_entidade' data-visible="false">id_entidade</th>
									<th data-field='cpf'>CPF</th>
									<th data-field='nome' data-sortable="true">Nome</th>

									<th data-field='inscricao_imobiliaria' data-sortable="true">Inscrição Imobiliária</th>
									<th data-field='protocolo' data-sortable="true">Protocolo</th>
									<th data-field='data' data-formatter='true' class='text-center' data-sortable="true">
										Data inclusão</th>
									<th class='text-center' data-formatter="imageFormatter">
										Imprimir</th>

									<!--<th data-field='adesao' data-formatter='_status' class='text-center'>Adesão</th>
									<th data-field='data_hora_criacao' data-formatter='true' class='text-center'>Data criação</th>
									<th data-field='data_hora_alteracao' data-formatter='true' class='text-center'>Última alteração</th>-->
								</tr>
							</thead>
						</table>
					</fieldset>
				</div>
				<div class="tab-pane fade" id="nav-3" role="tabpanel" aria-labelledby="nav-3-tab">
					<!--permissoes-->
					<?
					switch (intval($acao)) {
						case 11:
							//inclui usuario
							$sql = "INSERT INTO dti.permissoes (sistema, servidor, permissao) VALUES ('IAMBIENTAL', " . intval($idservidor) . ", " . intval($perfil) . ")";
							pg_query($conn, $sql);
							focoTab(3);
							break;

						case 12:
							//altera perfil usuarios
							foreach ($_REQUEST as $campo => $valor) {
								if (((substr($campo, 0, 7)) == 'perfil_')) {
									// Atualiza usuário
									$sql = 'UPDATE dti.permissoes SET ';
									$sql .= 'permissao = ' . intval($valor);
									$sql .= " WHERE id = " . (substr($campo, 7));
									pg_query($conn, $sql);
								}
							}
							focoTab(3);
							break;
					}
					$permissao = verificaPermissao($nome_sessao, $conexao);
					if ($permissao == 1) {
						?>
						<fieldset class="form-group border m-2 p-2 rounded ">
							<form method='POST' action='<?php echo $_SERVER['PHP_SELF']; ?>'>
								<input type="hidden" name="acao" value="11">
								<input type="text" name="idservidor" id="idservidor" style="display:none;" REQUIRED>
								<div class='form row'>
									<div class='col-sm-5'>
										<label>Servidor: </label><label style="color:#FF0000;" id="labelservidor">Selecione um
											servidor na lista</label>
									</div>
									<div class='col-sm-4'>
										<label>Perfil:</label>
									</div>

								</div>

								<div class='form row'>
									<div class='col-sm-5'>
										<input class='form-control form-control-sm w-100' type="text"
											placeholder='Comece digitar o nome para pesquisar...' name="servidor" id="servidor"
											REQUIRED>
									</div>
									<div class='col-sm-4'>
										<select class='form-select form-select-sm w-100' name="perfil">
											<?
											$aux = "<option hidden>Selecione</option>";
											foreach ($arr_perfil as $key => $value) {
												$aux .= "<option value='$key'>$value</option>";
											}
											echo $aux;
											?>
										</select>
									</div>
									<div class='col-sm-3'>
										<input type='submit' class='btn btn-primary btn-sm w-100' value='Adicionar' />
									</div>
								</div>

							</form>
						</fieldset>
						<fieldset class="form-group border m-2 p-2 rounded">
							<form method='POST' action='<?php echo $_SERVER['PHP_SELF']; ?>'>
								<input type="hidden" name="acao" value="12">
								<table class='table table-sm table-hover table-light'>
									<tbody>
										<?php

										// Busca os usuários no banco de dados
										$sql = "SELECT u.id, u.permissao, initcap(lower(s.nome))
							FROM dti.permissoes u 
							JOIN dti.servidores s ON u.servidor = s.id 
							Where u.sistema = 'IAMBIENTAL'
							ORDER BY s.nome";

										// Executa a consulta
										$result = $conexao->consulta($sql);

										// Lista os usuários
										while ($row = pg_fetch_row($result)) {
											echo "<tr>";
											echo "<td>" . $row[2] . "</td>";

											echo "<td><select class='form-select form-select-sm w-100' name='perfil_$row[0]'>
									<option value='1' " . (($row[1] == '1') ? " selected " : "") . ">Administrador</option>
									<option value='2' " . (($row[1] == '2') ? " selected " : "") . ">Usuário</option>
									<option value='3' " . (($row[1] == '3') ? " selected " : "") . ">Atendente</option>
									<option value='0' " . (($row[1] == '0') ? " selected " : "") . ">* Desabilitado</option>
								  </select>
							  </td>";
											echo "</tr>";
										}
										?>
									</tbody>
								</table>
								<p><input type='submit' class='btn btn-primary w-100' value='Salvar' /></p>
							</form>
						<? } ?>
					</fieldset>
					<!--fim permissoes-->
				</div>
			</div>
		</div>
	<? } ?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" crossorigin="anonymous"
		referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.7/umd/popper.min.js" crossorigin="anonymous"
		referrerpolicy="no-referrer"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"
		referrerpolicy="no-referrer"></script>
	<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js" crossorigin="anonymous"
		referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta3/js/bootstrap-select.min.js"
		crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta3/js/i18n/defaults-pt_BR.min.js"
		crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"
		crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.js" crossorigin="anonymous"
		referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" crossorigin="anonymous"
		referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment-with-locales.min.js"
		crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.22.0/bootstrap-table.min.js"
		crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.22.0/locale/bootstrap-table-pt-BR.min.js"
		crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="js/funcoes.js?v=3"></script>
	<script>

		$("#btnconsultaii").on("click", function (e) {
			e.preventDefault();
			$.ajax({
				type: "GET",
				url: "include/inscricao_imobiliaria.php",
				data: {
					term: $('#inscricao_imobiliaria').val(),
				},
				success: function (result) {
					$('#inscricao_imobiliaria').prop('readonly', true);
					if (result != '') popula_ii(result);
				},
				error: function (result) {
					$.alert({
						type: 'red',
						title: "<i class='fa-solid fa-triangle-exclamation pe-3 text-warning'></i>Atenção",
						content: 'Inscrição imobiliária não encontrada!',
						autoClose: 'cancel|3000',
						buttons: {
							cancel: {
								btnClass: 'btn-danger',
								text: 'Ok',
							},
						}
					});
				}
			});
		});

		$("#btnconsultaii2").on("click", function (e) {
			e.preventDefault();
			$.ajax({
				type: "GET",
				url: "include/inscricao_imobiliaria.php",
				data: {
					term: $('#inscricao_imobiliaria').val(),
				},
				success: function (result) {
					$('#inscricao_imobiliaria').prop('readonly', true);
					if (result != '') popula_ii(result);
				},
				error: function (result) {
					$.alert({
						type: 'red',
						title: "<i class='fa-solid fa-triangle-exclamation pe-3 text-warning'></i>Atenção",
						content: 'Inscrição imobiliária não encontrada!',
						autoClose: 'cancel|3000',
						buttons: {
							cancel: {
								btnClass: 'btn-danger',
								text: 'Ok',
							},
						}
					});
				}
			});
		});

		$('#desc_1').change(function () {
			$("#qtd_arvores").prop({ 'disabled': !this.checked, 'required': this.checked });
		});

		$('#desc_2').change(function () {
			$("#captacao_chuva").prop({ 'disabled': !this.checked, 'checked': false });
			$("#reuso").prop({ 'disabled': !this.checked, 'checked': false });
			$("#aquecimento").prop({ 'disabled': !this.checked, 'checked': false });
			$("#solar").prop({ 'disabled': !this.checked, 'checked': false });
			$("#eolica").prop({ 'disabled': !this.checked, 'checked': false });
			$("#telhado_verde").prop({ 'disabled': !this.checked, 'checked': false });
			$("#sustentavel").prop({ 'disabled': !this.checked, 'checked': false });
		});

		$('#desc_3').change(function () {
			$("#areapermeavel").prop({ 'disabled': !this.checked, 'required': this.checked });
		});

		$('#check2').change(function () {
			$('#salvar').prop('disabled', !($("#check2").is(":checked")));
		});


		function popula_ii(e) {
			$('#info-imovel').removeClass('d-none');
			$("#proprietario").text(e[0].proprietario);
			$("#logradouro").text(e[0].logradouro + ' , ' + e[0].numero);
			$("#bairro").text(e[0].bairro);
			$("#cep").text(e[0].cep);
			$("#testada").val(e[0].testada);
			$("#areaterreno").val(e[0].areaterreno);
		}

		function isCPF(campo) {
			if (isEmpty(campo)) { return true; }

			//let id = document.getElementById('identidade').value;

			var campo_filtrado = "", valor_1 = " ", valor_2 = " ", ch = "";
			var valido = false;

			for (i = 0; i < campo.length; i++) {
				ch = campo.substring(i, i + 1);
				if (ch >= "0" && ch <= "9") {
					campo_filtrado = campo_filtrado.toString() + ch.toString()
					valor_1 = valor_2;
					valor_2 = ch;
				}
				if ((valor_1 != " ") && (!valido)) valido = !(valor_1 == valor_2);
			}
			if (!valido) campo_filtrado = "12345678912";

			if (campo_filtrado.length < 11) {
				for (i = 1; i <= (11 - campo_filtrado.length); i++) { campo_filtrado = "0" + campo_filtrado; }
			}

			if ((campo_filtrado.substring(9, 11) == checkCPF(campo_filtrado.substring(0, 9))) && (campo_filtrado.substring(11, 12) == "")) {
				//aki
				return true;
			}

			else {
				$.alert({
					type: 'red',
					title: "<i class='fa-solid fa-triangle-exclamation pe-3 text-warning'></i>Atenção",
					content: 'CPF inválido!',
					autoClose: 'cancel|3000',
					buttons: {
						cancel: {
							btnClass: 'btn-danger',
							text: 'Ok',
						},
					}
				});
				limpa_entidade();
				return false;
			}
		}

		function checkCPF(vCPF) {
			var mControle = ""
			var mContIni = 2, mContFim = 10, mDigito = 0;
			for (j = 1; j <= 2; j++) {
				mSoma = 0;
				for (i = mContIni; i <= mContFim; i++)
					mSoma = mSoma + (vCPF.substring((i - j - 1), (i - j)) * (mContFim + 1 + j - i));
				if (j == 2) mSoma = mSoma + (2 * mDigito);
				mDigito = (mSoma * 10) % 11;
				if (mDigito == 10) mDigito = 0;
				mControle1 = mControle;
				mControle = mDigito;
				mContIni = 3;
				mContFim = 11;
			}
			return ((mControle1 * 10) + mControle);
		}

		function check_entidade(aux) {

			let cpf = $("#cpf").val()
			let email = $("#email1").val()

			let url = (aux == 0) ? 'term=' + cpf + '&term2=' + email : 'term=' + cpf;

			$.ajax({
				type: 'GET',
				url: "include/entidades.php?" + url,
				dataType: 'json',
				success: function (response) {
					if (response.length > 0) {
						if ((aux == 0) && (!(cpf == response[0].cpf && email == response[0].mail))) {
							$.alert({
								columnClass: 'col-md-12',
								type: 'red',
								title: "<i class='fa-solid fa-triangle-exclamation pe-3 text-warning'></i>Atenção",
								content: 'e-mail já cadastrado para o munícipe: <strong>' + response[0].value + '</strong>',
								autoClose: 'cancel|5000',
								buttons: {
									cancel: {
										btnClass: 'btn-danger',
										text: 'Ok'
									}
								}
							})
							$('#email1').addClass('bg-danger text-white');
						} else {
							if (aux != 0) popula_entidade(response[0]);
						}
					}
				}
			})
		}

		function popula_entidade(e) {

			$("#cpf").val(e.cpf);
			$("#nome").val(e.nome);
			$("#fone1").val(e.fone);
			$("#email1").val(e.mail);
			$("#id").val(e.id);
			$('#email1').removeClass('bg-danger text-white');
			des_abi_entidade(true);
		}

		function limpa_entidade() {

			window.location.replace("index.php");

			/*			
			
			$("#cpf").val(null);
			$("#nome").val(null);
			$("#fone1").val(null);
			$("#email1").val(null);
			$("#id").val(null);
			$('#email1').removeClass('bg-danger text-white');
			des_abi_entidade(false);
			
			document.getElementById('cpf').focus();
			*/
		}

		function des_abi_entidade(b) {
			$("#cpf").prop('readonly', b);
			$("#nome").prop('readonly', b);
			//	$( "#fone1" ).prop('readonly', b);
			//	$( "#email1" ).prop('readonly', b);




			$("#add_mun").prop('hidden', b);
			$("#sel_mun").prop('hidden', !b);
		}
		function isEmpty(pStrText) {
			var len = pStrText.length;
			var pos;
			var vStrnewtext = "";

			for (pos = 0; pos < len; pos++) {
				if (pStrText.substring(pos, (pos + 1)) != " ") {
					vStrnewtext = vStrnewtext + pStrText.substring(pos, (pos + 1));
				}
			}

			if (vStrnewtext.length > 0)
				return false;
			else
				return true;
		}

		$(document).ready(function ($) {
			//DESABILITANDO TEMPORARIAMENTE O CHECKBOX, SENÃO O POST PARA INSERÇÃO NÃO PODERIA SER FEITO
			$("#check2").prop("disabled", true);

			//HABILITANDO O CHECKBOX APÓS CLIQUE NO BOTÃO DE PESQUISA DE INSCRIÇÃO IMOBILIÁRIA
			$("#btnconsultaii2").on("click", function () {
				$("#check2").prop("disabled", false);
			});
			$("#btnconsultaii").on("click", function () {
				$("#check2").prop("disabled", false);
			});
			var SPMaskFone = function (val) {
				return val.replace(/\D/g, '').length === 11 ? '(00) 0.0000-0000' : '(00) 0000-00009';
			},
				spOptions = {
					onKeyPress: function (val, e, field, options) {
						field.mask(SPMaskFone.apply({}, arguments), options);
					}
				};

			$('#cpf').mask('000.000.000-00');
			$('#cep').mask('00.000-000', { reverse: true });
			$('#fone1').mask(SPMaskFone, spOptions);

			$('#email1').focus(function () {
				$('#email1').removeClass('bg-danger text-white');
			});


			$('.datepicker').datepicker({
				clearBtn: true,
				language: "pt-BR",
				multidate: true,
				disableTouchKeyboard: true,
				daysOfWeekHighlighted: "0,6",
				endDate: "0d"
			});




			$("#cidadao").autocomplete({
				minLength: 3,
				source: function (request, response) {
					$.ajax
						({
							url: "include/entidades.php",
							type: "GET",
							data: {
								term: request.term
							},
							success: function (data) {
								response($.map(data, function (el) {
									return {
										label: el.value,
										value: el.id
									};
								}));
							}
						})
				},
				select: function (event, ui) {
					$("#cidadao").val(ui.item.label);
					$("#idusuario").val(ui.item.value);

					document.getElementById('labelcidadao').innerHTML = 'Usuário selecionado com sucesso';
					document.getElementById('labelcidadao').style.color = "#008000";

					return false;
				},
				search: function (event, ui) {
					document.getElementById('labelcidadao').innerHTML = 'Selecione um usuário na lista';
					document.getElementById('labelcidadao').style.color = "#FF0000";
					$("#idusuario").val(null);
				}
			});

			$("#cpf").autocomplete({
				minLength: 3,
				source: function (request, response) {
					$.ajax
						({
							url: "include/entidades.php",
							type: "GET",
							data: {
								term: request.term
							},
							success: function (data) {
								response($.map(data, function (el) {
									return {
										label: el.value,
										value: el.id,
										cpf: el.cpf,
										nome: el.nome,
										fone: el.fone,
										id: el.id,
										mail: el.mail
									};
								}));
							}
						})
				},
				select: function (event, ui) {
					popula_entidade(ui.item);
					return false;
				},
			});
		});
		//FUNÇÃO QUE É CHAMADA QUANDO UM MUNÍCIPE É CADASTRADO NO SISTEMA. O REDIRECT É PARA OCULTAR O PARÂMETRO PASSAGO VIA GET E QUE EXIBE O BOX COM A MENSAGEM DE SUCESSO
		function exibeAlerta(msg) {
			$.alert({
				type: 'green',
				title: "<i class='bi bi-exclamation-triangle pe-3 text-success'></i>Sucesso",
				content: msg + "<br />Aguarde...",
				autoClose: 'cancel|3000',
			});
			setTimeout(function () { window.location = 'index.php'; }, 3100);
		}


	</script>
	<!--FUNÇÃO QUE FORMATA A IMPRESSORA NA TABELA DO BOOTSTRAP-->
	<script>

		function imageFormatter(value, row, index) {
			//console.log(this);
			value = '<a href="imprime.php?id=' + row.id + '" target="_BLANK"><img src="imagens/impressora2.png" /></a>';
			return value;
		}
	</script>


	<!--FUNÇÃO QUE VERIFICA SE O MUNÍCIPE ACABOU DE SER CADASTRADO E EXIBE A MENSAGEM DE SUCESSO-->
	<?php
	if (isset($_GET['v'])) {
		if ($_GET['v'] == 1) {
			echo '<script type="text/javascript">',
				'exibeAlerta("Munícipe cadastrado com sucesso!");',
				'</script>'
			;
		}
		else
			if ($_GET['v'] == 2) {
				echo '<script type="text/javascript">',
					'exibeAlerta("ERRO: O e-mail informado já foi utilizado em outro cadastro. Utilize outro e-mail");',
					'</script>'
				;
			}
	}
	?>


</body>

</html>
