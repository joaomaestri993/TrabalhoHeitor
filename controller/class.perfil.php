<?php

class Perfil extends PerfilModel {

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
        header("Location: " . BASE_ADMIN_URL . "/perfis-form.html?msg=ok&id=" . $id);
      else
        header("Location: " . BASE_ADMIN_URL . "/perfis-form.html?msg=erro");
      die;
    }
    // Altera o registro selecionado
    elseif ($acao == "alterar") {
      $id = getRequest("codigo");
      if ($this->update($id))
        header("Location: " . BASE_ADMIN_URL . "/perfis-form.html?msg=ok&id=" . $id);
      else
        header("Location: " . BASE_ADMIN_URL . "/perfis-form.html?msg=erro&id=" . $id);
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
   * @return array
   */
  public function getListaAdmin($rpp, $pag_atual) {
    $lista_resultados = parent::getListaAdmin($rpp, $pag_atual);
    return $lista_resultados;
  }

  /**
   * Insere o registro no banco de dados
   *
   */
  public function insert() {
    $this->setNome(utf8_decode(getRequest("nome")));
    $id = $this->incluir();
    return $id;
  }

  /**
   * Altera o registro no banco de dados
   *
   */
  public function update($id) {
    $return = false;
    if($id != 1) {
      $this->setIdTable($id);
      $this->getByCod();
      $this->setNome(utf8_decode(getRequest("nome")));
      $return = $this->alterar();
      $paginas = getArrayPost("pagina");
      $this->delPaginas();
      foreach($paginas as $pagina)
        $this->incluiPagina($pagina);
    }
    return $return;
  }

  public function getPerfis() {
    return parent::listAll('');
  }

  public function getPermicoes($id_registro, $id_pagina){
    return $this->temPermissao($id_registro, $id_pagina);
  }
  
}
