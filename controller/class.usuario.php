<?php

class Usuario extends UsuarioModel {

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
        header("Location: " . BASE_ADMIN_URL . "/usuarios-form.html?msg=ok&id=" . $id);
      else
        header("Location: " . BASE_ADMIN_URL . "/usuarios-form.html?msg=erro");
      die;
    }
    // Altera o registro selecionado
    elseif ($acao == "alterar") {
      $id = getRequest("codigo");
      if ($this->update($id))
        header("Location: " . BASE_ADMIN_URL . "/usuarios-form.html?msg=ok&id=" . $id);
      else
        header("Location: " . BASE_ADMIN_URL . "/usuarios-form.html?msg=erro&id=" . $id);
      die;
    }
    elseif ($acao == "logar") {
      if ($this->verificarLogin(getRequest("login"), getRequest("senha")))
        header("Location: " . BASE_ADMIN_URL);
      else
        header("Location: " . BASE_ADMIN_URL . "/login.html?msg=erro");
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
  * @return array|bool
  */
  public function getListaAdmin($rpp, $pag_atual) {
    $lista_resultados = parent::getListaAdmin($rpp, $pag_atual);

    //cria o array com os registros já tratados
    $array_results = array();
    if($lista_resultados === false)
      return false;

    foreach ($lista_resultados as $object) {
      $object->telefone = ($object->telefone != "" ? formatarTelefone($object->telefone, "user") : null);
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

    $this->setDataCadastro(date('Y-m-d H:i:s'));
    $this->setNomeCompleto(utf8_decode(getRequest("nome_completo")));
    $this->setEmail(strtolower(getRequest("email")));
    $this->setLogin(utf8_decode(getRequest("login")));
    $this->setSenha(md5(sha1(getRequest("senha"))));
    $this->setTelefone(formatarTelefone(getRequest("telefone"), "bd"));
    $this->setOnline(0);
    $this->setIdPerfil(getRequest("id_perfil"));
    if(isset($_FILES["imagem"]["name"]) && $_FILES["imagem"]["name"] != "")
      $this->setUrlImagem(uploadArquivo($_FILES["imagem"], 'uploads/usuarios/', 'usuario', true, array('usuario/'), array(array(57, 57))));
    $this->setStatus((getRequest("status") == "A") ? "A" : "I");

    $id = $this->incluir();

    return $id;
  }

  /**
  * Altera o registro no banco de dados
  * @param int $id
  *
  * @return bool
  */
  public function update($id) {

    $this->setIdTable($id);
    $this->getByCod();

    $this->setNomeCompleto(utf8_decode(getRequest("nome_completo")));
    $this->setEmail(strtolower(getRequest("email")));
    $this->setLogin(utf8_decode(getRequest("login")));
    if (getRequest("senha") != "")
      $this->setSenha(md5(sha1(getRequest("senha"))));
    $this->setTelefone(formatarTelefone(getRequest("telefone"), "bd"));
    $this->setUltimoAcesso(formatarData("bd2", getRequest("ultimo_acesso")));
    if($id != 1)
      $this->setIdPerfil(getRequest("id_perfil"));
    if(isset($_FILES["imagem"]["name"]) && $_FILES["imagem"]["name"] != ""){
      if($this->getUrlImagem())
        deleteArquivo('uploads/usuarios/usuario/'.$this->getUrlImagem());
      $this->setUrlImagem(uploadArquivo($_FILES["imagem"], 'uploads/usuarios/', 'usuario', true, array('usuario/'), array(array(57, 57))));
    }
    $this->setStatus((getRequest("status") == "A") ? "A" : "I");


    return $this->alterar();
  }

  public function verificarLogin($user, $pass) {

    $this->setLogin($user);
    $this->setSenha(md5(sha1($pass)));

    if ($id = $this->logar()) {
      $this->setIdTable($id);
      $this->getByCod();
      $this->atualizaAcessos();

      setSession("autorizado", "acesso", "user");
      setSession($this->getIdUsuario(), "id", "user");
      setSession($this->getIdPerfil(), "id_perfil", "user");
      setSession($this->getNomeCompleto(), "nome", "user");
      setSession($this->getEmail(), "email", "user");
      setSession($this->getUltimoAcesso(), "ultimo_acesso", "user");

      return true;
    } else {
      setSession("negado", "acesso", "user");
      return false;
    }
  }


  /* Atualiza quantidade de acessos */

  public function atualizaAcessos() {
    $this->setIdSessao(session_id());
    return $this->updateAccess();
  }

  /**
  * Método de logout do site
  *
  */
  public function sair() {
    $this->setIdTable(getSession("id", "user"));
    return $this->logout();
  }

  /**
  * Verifica se usuário está online
  *
  * @param $id_sessao
  * @return Boolean
  */
  public function online($id_sessao) {
    return $this->verificaOnline($id_sessao);
  }

}
