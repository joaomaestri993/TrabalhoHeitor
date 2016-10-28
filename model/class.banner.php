<?php

class BannerModel extends Model {

  /**
   *
   * Atributos privados da classe
   */
  protected $id_table;
  protected $nome_banner;
  protected $link_banner;
  protected $url_banner;
  protected $status;

  protected $table = 'banners';
  protected $key = "id_banner";

  /**
   * Busca o total de registros encontrados na tabela
   *
   * @return int
   */
  protected function getTotalAdmin() {
    $query = "SELECT COUNT(" . $this->key . ") AS total FROM " . $this->table . " WHERE 1 = 1";

    # filtros de pesquisa
    if (getSession("id", $this->filtro) > 0)
      $query .= " AND " . $this->key . " = '" . getSession("id", $this->filtro) . "'";
    if (getSession("nome_banner", $this->filtro) != "")
      $query .= " AND upper(nome_banner) LIKE upper('%" . getSession("nome_banner", $this->filtro) . "%')";
    if (getSession("link_banner", $this->filtro) != "")
      $query .= " AND upper(link_banner) LIKE upper('%" . getSession("link_banner", $this->filtro) . "%')";
    if (getSession("status", $this->filtro) != "")
      $query .= " AND status = '" . getSession("status", $this->filtro) . "'";
    # fim dos filtros de pesquisa

    $resultado = self::$conexao->runQuery($query);
    $dados = $this->getLinhaRegistro($resultado);
    return isset($dados->total) ? $dados->total : 0;
  }

  /**
   * Retorna lista com registros cadastrados no banco
   *
   * @param Int $rpp
   * @param Int $pag_atual
   * @return array
   */
  protected function getListaAdmin($rpp, $pag_atual) {
    $query = "SELECT * FROM " . $this->table . " WHERE 1 = 1";

    # filtros de pesquisa
    if (getSession("id", $this->filtro) > 0)
      $query .= " AND " . $this->key . " = '" . getSession("id", $this->filtro) . "'";
    if (getSession("nome_banner", $this->filtro) != "")
      $query .= " AND upper(nome_banner) LIKE upper('%" . getSession("nome_banner", $this->filtro) . "%')";
    if (getSession("link_banner", $this->filtro) != "")
      $query .= " AND upper(link_banner) LIKE upper('%" . getSession("link_banner", $this->filtro) . "%')";
    if (getSession("status", $this->filtro) != "")
      $query .= " AND status = '" . getSession("status", $this->filtro) . "'";
    # fim dos filtros de pesquisa

    $query .= " ORDER BY " . $this->key . " LIMIT " . (($pag_atual - 1) * $rpp) . ", " . $rpp . ";";

    $resultado = self::$conexao->runQuery($query);
    return $this->getListaRegistros($resultado);
  }

  protected function listaBanner($status){
    $query = "SELECT * FROM ".$this->table." WHERE status = '".$status."';";

    $resultado = self::$conexao->runQuery($query);
    return $this->getListaRegistros($resultado);
  }

}