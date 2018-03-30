/**
 * @Author: Thibault NapolÃ©on <Imothep>
 * @Company: ISEN YncrÃ©a Ouest
 * @Email: thibault.napoleon@isen-ouest.yncrea.fr
 * @Created Date: 23-Jan-2018 - 17:00:53
 * @Last Modified: 24-Jan-2018 - 17:03:23
 */

'use strict';

//------------------------------------------------------------------------------
//--- ajaxRequest --------------------------------------------------------------
//------------------------------------------------------------------------------
// Perform an Ajax request.
// \param type The type of the request (GET, DELETE, POST, PUT).
// \param request The request with the data.
// \param callback The callback to call where the request is successful.
// \param data The data associated with the request.
function ajaxRequest(type, request, callback, data = null) {
  var xhr;

  // Create XML HTTP request.
  xhr = new XMLHttpRequest();
  if (data != null && (type == 'GET' || type == 'POST' || type == 'DELETE')) {
    request += '?' + data;
  }
  console.log('request : '+request)
  xhr.open(type, request, true);
  // console.log("Token dans Ajax.js : "+Cookies.get('token'));
  xhr.setRequestHeader('Authorization', 'Bearer ' + Cookies.get('token'));
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  // Add the onload function.
  xhr.onload = function () {
    console.log(xhr.status);

    switch (xhr.status) {
      case 200:
      case 201:
        console.log(xhr.responseText);
        callback(xhr.responseText);
        break;
      case 401:
      case 403:
        httpErrors(xhr.status);
        authentication();
        break;
      default:
        httpErrors(xhr.status);
    }
  };

  // Send XML HTTP request.
  xhr.send(data);
}

//------------------------------------------------------------------------------
//--- httpErrors ---------------------------------------------------------------
//------------------------------------------------------------------------------
// Display a message corresponding to an Http error code.
// \param errorNumber the error code.
function httpErrors(errorNumber) {
  var text = '<div class="alert alert-danger" role="alert">';
  text += '<span class="glyphicon glyphicon-exclamation-sign"></span>';

  switch (errorNumber) {
    case 400:
      // Bad request.
      text += '<strong> RequÃªte incorrecte</strong>';
      break;
    case 401:
      // Unauthorized.
      text += '<strong> Authentifiez vous</strong>';
      break;
    case 403:
      // Forbidden.
      text += '<strong> AccÃ¨s refusÃ©</strong>';
      break;
    case 404:
      // Ressource not found.
      text += '<strong> Page non trouvÃ©e</strong>';
      break;
    case 500:
      // Internal server error.
      text += '<strong> Erreur interne du serveur</strong>';
      break;
    case 503:
      // Service unavailable.
      text += '<strong> Service indisponible</strong>';
      break;
    default:
      text += '<strong> HTTP erreur ' + errorNumber + '</strong>';
      break;
  }
  text += '</div>';
  $('#errors').html(text);
}
