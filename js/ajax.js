/**
 * @Author: Thibault Napoléon <Imothep>
 * @Company: ISEN Yncréa Ouest
 * @Email: thibault.napoleon@isen-ouest.yncrea.fr
 * @Created Date: 23-Jan-2018 - 17:00:53
 * @Last Modified: 05-Feb-2018 - 23:14:39
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
function ajaxRequest(type, request, callback, data = null)
{
  var xhr;

  // Create XML HTTP request.
  xhr = new XMLHttpRequest();
  if (type == 'GET' && data != null)
    request += '?' + data;
  xhr.open(type, request, true);
  xhr.setRequestHeader('Authorization', 'Bearer ' + Cookies.get('token'));
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  // Add the onload function.
  xhr.onload = function ()
  {
    switch (xhr.status)
    {
      case 200:
      case 201:
        console.log(xhr.responseText);
        callback(xhr.responseText);
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
function httpErrors(errorNumber)
{
  var text;
  text = '<div class="alert alert-danger" role="alert">';
  text += '<span class="glyphicon glyphicon-exclamation-sign"></span>';
  switch (errorNumber)
  {
    case 400:
      // Bad request.
      text += '<strong> Requête incorrecte</strong>';
      break;
    case 401:
      // Unauthorized.
      text += '<strong> Authentifiez vous</strong>';
      break;
    case 403:
      // Forbidden.
      text += '<strong> Accès refusé</strong>';
      break;
    case 404:
      // Ressource not found.
      text += '<strong> Page non trouvée</strong>';
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
