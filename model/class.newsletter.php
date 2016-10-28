<?php

class NewsletterModel extends Model {

  /**
   *
   * Atributos privados da classe
   */
  protected $id_table;
  protected $nome;
  protected $email;
  protected $status;

  protected $table = 'newsletter';
  protected $key = "id_newsletter";

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
    if (getSession("nome", $this->filtro) != "")
      $query .= " AND upper(nome) LIKE upper('%" . getSession("nome", $this->filtro) . "%')";
    if (getSession("email", $this->filtro) != "")
      $query .= " AND upper(email) LIKE upper('%" . getSession("email", $this->filtro) . "%')";
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
    if (getSession("nome", $this->filtro) != "")
      $query .= " AND upper(nome) LIKE upper('%" . getSession("nome", $this->filtro) . "%')";
    if (getSession("email", $this->filtro) != "")
      $query .= " AND upper(email) LIKE upper('%" . getSession("email", $this->filtro) . "%')";
    if (getSession("status", $this->filtro) != "")
      $query .= " AND status = '" . getSession("status", $this->filtro) . "'";
    # fim dos filtros de pesquisa

    $query .= " ORDER BY " . $this->key . " LIMIT " . (($pag_atual - 1) * $rpp) . ", " . $rpp . ";";

    $resultado = self::$conexao->runQuery($query);
    return $this->getListaRegistros($resultado);
  }

  /**
   * Testa se determinado email já está cadastrado
   *
   * @return bool
   */
  public function getByEmail() {
    $query = "SELECT email FROM ".$this->table." WHERE upper(email) = upper('".$this->email."')";
    $resultado = self::$conexao->runQuery($query);
    $dados = $this->getLinhaRegistro($resultado);
    if($dados === false)
      return false;
    return true;
  }

}
