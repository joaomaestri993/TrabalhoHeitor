<?php

class Banner extends BannerModel {

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
        header("Location: " . BASE_ADMIN_URL . "/banners-form.html?msg=ok&id=" . $id);
      else
        header("Location: " . BASE_ADMIN_URL . "/banner-form.html?msg=erro");
      die;
    }
    // Altera o registro selecionado
    elseif ($acao == "alterar") {
      $id = getRequest("codigo");
      if ($this->update($id))
        header("Location: " . BASE_ADMIN_URL . "/banners-form.html?msg=ok&id=" . $id);
      else
        header("Location: " . BASE_ADMIN_URL . "/banners-form.html?msg=erro&id=" . $id);
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

    $this->setNomeBanner(utf8_decode(getRequest("nome_banner")));
    $this->setLinkBanner(strtolower(getRequest("link_banner")));
    if(isset($_FILES["imagem"]["name"]) && $_FILES["imagem"]["name"] != "")
      $this->setUrlBanner(uploadArquivo($_FILES["imagem"], 'uploads/banners/', 'banner', true, array('banner/'), array(array(905, 190))));
    $this->setStatus((getRequest("status") == "A") ? "A" : "I");

    $id = $this->incluir();
    return $id;

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

    $this->setNomeBanner(utf8_decode(getRequest("nome_banner")));
    $this->setLinkBanner(strtolower(getRequest("link_banner")));
    if(isset($_FILES["imagem"]["name"]) && $_FILES["imagem"]["name"] != ""){
      deleteArquivo('uploads/banners/banner/'.$this->getUrlBanner());
      $this->setUrlBanner(uploadArquivo($_FILES["imagem"], 'uploads/banners/', 'banner', true, array('banner/'), array(array(905, 190))));
    }
    $this->setStatus((getRequest("status") == "A") ? "A" : "I");

    return $this->alterar();
  }

  public function getListaBanner($status){
    return $this->listaBanner($status);
  }

}
