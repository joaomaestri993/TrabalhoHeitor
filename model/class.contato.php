<?php

class ContatoModel extends Model {

  /**
   *
   * Atributos privados da classe
   */
  protected $id_table;
  protected $data_cadastro;
  protected $ip;
  protected $nome;
  protected $telefone;
  protected $email;
  protected $assunto;
  protected $mensagem;
  protected $status;

  protected $table = 'contatos';
  protected $key = "id_contato";

  /**
   * Busca o total de registros encontrados na tabela
   * @author Alex Cardozo
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
    if (getSession("telefone", $this->filtro) != "")
      $query .= " AND telefone = '" . getSession("telefone", $this->filtro) . "'";
    if (getSession("status", $this->filtro) != "")
      $query .= " AND status = '" . getSession("status", $this->filtro) . "'";
    # fim dos filtros de pesquisa

    $resultado = self::$conexao->runQuery($query);
    $dados = $this->getLinhaRegistro($resultado);
    return isset($dados->total) ? $dados->total : 0;
  }

  /**
   * Retorna lista com registros cadastrados no banco
   * @author Alex Cardozo
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
    if (getSession("telefone", $this->filtro) != "")
      $query .= " AND telefone = '" . getSession("telefone", $this->filtro) . "'";
    if (getSession("status", $this->filtro) != "")
      $query .= " AND status = '" . getSession("status", $this->filtro) . "'";
    # fim dos filtros de pesquisa

    $query .= " ORDER BY " . $this->key . " LIMIT " . (($pag_atual - 1) * $rpp) . ", " . $rpp . ";";

    $resultado = self::$conexao->runQuery($query);
    return $this->getListaRegistros($resultado);
  }

}
