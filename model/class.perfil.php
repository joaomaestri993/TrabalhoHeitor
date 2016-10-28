<?php


class PerfilModel extends Model {

  /**
  *
  * Atributos privados da classe
  */
  protected $id_table;
  protected $nome;

  protected $table = 'perfis';
  protected $key = "id_perfil";

  /**
  * Busca o total de registros encontrados na tabela
  *
  * @return int
  */
  protected function getTotalAdmin() {
    $query = "SELECT COUNT(" . $this->key . ") AS total FROM " . $this->table . " WHERE 1 = 1";

    #filtros de pesquisa
    if (getSession("id", $this->filtro) > 0)
      $query .= " AND " . $this->key . " = '" . getSession("id", $this->filtro) . "'";
    if (getSession("nome", $this->filtro) != "")
      $query .= " AND upper(nome) LIKE upper('%" . getSession("nome", $this->filtro) . "%')";
    #fim do filtro de pesquisa

    $resultado = self::$conexao->runQuery($query);
    $dados = $this->getLinhaRegistro($resultado);
    return isset($dados->total) ? $dados->total : 0;
  }

  /**
  * Retorna lista com registros cadastrados no banco
  *
  * @param Int $rpp
  * @param Int $pag_atual
  *
  * @return array
  */
  protected function getListaAdmin($rpp, $pag_atual) {
    $query = "SELECT * FROM " . $this->table . " WHERE 1 = 1";

    # filtros de pesquisa
    if (getSession("id", $this->filtro) > 0)
      $query .= " AND " . $this->key . " = '" . getSession("id", $this->filtro) . "'";
    if (getSession("nome", $this->filtro) != "")
      $query .= " AND upper(nome) LIKE upper('%" . getSession("nome", $this->filtro) . "%')";
    # fim dos filtros de pesquisa

    $query .= " ORDER BY " . $this->key . " LIMIT " . (($pag_atual - 1) * $rpp) . ", " . $rpp . ";";

    $resultado = self::$conexao->runQuery($query);
    return $this->getListaRegistros($resultado);
  }

  /**
  * Testa a permissão de acesso de telas para o usuário logado
  * @param $page_id
  * @return boolean
  */
  public static function testaPermissoes($page_id) {
    $id_perfil = getSession("id_perfil", "user");
    $query = "SELECT * from perfis_paginas where id_perfil = ".$id_perfil." AND id_pagina IN(".$page_id.")";
    $resultado = self::$conexao->runQuery($query);
    if($resultado->fetch(PDO::FETCH_ASSOC) < 1)
      return false;
    return true;
  }

  /**
  * Testa a permissão de acesso da tela de diferentes perfis
  * @param $id_perfil
  * @param $id_pagina
  * @return boolean
  */
  public function temPermissao($id_perfil, $id_pagina) {
    $query = "SELECT * from perfis_paginas where id_perfil = ".$id_perfil." AND id_pagina = ".$id_pagina.";";
    $resultado = self::$conexao->runQuery($query);
    if($resultado->fetch(PDO::FETCH_ASSOC) < 1)
      return false;
    return true;
  }

  /**
  * Exclui permissoes das páginas do perfil atual
  * @return boolean
  */
  protected function delPaginas() {
    $query = "DELETE FROM perfis_paginas where id_perfil = " . $this->getIdTable();
    $resultado = self::$conexao->runQuery($query);
    return $resultado;
  }

  /**
  * Insere permissao para uma página do perfil atual
  * @param $id_pagina
  * @return boolean
  */
  protected function incluiPagina($id_pagina) {
    $query = "INSERT INTO perfis_paginas VALUES (".$this->getIdTable().", ".$id_pagina.")";
    $resultado = self::$conexao->runQuery($query);
    return $resultado;
  }

}
