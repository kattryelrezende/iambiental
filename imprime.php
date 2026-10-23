<!DOCTYPE html>
<?
if(session_status() === PHP_SESSION_NONE) session_start();
$nome_sessao = (md5('geral_'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']));
if(!isset($_SESSION[$nome_sessao]['servidor'])) die();

include_once('../conexao/conexao.php');

if(!isset($_GET['id']))
{
    header("Location: index.php");
    die();
}
else
{
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_URL);
}
if(!is_numeric($id))
{
    header("Location: index.php");
        die();
}
else
{
$id = $_GET['id'];
}

$sql = "SELECT nome, inscricao_imobiliaria, protocolo FROM fazenda.ia_adesoes WHERE id = " . $id;
//echo($sql);
$conexao = new Conexao();
$conn = $conexao->conecta('pmsc_producao');

$result = $conexao->consulta($sql);
if (!($result && ((pg_num_rows($result)) > 0)))
{
	header("Location: index.php");
die();
}

	else {
$row = pg_fetch_row($result);
	}
?>
<html>
	<head>
		<title>Prefeitura Municipal de São Carlos</title>
		<meta content="text/html; charset=UTF-8" http-equiv="content-type"/>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> 
		<link rel="icon" href="../imagens/favicon.ico">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" >
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" >
		<style>
			@page { 
				size: A4;
				margin-top: 5mm;
				margin-bottom: 15mm;
				margin-left: 10mm;
				margin-right: 10mm;

				@bottom-left{
				font-size:x-small; 
					content: counter(page) " / " counter(pages) ;
					-webkit-border-before: 1px solid whitesmoke;
				}
			}

			html{
				font-size:small;
			}

			#marca_dagua {
				position: fixed;
				top:   20%;
				left:  8%;
				z-index:  -1000;
				opacity: 0.1; 
			}

			#carimbo {
				line-height: 1.0; 
				font-size:x-small; 
				writing-mode: vertical-rl; 
				position: fixed; 
				/*left: 5px; */
				right: 1px;
				top: 10px; 
				z-index: -1;	
			}
		</style>		

	</head>
	<body>
		<div id="marca_dagua">
			<img src="imagens/brasao-mono.jpg" />
		</div>
		<div class="container p-2 m-2">
 			<div class="row justify-content-start align-items-center border-bottom pb-3 mb-3">
			
				<div class="col-2">
					<img class="img-fluid w-75" src="imagens/brasao300x300.png" />
				</div>
				<div class="col-9 text-center">
					<div class='row'>
						<h1><strong>Prefeitura Municipal de São Carlos</strong></h1>
					</div>
					<div class='row'>
						<h3><strong>Programa de Incentivo Ambiental</strong></h3>
					</div>
				</div>
			</div>
			
			<div class="row justify-content-start border-bottom pb-3 mb-3">
				<div class='col-4'>
					<h5><strong>Nome:  </strong></h5>
				</div>
				<div class='col-5 justify-content-start '>
					<h5><strong><?php echo ($row[0]); ?></strong></h5>
				</div>
		</div>

		<div class="row justify-content-start border-bottom pb-3 mb-3">
				<div class='col-4'>
					<h5><strong>Inscrição Imobiliária:  </strong></h5>
				</div>
				<div class='col-5 justify-content-start '>
					<h5><strong><?php echo ($row[1]); ?></strong></h5>
				</div>
		</div>

		<div class="row justify-content-start border-bottom pb-3 mb-3">
				<div class='col-4'>
					<h5><strong>Protocolo:  </strong></h5>
				</div>
				<div class='col-5 justify-content-start '>
					<h5><strong><?php echo ($row[2]); ?></strong></h5>
				</div>
		</div>


		</div>

		<div class="footer container mt-auto p-2 m-2">
			<div id='carimbo'>
				<p class="text-muted"><?echo date("d/m/Y H:i").', '.ucwords(strtolower( $_SESSION[$nome_sessao]['servidor'][1]['nome'] ));?></p>
			</div>
		</div>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
		<script src="https://unpkg.com/pagedjs/dist/paged.polyfill.js"></script>

		<script type="text/javascript">
			class MyHandler extends Paged.Handler {
				constructor(chunker, polisher, caller) {
				  super(chunker, polisher, caller);
				}
				afterPreview(pages){
					window.print();
				};
			}
			Paged.registerHandlers(MyHandler);
			window.onafterprint = function(){ window.close()};
		</script>

	</body>

</html>	
<?
