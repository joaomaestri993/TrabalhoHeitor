<?php


class PaginaModel extends Model {

  /**
   * Atributos privados da classe
   *
   */
  protected $id_table;
  protected $nome;
  protected $titulo;
  protected $grupo;
  protected $ordem;

  protected $table = 'paginas';
  protected $key = "id_pagina";

  protected function getGrupos() {
    $query = "SELECT distinct(grupo) as nome from ".$this->table." order by grupo";
    $resultado = self::$conexao->runQuery($query);
    return $this->getListaRegistros($resultado);
  }

  protected function getIdsByGrupo($grupo) {
    $query = "SELECT ".$this->key." FROM ".$this->table." WHERE grupo = '".removerAcento($grupo)."'";
    $resultado = self::$conexao->runQuery($query);
    return $this->getArrayRegistros($resultado);
  }

  protected function getPaginas() {
    $query = "SELECT * from ".$this->table." ORDER BY grupo, ordem";
    $resultado = self::$conexao->runQuery($query);
    return $this->getListaRegistros($resultado);
  }

}
