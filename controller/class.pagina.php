<?php


class Pagina extends PaginaModel {

  public function __construct($submit = false) {
    parent::__construct($submit);
  }

  public function getGrupos() {
    return parent::getGrupos();
  }

  public function getIdsByGrupo($grupo) {
    $ids = parent::getIdsByGrupo($grupo);
    $return = "";
    foreach($ids as $id) {
      $return .= $id["id_pagina"] . ",";
    }
    $return = substr($return, 0, -1);
    return $return;
  }

  public function getPaginas() {
    return parent::getPaginas();
  }

}
