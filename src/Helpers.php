<?php

/**
 * Retorna o caminho dos arquivos da sessao
 *
 * @return string
 */
function SessionAuthPathSession()
{
  @session_start();
  $id_session = $_SESSION['SessionAuth_id'];
  $filePath = config('SessionAuth.session_path') . "/$id_session.json";

  return $filePath;
}

/**
 * Retorna um objeto com os dados da sessão
 *
 * @return object
 */
function SessionAuth()
{
    $filePath = SessionAuthPathSession();

    $session = json_decode( file_get_contents($filePath));
    $x=0;
    foreach($session->systems as $sys){

        if($sys->id_system == config('SessionAuth.id_system')){
            $x++;

            $session->name_system = $sys->name_system;
            $session->id_system = $sys->id_system;
            $session->port = $sys->port;
            $session->profile = $sys->profile;
            $session->permissions = $sys->permissions;

        }
    }

    unset($session->systems);

    if($x==0)  $session->block = true;

    return $session;
}

/**
 * Verifica se uma ou mais permissões estão na sessão
 *
 * @param [type] $cod
 * @return bool
 */
function SessionAuthHasPermission($cod)
{
    if( !SessionAuthHas() )
      return false;


    if(!is_array($cod)){

      foreach( SessionAuth()->permissions as $permission){
        if($cod == $permission->cod){
          return true;
        }
      }

    }

    else{

      foreach($cod as $cod_permission){

        foreach( SessionAuth()->permissions as $permission){
          if($cod_permission == $permission->cod){
            return true;
          }
        }

      }

    }

    return false;
}

/**
 * Verifica se há sessão
 *
 * @return bool
 */
function SessionAuthHas()
{
    $filePath = SessionAuthPathSession();

    if(file_exists($filePath))
      if( !array_key_exists('block', (array)SessionAuth()) )
        return true;


    return false;

}

/**
 * Retorna a rota para a página de login
 *
 * @return string
 */
function SessionAuthRouteLogin()
{
    return config('SessionAuth.dominio').":81";
}

/**
 * Retorna a rota para a página de logout
 *
 * @return string
 */
function SessionAuthRouteLogout()
{
    return config('SessionAuth.dominio').":81/autenticacao/logout";
}

/**
 * Cria um html com o conteudo do arquivo CHANGELOG.md
 *
 * @return string
 */
function changeLog()
{
  $file = __DIR__ . "/../../../../CHANGELOG.md";

  if(!file_exists($file)){
    return json_encode(['message'=>'CHANGELOG.md não existe!'],JSON_UNESCAPED_UNICODE);
  }

  $content = file_get_contents($file);

  $explodedContents = explode('## ',  $content);

  $html = [];
  foreach($explodedContents as $index=>$explodedContent){
    if($index <> 0){
      $explodedNote = explode(']',$explodedContent);
      $explodedNote[0] .= ']';

      $date = mb_substr($explodedNote[1],0,11);
      $notes = mb_substr($explodedNote[1],11);

      $html[$explodedNote[0]] = ['date'=>$date, 'notes'=>$notes];
    }

  }


  $html = json_encode($html, JSON_UNESCAPED_UNICODE);
  $html = str_replace('\r\n','',$html);

  return $html;
}

/**
 * Escreve no arquivo json da sessão
 *
 * @param string $session_data
 * @return bool
 */
function SessionAuth_write(string $session_data)
{
    $file = SessionAuthPathSession();

    $fileCreated = fopen($file ,'w');
    if($fileCreated == false)
        return false;

    if(fwrite($fileCreated, $session_data) ){
        return true;
    }else{
        return false;
    }
}

/**
 * Adicione uma nova chave na sessão
 *
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function SessionAuthAdd( string $key, mixed $value )
{
    try{

        $session_data = SessionAuth();
        $session_data->$key = $value;

        SessionAuth_write( json_encode($session_data) );

        return true;

    }catch(\ErrorException $e){
        return $e->getMessage();
    }
}

/**
 * Deleta uma chave na sessão
 *
 * @param string $key
 * @return mixed
 */
function SessionAuthDelete(string $key)
{
    try{

        $session_data = SessionAuth();
        unset($session_data->$key);

        SessionAuth_write( json_encode($session_data) );

        return true;

    }catch(\ErrorException $e){
        return $e->getMessage();
    }
}
