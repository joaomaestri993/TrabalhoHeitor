<?php

class UsuarioModel extends Model {

  /**
   *
   * Atributos privados da classe
   */
  protected $id_table;
  protected $id_perfil;
  protected $data_cadastro;
  protected $nome_completo;
  protected $email;
  protected $login;
  protected $senha;
  protected $telefone;
  protected $ultimo_acesso;
  protected $qtde_acessos;
  protected $id_sessao;
  protected $url_imagem;
  protected $status;

  protected $table = 'usuarios_admin';
  protected $key = "id_usuario";

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
      $query .= " AND upper(nome_completo) LIKE upper('%" . getSession("nome", $this->filtro) . "%')";
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
      $query .= " AND upper(nome_completo) LIKE upper('%" . getSession("nome", $this->filtro) . "%')";
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

  /**
   * Verifica se as credenciais existem cadastradas
   *
   */
  public function logar() {
    $query = "SELECT id_usuario FROM " . $this->table . " WHERE login = '" . $this->login . "' AND senha = '" . $this->senha . "' AND status = 'A'";
    $resultado = self::$conexao->runQuery($query);
    $resultado = $this->getLinhaRegistro($resultado);
    if($resultado !== false){
      return $resultado->id_usuario;
    }else{
      return false;
    }
  }

  /**
   * Atualiza a quantidade de acessos e último acesso
   *
   */
  public function updateAccess() {
    $query = "UPDATE " . $this->table . " SET ultimo_acesso = CURRENT_TIMESTAMP, qtde_acessos = qtde_acessos + 1, id_sessao = '" . $this->getIdSessao() . "' WHERE id_usuario = '" . $this->getIdTable() . "'";
    $resultado = self::$conexao->runQuery($query);
    if ($resultado)
      return true;
    else
      return false;
  }

  /**
   * Efetua o logout no banco
   *
   */
  public function logout() {
    $query = "UPDATE " . $this->table . " SET id_sessao = null WHERE id_usuario = '" . $this->getIdTable() . "'";
    $resultado = self::$conexao->runQuery($query);
    if ($resultado)
      return true;
    else
      return false;
  }

  /**
   * Verifica se usuário está online
   *
   * @param $id_sessao
   * @return Boolean
   */
  public function verificaOnline($id_sessao) {
    $query = "SELECT online FROM `sessions` WHERE id = '$id_sessao'";
    $resultado = self::$conexao->runQuery($query);
    $dados = $this->getLinhaRegistro($resultado);
    if ($dados !== false) {
      if ($dados->online == "S")
        return true;
      else
        return false;
    } else {
      return false;
    }
  }

}
