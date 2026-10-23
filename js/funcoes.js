//habilitando popups..
const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))

$(function(){	
	var ano = $('#ano').val();
	$('#tble0').bootstrapTable({
		url: 'include/adesoes.php?table=0',
		loadingFontSize: '12px',
	});
	$('#tble1').bootstrapTable({
		//url: 'include/adesoes.php?table=1',
		url: 'include/adesoes.php?table=1&ano='+ano,
		loadingFontSize: '12px',
	});
	$('#ano').change(function () {
		let ano = $('#ano').val();
		$('#tble1').bootstrapTable('refresh',{
			url: 'include/adesoes.php?table=1&ano='+ano,
		});		
	});

});

function _status(value){
	if (value == 't') return '<img src="https://img.icons8.com/emoji/25/000000/check-mark-emoji.png"/>';
	else return '<img src="https://img.icons8.com/emoji/25/cross-mark-emoji.png"/> ';
}


function _verifica(value){
	if (value == '3') return '<i class="fa-solid fa-check"></i>';
	else if (value == '2') return '<i class="fa-solid fa-xmark pe-2"></i>';
	else return '<i class="fa-regular fa-clock pe-2"></i>Solicitado';
}



function dateSorter(a, b){
	return(new Date( moment(a, 'DD/MM/YYYY hh:mm:ss') ).getTime() - new Date( moment(b, 'DD/MM/YYYY hh:mm:ss') ).getTime());
}

function _detail(index, row, element) {
	var tumb = "";
	var html = [];
	$.each(row, function (key, value) {
		if( value !== '' ){
			if( key.indexOf('telefone')!== -1)	{ tumb+="<span class='col-sm-12'>Fone: <strong>"+value+"</strong></span>" }
			if( key.indexOf('email')!== -1)	{ tumb+="<span class='col-sm-12'>e-mail: <strong>"+ "<a href='mailto:"+value+"' target='_blank'>"+value+"</strong></a>" +"</span>" }

			if((key.indexOf('descontoarvore')!== -1)&&(value=='t')) { tumb+="<span class='col-sm-12'>Desconto árvores: <strong>SIM</strong></span>" } 
			if( key.indexOf('testada')!== -1) { tumb+="<span class='col-sm-12 ms-3'>Testada: <strong>"+value+"</strong></span>" } 
			if((key.indexOf('qtdarvores')!== -1)&&(parseInt(value)>0)) { tumb+="<span class='col-sm-12 ms-3'>Qtd.árvores: <strong>"+value+"</strong></span>" } 
			
			if((key.indexOf('descontosistemas')!== -1)&&(value=='t')) { tumb+="<span class='col-sm-12'>Desconto referente à sistemas ecológicos do imóvel: <strong>SIM</strong></span>" } 
			if((key.indexOf('captacao')!== -1)&&(value=='t')) { tumb+="<span class='col-sm-12 ms-3'>Captação de água da chuva</span>" } 
			if((key.indexOf('reuso')!== -1)&&(value=='t')) { tumb+="<span class='col-sm-12 ms-3'>Reuso da água</span>" } 
			if((key.indexOf('aquecimento')!== -1)&&(value=='t')) { tumb+="<span class='col-sm-12 ms-3'>Aquecimento hidráulico solar</span>" } 
			if((key.indexOf('solar')!== -1)&&(value=='t')) { tumb+="<span class='col-sm-12 ms-3'>Energia solar</span>" } 
			if((key.indexOf('eolica')!== -1)&&(value=='t')) { tumb+="<span class='col-sm-12 ms-3'>Energia eólica</span>" } 
			if((key.indexOf('telhado')!== -1)&&(value=='t')) { tumb+="<span class='col-sm-12 ms-3'>Telhado verde</span>" } 
			if((key.indexOf('construcoes')!== -1)&&(value=='t')) { tumb+="<span class='col-sm-12 ms-3'>Construção com material sustentável</span>" } 

			if((key.indexOf('descontopermeavel')!== -1)&&(value=='t')) { tumb+="<span class='col-sm-12'>Desconto referente à existência de área(s) permeável(eis) no perímetro do terreno: <strong>SIM</strong></span>" } 

			if((key.indexOf('areaterreno')!== -1)&&(parseInt(value)>0)) { tumb+="<span class='col-sm-12 ms-3'>Área terreno: <strong>"+value+"</strong></span>" } 
			if((key.indexOf('areapermeavel')!== -1)&&(parseInt(value)>0)) { tumb+="<span class='col-sm-12 ms-3'>Área permeável: <strong>"+value+"</strong></span>" } 
			
			if(key.indexOf('autorizacao')!== -1) { tumb+="<span class='col-sm-12'>Autorizo a entrada de um fiscal da prefeitura em meu terreno a fim de efetuar a medição das áreas acima solicitadas: <strong>"+(value=='t' ? 'SIM' : 'NÃO')+"</strong></span>" } 
		}
	});
	html.push("<div class='row justify-content-md-center'>" + tumb + "</div>")
	return html.join('')
}

$(document).on("keydown", ":input:not(textarea)", function(event) {
	if (event.key == "Enter") {
		event.preventDefault();
	}
});

/*$(document).on("keypress", ".commaseparated", function (e) {
    var currentValue = $(this).val();
    if(currentValue == "" && e.keyCode >= 48 && e.keyCode <= 57) {
        return true;
    }
    else {
        var lastChar = currentValue.substr(currentValue.length - 1);
        if(lastChar == "," && e.keyCode >= 48 && e.keyCode <= 57){
            return true;
        } else if(lastChar == "," && e.keyCode == 44){
            return false;
        } else if (e.keyCode == 44 || (e.keyCode >= 48 && e.keyCode <= 57)) {
            return true;
        } else{
            return false;
        }
    }
});*/

$(document).ready(function($){

	var SPMaskFone = function (val) {
	  return val.replace(/\D/g, '').length === 11 ? '(00) 0.0000-0000' : '(00) 0000-00009';
	},
	spOptions = {
	  onKeyPress: function(val, e, field, options) {
		  field.mask(SPMaskFone.apply({}, arguments), options);
		}
	};

	$('#fone').mask(SPMaskFone, spOptions);	  //$('#fone, #fone1').mask(SPMaskFone, spOptions);	
	
	$('#inscricao_imobiliaria').mask('00.000.000.000');

	$( "#servidor" ).autocomplete
	({
		minLength: 3,
		source: function(request,response)
		{
			$.ajax
			( {
				url: "include/servidores.php",
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
		select: function( event, ui )
		{
			$( "#servidor" ).val( ui.item.label );
			$( "#idservidor" ).val( ui.item.value );

			document.getElementById('labelservidor').innerHTML = 'Servidor selecionado com sucesso';
			document.getElementById('labelservidor').style.color = "#008000";

			return false;
		},
		search: function( event, ui )
		{
			document.getElementById('labelservidor').innerHTML = 'Selecione um servidor na lista';
			document.getElementById('labelservidor').style.color = "#FF0000";
			$( "#idservidor" ).val( null );
		}
	});
});

