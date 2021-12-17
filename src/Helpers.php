<?php

function SessionAuthPathSession()
{
    $remote = $_SERVER["REMOTE_ADDR"] ?? '127.0.0.1';
    $id_session = MD5($remote);
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
    foreach($session->systems as $sys){

        if($sys->id_system == config('SessionAuth.id_system')){
            $session->profile = $sys->profile;
            $session->permissions = $sys->permissions;
        }
    }

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
    if( empty(SessionAuthHas()) )
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

    if(file_exists($filePath)){
        return true;
    }

    return false;

}

/**
 * Retorna a rota para a página de login
 *
 * @return string
 */
function SessionAuthRouteLogin()
{
    return config('SessionAuth.dominio').":181";
}


function SessionAuthRouteLogout()
{
    return config('SessionAuth.dominio').":181/autenticacao/logout";
}

/**
 * Cria um html com o conteudo do arquivo CHANGELOG.md
 *
 * @return void
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
