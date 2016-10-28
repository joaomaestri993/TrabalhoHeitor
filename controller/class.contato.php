<?php

class Contato extends ContatoModel {

  public function __construct($submit = false) {
    parent::__construct($submit);
  }

  /**
  * Verifica o post enviado pelo formulário
  *
  */
  public function postProcess($acao) {

    // Inclui o registro
    if ($acao == "incluir") {
      $id = $this->insert();
      if ($id)
        header("Location: " . BASE_ADMIN_URL . "/contatos-form.html?msg=ok&id=" . $id);
      else
        header("Location: " . BASE_ADMIN_URL . "/contatos-form.html?msg=erro");
      die;
    }
    // Altera o registro selecionado
    elseif ($acao == "alterar") {
      $id = getRequest("codigo");
      if ($this->update($id))
        header("Location: " . BASE_ADMIN_URL . "/contatos-form.html?msg=ok&id=" . $id);
      else
        header("Location: " . BASE_ADMIN_URL . "/contatos-form.html?msg=erro&id=" . $id);
      die;
    }
    // cliente salvando contato pelo site
    elseif ($acao == "site_incluir") {
      $envio = $this->enviar();
      if($envio)
        header("Location: " . BASE_SITE_URL . "/fale-conosco.html?msg=ok");
      else
        header("Location: " . BASE_SITE_URL . "/fale-conosco.html?msg=erro");
      die;
    }
  }

  /**
  * Busca o total de registros encontrados na tabela
  *
  * @return int
  */
  public function getTotalAdmin() {
    return parent::getTotalAdmin();
  }

  /**
  * Retorna lista com registros cadastrados no banco
  *
  * @param Int $rpp
  * @param Int $pag_atual
  *
  * @return array
  */
  public function getListaAdmin($rpp, $pag_atual) {
    $lista_resultados = parent::getListaAdmin($rpp, $pag_atual);

    //cria o array com os registros já tratados
    $array_results = array();
    if($lista_resultados === false)
      return false;

    foreach ($lista_resultados as $object) {
      $object->telefone = ($object->telefone != "" ? formatarTelefone($object->telefone, "user") : null);
      $object->img_status = ($object->status == "A" ? '<a class="btn_status_ativo" title="Registro Ativo">Status</a>' : '<a class="btn_status_inativo" title="Status Inativo">Status</a>');
      $array_results[] = $object;
    }

    return $array_results;
  }

  /**
  * Insere o registro no banco de dados
  *
  */
  public function insert() { 

    $this->setDataCadastro(date('Y-m-d H:i:s'));
    $this->setIp($_SERVER['REMOTE_ADDR']);
    $this->setNome(utf8_decode(getRequest('nome')));
    $this->setEmail(strtolower(getRequest('email')));
    $this->setAssunto(utf8_decode(getRequest('assunto')));
    $this->setTelefone(formatarTelefone(getRequest('telefone'), "bd"));
    $this->setMensagem($_POST['mensagem']);
    $this->setStatus((getRequest("status") == "A") ? "A" : "P");

    $id = $this->incluir();
    return $id;
  }

  /**
  * Altera o registro no banco de dados
  *
  */
  public function update($id) {
    $this->setIdTable($id);
    $this->getByCod();

    $this->setNome(utf8_decode(getRequest("nome")));
    $this->setEmail(strtolower(getRequest("email")));
    $this->setAssunto(utf8_decode(getRequest("assunto")));
    $this->setTelefone(formatarTelefone(getRequest("telefone"), "bd"));
    $this->setMensagem($_POST['mensagem']);
    $this->setStatus((getRequest("status") == "A") ? "A" : "P");

    return $this->alterar();
  }

  # cliente enviando contato pelo site
  protected function enviar(){
   
    $this->setDataCadastro(date('Y-m-d H:i:s'));
    $this->setIp($_SERVER["REMOTE_ADDR"]);
    $this->setNome(utf8_decode(getRequest("nome")));
    $this->setEmail(strtolower(getRequest("email")));
    $this->setAssunto(utf8_decode(getRequest("assunto")));
    $this->setTelefone(formatarTelefone(getRequest("telefone"), "bd"));
    $this->setMensagem(utf8_decode(getRequest("mensagem")));
    $this->setStatus("P");
    $id = $this->incluir();

    ini_set("allow_url_fopen", 1);
    $mensagem = file_get_contents("view/mails/contato.html");

    $mensagem = str_replace("##base_url_site##", BASE_SITE_URL, $mensagem);
    $mensagem = str_replace("##hora##", date("d/m/Y H:i:s"), $mensagem);
    $mensagem = str_replace("##ip##", $this->getIp(), $mensagem);
    $mensagem = str_replace("##nome##", $this->getNome(), $mensagem);
    $mensagem = str_replace("##email##", $this->getEmail(), $mensagem);
    $mensagem = str_replace("##telefone##", $this->getTelefone(), $mensagem);
    $mensagem = str_replace("##mensagem##", nl2br($this->getMensagem()), $mensagem);

    $mensagem = wordwrap($mensagem);
    $assunto = utf8_decode("Contato realizado no site.");
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'From: '.utf8_encode(SITE_TITLE).' <'.$this->getEmail().'>' . "\r\n";
    $envio = mail(SITE_EMAIL, $assunto, $mensagem, $headers);

    return($id && $envio);
  }

}
