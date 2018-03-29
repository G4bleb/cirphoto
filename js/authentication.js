'use strict';

function authentication()
{
  $('#authentication-send').off('click').click(validateLogin);
  $("#authentication").show();
}

function validateLogin(event)
{
  var login;
  var password;
  var text;
  var xhr;

  event.preventDefault();

  login = $('#login').val();
  password = $('#password').val();
  $('#errors').html('');
  if (login == '' || password == '')
  {
    $('#errors').html('<div id="errors" class="alert alert-danger" role="alert">'+
    '<span class="glyphicon glyphicon-exclamation-sign"aria-hidden="true"></span>'+
    '<strong> L\'un des champs est vide.</strong></div>');
  }
  else
  {
    Cookies.set('login', login);

    xhr = new XMLHttpRequest();
    xhr.open('GET', 'php/request.php/authenticate', true);
    xhr.setRequestHeader('Authorization', 'Basic ' + btoa(login + ':' +
      password));

    xhr.onload = function ()
    {
      switch (xhr.status)
      {
        case 200:
          Cookies.set('token', xhr.responseText);
          $("#authentication").hide();
          $('#infos').html('Authentification OK');
          $('#chat').show()
          break;
        default:
          httpErrors(xhr.status);
      }
    };

    xhr.send();
  }
}
