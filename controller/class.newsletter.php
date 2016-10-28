<?php

class Newsletter extends NewsletterModel {

  public function __construct($submit = false) {
    parent::__construct($submit);
  }

  /**
  * Verifica o post enviado pelo formulário
  *
  */
  public function postProcess($acao) {
    // requisicoes ajax
    if(getRequest("ajax")) {
      if($acao == "incluir") {
        if($this->existeEmail())
          die("exists");
        $id = $this->insert();
        if($id)
          die("ok");
        die("erro");
      }
    }
    // requisicoes comuns
    # Inclui o registro
    if ($acao == "incluir") {
      $id = $this->insert();
      if ($id)
        header("Location: " . BASE_ADMIN_URL . "/news-form.html?msg=ok&id=" . $id);
      else
        header("Location: " . BASE_ADMIN_URL . "/news-form.html?msg=erro");
      die;
    }
    # Altera o registro selecionado
    elseif ($acao == "alterar") {
      $id = getRequest("codigo");
      if ($this->update($id))
        header("Location: " . BASE_ADMIN_URL . "/news-form.html?msg=ok&id=" . $id);
      else
        header("Location: " . BASE_ADMIN_URL . "/news-form.html?msg=erro&id=" . $id);
      die;
    }
    # exportar para planilha excel
    elseif ($acao == "exportar") {
      $news = $this->listAll();
      if($news !== false) {
        header("Content-type: application/vnd.ms-excel");
        header("Content-type: application/force-download");
        header("Content-Disposition: attachment; filename=newsletter.xls");
        header("Pragma: no-cache");
        $planilha  = '<style type="text/css">.texto { mso-number-format:"\@"; }</style>';
        $planilha .= '<table width="100%" cellpadding="0" cellspacing="0" border="1" style="font-family:Arial, serif;font-size:15px">';
        $planilha .= '<tr style="color:#D90000">';
        $planilha .= '<th>NOME</th>';
        $planilha .= '<th>EMAIL</th>';
        $planilha .= '</tr>';
        foreach($news as $new) {
          $planilha .= '<tr>';
          $planilha .= '<td>'.utf8_decode($new->nome).'</td>';
          $planilha .= '<td>'.$new->email.'</td>';
          $planilha .= '</tr>';
        }
        $planilha .= '</table>';
        die($planilha);
      }
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
  * @param int $rpp
  * @param int $pag_atual
  * @return array|bool
  */
  public function getListaAdmin($rpp, $pag_atual) {
    $lista_resultados = parent::getListaAdmin($rpp, $pag_atual);

    //cria o array com os registros já tratados
    $array_results = array();
    if($lista_resultados === false)
      return false;

    foreach ($lista_resultados as $object) {
      $object->img_status = ($object->status == "A" ? ADMIN_IMAGES_URL."/btn_ball_green.gif" : ADMIN_IMAGES_URL."/btn_ball_red.gif");
      $array_results[] = $object;
    }

    return $array_results;
  }

  /**
  * Insere o registro no banco de dados
  *
  */
  public function insert() {

    $this->setNome(utf8_decode(getRequest("nome")));
    $this->setEmail(strtolower(getRequest("email")));
    $this->setStatus((getRequest("status") == "A") ? "A" : "P");
    
    $id = $this->incluir($dados);
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
    $this->setStatus((getRequest("status") == "A") ? "A" : "I");
    
    return $this->alterar();
  }

  /**
  * Testa se determinado email já está cadastrado
  *
  * @return bool
  */
  public function existeEmail() {
    $this->setEmail(getRequest("email"));
    return $this->getByEmail();
  }

}
