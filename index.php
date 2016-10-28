<?php
session_start();
session_cache_expire(30);
define("ID_SESSAO", session_id()); // Registra a variavel com ID de sessão

#busca informações do arquivo de configuração
require_once("lib/setup/init.inc.php");

$session = new Session();

$pagina_requisitada = (isset($_GET['pagina']) && ($_GET['pagina'] != "")) ? $_GET['pagina'] : 'home.html';
$url_rewrite = new UrlRewrite();


// Remontam todos parametros passados na URL
$url_params = explode("?", str_replace("&amp;", "&", $_SERVER['REQUEST_URI']));
if (count($url_params) > 1) {
  $params = explode("&", $url_params[1]);
  for ($i = 0; $i < count($params); $i++) {
    $param = explode("=", $params[$i]);
    if(isset($param[0]) && isset($param[1]))
      $_GET[$param[0]] = $param[1];
  }
}

$array_requisicao = explode("/", $pagina_requisitada);
$pasta = '';
if (count($array_requisicao) > 1) {

  $pasta = $array_requisicao[0];

  if($pasta == "sistema") {
    $url_rewrite->setTipoUrl("A");
    // testando se o usuario admin está logado
    if($array_requisicao[1] != "login.html" && getSession("user") == "") {
      header("Location: login.html");
      die;
    }
    if($array_requisicao[1] == "")
      $pagina_requisitada = "sistema/modulos/common/home.phtml";
    else {
      $url_rewrite->setUrlDestino($array_requisicao[1]);
      $pagina_requisitada = ($url_rewrite->getOrigem()) ? $url_rewrite->getUrlOrigem() : "404.html";
    }
  }
  // url amigaveis com ID's aqui
  else {
    switch($pasta) {
      case "exemplos" :
        $link = explode("-", $array_requisicao[1]);
        $id_table = str_replace(".html", "", $link[count($link)-1]); // id na ultima posicao
        if($id_table > 0){
          $_GET['id_exemplo'] = $id_table;
          $pagina_requisitada = 'view/exemplo.phtml';
        }
        break;
    }
  }

} else {  
  $url_rewrite->setTipoUrl("S");
  // casos de migracao de php para html
  if (substr_count($pagina_requisitada, '.php') > 0) {
    $url_rewrite->setUrlOrigem($pagina_requisitada);
    $pagina_requisitada = ($url_rewrite->getDestino()) ? $url_rewrite->getUrlDestino() : "404.html";
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: ".BASE_SITE_URL."/".$pagina_requisitada);
  }else {
    $url_rewrite->setUrlDestino($pagina_requisitada);
    $pagina_requisitada = ($url_rewrite->getOrigem()) ? $url_rewrite->getUrlOrigem() : "404.html";
  }
}
//caso o site esteja passando por alguma atualização
if(SITE_MANUTENCAO == 'S'){
  $ips_liberados = array(SITE_IPS_LIBERADOS);
  if(!in_array($_SERVER['REMOTE_ADDR'], $ips_liberados) && $pasta != 'sistema'){
    $pagina_requisitada = (file_exists($pagina_requisitada)) ? 'view/construcao.phtml' : "404.html";
  }
}else{
  $pagina_requisitada = (file_exists($pagina_requisitada)) ? $pagina_requisitada : "404.html";
}

$pagina_requisitada = ($pagina_requisitada == "404.html") ? "view/error/404.phtml" : $pagina_requisitada;
include_once($pagina_requisitada);
