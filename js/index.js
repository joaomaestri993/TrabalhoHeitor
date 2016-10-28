// dom ready
$(document).ready(function(){

  /* MÉTODOS-PADRÃO */  

  $('.banner_principal').cycle({ 
    fx:     'fade', 
    timeout: 3000, 
    next:   '#next2', 
    prev:   '#prev2' 
  });

  // abas
  $(".tabs_links li").bind("click", function(){
    var id = $(this).attr("data-id");
    $(".tabs_links li").removeClass("ativo");
    $(this).addClass("ativo");
    $(".tab_bloco").removeClass("ativo");
    $("#"+id).addClass("ativo");
  });
  // função esperta para placeholders (no IE não funciona password)
  $(".place").bind("focus", function(){
    if($(this).val() == $(this).attr("data-place")) {
      $(this).val("");
      if($(this).attr("name") == "senha")
        $(this).prop("type", "password");
    }
  }).bind("blur", function(){
    if($(this).val() == "") {
      $(this).val($(this).attr("data-place"));    
      if($(this).attr("name") == "senha")
        $(this).prop("type", "text");
    }
  }).blur();
  // mascara de moeda
  if($('.money').length > 0)
    $('.money').mask('000.000.000.000.000,00', {reverse: true});
  // mascara de inteiros
  $('.int').keyup(function(){
    var texto = $(this).val();
    var ret = "";
    for(i=0; i < texto.length; i++){
      if(texto.charCodeAt(i) > 47 && texto.charCodeAt(i) < 58)
        ret += texto[i];
    }
    $(this).val(ret);
  }).blur(function(){
    var texto = $(this).val();
    var ret = "";
    for(i=0; i < texto.length; i++){
      if(texto.charCodeAt(i) > 47 && texto.charCodeAt(i) < 58)
        ret += texto[i];
    }
    $(this).val(ret);
  });
  /* FIM DOS MÉTODOS-PADRÃO */

  // newsletter rodapé
  $("#btn_news").bind("click", function(){
    if(!validaForm(document.form_news, false))
      return false;
    $.ajax({
      type: "POST",
      url: 'ajax-news.html',
      data:{
        ajax: true,
        acao: "incluir",
        nome: $("#nome_news").val(),
        email: $("#email_news").val(),
        status: "A"
      },
      success: function(ret) {
        if(ret == "erro")
          alert("Erro ao se cadastrar!");
        else if(ret == "exists")
          alert("Este email já está cadastrado!");
        else if(ret == "ok")
          alert("Cadastro realizado com sucesso!");
      }
    });
    return true;
  });

});

function rolarpagina(elemento, segundos) {
  $('html, body').animate({
    scrollTop: $(elemento).offset().top - 375
  }, segundos * 1000);
}

function checkMail (email) {
  er = /^[a-zA-Z0-9][a-zA-Z0-9\._-]+@([a-zA-Z0-9\._-]+\.)[a-zA-Z-0-9]{2}/;
  if(er.exec(email))
    return true;
  else
    return false;
}

// funcao de validacao esperta, que funcionará com todos os forms do site - Eduardo Galvani
function validaForm(form, place){
  var elementos = form.elements;
  var border;
  for(var i=0; i < elementos.length; i++) {
    // ignorar campos com continue
    if($("#"+elementos[i].id).hasClass("opcional"))
      continue;
    // teste próprio para emails
    if((elementos[i].name == "email" && !checkMail(elementos[i].value)) ||
     (place && elementos[i].value == $("#"+elementos[i].id).attr("data-place"))) {
      elementos[i].focus();
    border = elementos[i].style.border;
    elementos[i].style.border = "1px solid #CC0000";
    setTimeout(function(){
      elementos[i].style.border = border;
    }, 1500);
    return false;
  }
    // campos-padrão
    else if(elementos[i].value == "" || (place && elementos[i].value == $("#"+elementos[i].id).attr("data-place"))) {
      elementos[i].focus();
      border = elementos[i].style.border;
      elementos[i].style.border = "1px solid #CC0000";
      setTimeout(function(){
        elementos[i].style.border = border;
      }, 1500);
      return false;
    }
  }
  return true;
}

function maskTelephone(textbox, blur) {

  var telephone = textbox.value.replace(/[^0-9]/g, '');

  if (/^\d{1,2}$/.test(telephone)) {
    telephone = '(' + telephone + ')';
  }
  if (/^\d{3,}$/.test(telephone)) {
    telephone = '(' + telephone.substring(0, 2) + ')' + telephone.substring(2);
  }
  if (/^.\d{2}.\d{5,8}$/.test(telephone)) {
    telephone = telephone.substring(0, 8) + '-' + telephone.substring(8);
  }
  if (/^.\d{2}.\d{9,}$/.test(telephone)) {
    telephone = telephone.substring(0, 9) + '-' + telephone.substring(9, 13);
  }
  var caretPos = getCursorPosition(textbox);
  var lastLength = textbox.value.length;
  textbox.value = telephone;
  var newLength = textbox.value.length;
  if (!blur) {
    setCursorPosition(textbox, caretPos + newLength - lastLength);
  }
}


function editTelephone(textbox, ev) {

  var event = ev ? ev : window.event;
  var code = event.which ? event.which : event.keyCode;

  if (!(code == 8 || code == 9 || (code >= 35 && code <= 57) || (code >= 96 && code <= 105))) {
    event.preventDefault();
    return false;
  }

  var caretPos = getCursorPosition(textbox);

  if (code == 8) {
    var charBefore = textbox.value.charAt(caretPos - 1);
    while (/[^\d]/.test(charBefore)) {
      setCursorPosition(textbox, --caretPos);
      charBefore = textbox.value.charAt(caretPos - 1);
    }
  }

  if (code == 46) {
    var charAfter = textbox.value.charAt(caretPos);
    while (/[^\d]/.test(charAfter)) {
      setCursorPosition(textbox, ++caretPos);
      charAfter = textbox.value.charAt(caretPos);
    }
  }

  return true;
}

function getCursorPosition(oField) {
 var iCaretPos = 0;
 // IE Support
 if (document.selection) {
   oField.focus ();
   var oSel = document.selection.createRange();
   oSel.moveStart ('character', -oField.value.length);
   iCaretPos = oSel.text.length;
 }
 // Firefox support
 else if (oField.selectionStart || oField.selectionStart == '0')
   iCaretPos = oField.selectionStart;
 return (iCaretPos);
}

function setCursorPosition(el, index) {
  if (el.createTextRange) { 
    var range = el.createTextRange(); 
    range.move('character', index); 
    range.select(); 
  } else if (el.selectionStart != null) { 
    el.focus(); 
    el.setSelectionRange(index, index); 
  }
}

function alert(texto) {
  $("#jquery-ui").html(texto).dialog({
    modal: true,
    title: "AVISO",
    dialogClass: "no-close",
    buttons: [
    {
      text: "OK",
      click: function() {
        $(this).dialog("close");
      }
    }
    ]
  });
}