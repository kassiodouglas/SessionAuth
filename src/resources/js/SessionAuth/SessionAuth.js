//Config
const timeToexpires_env = (process.env.MIX_TIME_TO_EXPIRES) ? process.env.MIX_TIME_TO_EXPIRES : 180;
const timeToCheck_env = (process.env.MIX_TIME_TO_CHECK) ? process.env.MIX_TIME_TO_CHECK : 300 ;

global.timeToexpires = 1000 * timeToexpires_env;
global.timeToCheck = 1000 * timeToCheck_env;
global.id_session = `SessionAuth_${process.env.SESSION_ID_SYSTEM}`;


/**
 * Seta os dados da sessão em localstorage
 */
global.setSession = async () =>{

    const url = `${process.env.MIX_APP_DOMINIO}:81/session`;
    const id_session = `SessionAuth_${process.env.SESSION_ID_SYSTEM}`;

    const response = await fetch(url);
    const responseJson = await response.json();

    if(responseJson.id_session){
        localStorage.setItem(id_session, JSON.stringify(responseJson));
        return;
    }
    localStorage.removeItem(id_session);
    return;
}

/**
 * Verifica se há sessão em localstorage
 */
global.hasSession = () => {
    var session = localStorage.getItem(id_session);
    if( session ){
        return true;
    }

    return false;
}

/**
 * Verifica se houve interação na tela
 * @param {*} e
 */
global.hasActivity = async(e)=> {
    clearInterval(timeout);
    timeout = setTimeout(()=>{inactivity()}, timeToCheck);
}

/**
 * Ativado quando a inativiade
 * @returns
 */
global.inactivity = async()  =>{

    var title = document.querySelector('title');
    var titleOriginal = title.innerHTML;

    var sess = await hasSession();
    if(!sess)
        return;

    title.innerHTML = '[Expirando a sessão!] - ' + titleOriginal

    var timelogout = setTimeout(()=>{
        localStorage.removeItem(id_session);
        location.href = 'autenticacao/logout';
    }, timeToexpires);

    var counter = timeToexpires/1000;

    var counterHtml = setInterval(() => {
        var ele = document.querySelector('.counter');
        if(ele !== null)
            if(ele.innerHTML == 0)
                ele.innerHTML = 0;
            else
                ele.innerHTML = parseInt(ele.innerHTML) - 1;
    }, 1000);

    Notiflix.Report.init({
        plainText: false,
        titleMaxLength: 250
    })

    var name = SessionAuth().name.substring(0,16) + '...';

    Notiflix.Report.info(
        `Olá ${name}`,
        `Você ainda está na ativa? <br>Sua sessão irá expirar em <span class="counter">${counter}</span> segundos.`,
        'Sim, estou na ativa!',
        () => {
            title.innerHTML = titleOriginal;
            clearInterval(timelogout);
            clearInterval(counterHtml);
         },
    );




}

/**
 * Retorna os dados da sessão em localstorage
 * @returns
 */
global.SessionAuth = () =>{
    var session = localStorage.getItem(id_session);
    if( session ){
        return JSON.parse(session);
    }

    return false;
}


/*Chama as funções*/
setSession()

global.timeout = setTimeout(()=>{inactivity()}, timeToCheck);

['keyup', 'touchmove' in window ? 'touchmove' : 'mousemove', "onwheel" in document.createElement("div") ? "wheel" : document.onmousewheel !== undefined ? "mousewheel" : "DOMMouseScroll"].forEach(function(ev) {
  window.addEventListener(ev, hasActivity);
});


