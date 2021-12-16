/**
 * SessionAuth-js
 */

const debug = false;

global.Notiflix = require('notiflix');
Notiflix.Loading.init({
    messageMaxLength: 150,
})


/**
 * Formata a data para americano
 * @returns
 */
global.dateformat = (dt = '')=>{
    var date = new Date(dt);

    var datestr = ("0" + date.getDate()).slice(-2)  + "-" + ("0"+(date.getMonth()+1)).slice(-2) + "-" + date.getFullYear();
    var hourstr = ("0" + date.getHours()).slice(-2) + ":" + ("0" + date.getMinutes()).slice(-2) + ":" + ("0" + date.getSeconds()).slice(-2);

    var datestring = datestr +' '+hourstr;
    return datestring;
}

/**
 * Gera logs no console
 * @param {*} x
 */
global.log = (x) =>{
    if(debug)
        console.log(dateformat(Date()) +'\n '+ x);
}

/**
 * Pega o token CSRF na tag meta
 * @returns
 */
global.getCSRF = () => {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

/**
 * Loga na api e retorna o token
 * @returns
 */
global.getTokenApi = async function(){

    log('Logando na api')
    const response = await fetch(`${process.env.MIX_APP_DOMINIO}:90/api/login`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `login=${process.env.MIX_API_LOGIN}&password=${process.env.MIX_API_PASSWORD}`

    });

    const responseJson = await response.json();

    log('Logado na api')
    return responseJson.token;
}

/**
 * Verifica se o token no browser é válido senao o recria
 * @returns
 */
global.verifyTokenApi = async ()  =>{

    var token = localStorage.getItem('token_session');

    log('Verificando se token existe em localstorage')
    if(token == null){
        token = await getTokenApi();
        localStorage.setItem('token_session', token)
    }

    return token;
}

/**
 * Busca a sessão na api e o cria em backend
 * @returns
 */
global.getSessionData = async () =>{

    Notiflix.Loading.dots('Verificando dados da sessão..');

    const token = await verifyTokenApi();

    log('Pegando id_session');
    Notiflix.Loading.change('Pegando id da sessão');
    const responseIdsession = await fetch(`${process.env.MIX_APP_URL}/id_session`);
    const jsonIdsession = await responseIdsession.json();

    if(jsonIdsession.msg){
        console.log('msg: ' + jsonIdsession.msg);
        return;
    }
    if(jsonIdsession.error){
        console.log('error: ' + jsonIdsession.error);
        return;
    }

    log('Acessando api com o id_session');
    Notiflix.Loading.change('Acessando api');
    const response = await fetch(`${process.env.MIX_APP_DOMINIO}:90/api/auth/SessionAuth/get/session_${jsonIdsession.id_system}/${jsonIdsession.id_session}`,{
        headers:{
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/x-www-form-urlencoded, application/json',
        },
    });
    const jsonResponse = await response.json();

    var respStatus = jsonResponse.status;

    if(respStatus == "Token is Expired" || respStatus == "Token is Invalid"){
        log(respStatus + ':Recuperando token');
        Notiflix.Loading.change('Recuperando token');
        localStorage.removeItem('token_session')
        var resp = await getSessionData();


    }else if(respStatus == "session not found"){
        log(respStatus + ':Sessão não foi criada na api');
        Notiflix.Loading.change('Sessão não encontrada. Redirecionando...');
        location.href = `${process.env.MIX_APP_DOMINIO}:181/`;


    }else{
        log(respStatus + ':Setando a sessao em backend');
        Notiflix.Loading.change('Criando sessão em backend');
        const setSession = await fetch(`${process.env.MIX_APP_URL}/set_session`,{
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': getCSRF()
            },
            method: 'POST',
            body: JSON.stringify(jsonResponse)
        });

        location.href = 'admin/';
    }

}

/**
 * Deleta a sessão na api e no backend
 * @returns
 */
global.deleteSessionData = async () =>{

    Notiflix.Loading.dots('Deletando dados da sessão..');

    log('Gerando token')
    Notiflix.Loading.change('Gerando token da api');
    const token = await verifyTokenApi();

    log('Resgatando id_session')
    Notiflix.Loading.change('Resgatando id da sessão');
    const responseIdsession = await fetch(`${process.env.MIX_APP_URL}/id_session`);
    const jsonIdsession = await responseIdsession.json();

    if(jsonIdsession.msg){
        console.log(jsonIdsession.msg);
        location.href = 'admin/';
        return;
    }

    log('Deletando sessao na api')
    Notiflix.Loading.change('Deletando dados da sessão');
    const response = await fetch(`${process.env.MIX_APP_DOMINIO}:90/api/auth/SessionAuth/delete/session_${jsonIdsession.id_system}/${jsonIdsession.id_session}`,{
        headers:{
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/x-www-form-urlencoded, application/json',
        },
        method: 'DELETE',
    });
    const jsonResponse = await response.json();

    if(jsonResponse.status == "Token is Expired" || jsonResponse.status == "Token is Invalid"){
        log(jsonResponse.status);
        log('Recuperando token');
        Notiflix.Loading.change('Recuperando token');
        localStorage.removeItem('token_session')
        await deleteSessionData();
    }
    else if(jsonResponse.status == "session deleted" || jsonResponse.status == "session not found"){
        log(jsonResponse.status);
        Notiflix.Loading.change('Redirecionando..');
        location.href = `${process.env.MIX_APP_DOMINIO}:181/`;
    }

}



//Chamada das funções de acordo com o url
if(location.href == `${process.env.MIX_APP_URL}/`){
    getSessionData();
}


if(location.href == `${process.env.MIX_APP_URL}/logout`){
    deleteSessionData();
}
