<?php

/**
 * Retorna um objeto com os dados da sessão
 *
 * @return object
 */
function SessionAuth()
{
    return session('SessionAuth');
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
  if(!session()->has('SessionAuth')){
      return false;
  }

  return true;
}

/**
 * Retorna a rota para a página de login
 *
 * @return string
 */
function SessionAuthRouteLogin()
{
    return config('SessionAuth.APP_DOMINIO').":181";
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

/**
 * Retorna a ultima versao no CHANGELOG.md
 *
 * @return string
 */
function appVersion()
{ 
  $versions = json_decode(changeLog());

  if(property_exists($versions, 'message')){
    return $versions->message;
  }

  foreach($versions as $version=>$notes){
    $v = $version;
  };
  $v = str_replace('[','',$v);
  $v = str_replace(']','',$v);

  return $v;
}