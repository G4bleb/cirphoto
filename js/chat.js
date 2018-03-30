'use strict';

var websocket;
var login='cir2';

createWebSocket();

function createWebSocket()
{
  websocket = new WebSocket('ws://chen-co.local:12345');
  $('#chat-send').unbind('click').click(sendMessage);
  websocket.onmessage = function(message)
  {
    var textArea;

    textArea = $('#chat-room');
    textArea.val(textArea.val() + message.data + '\n');
    textArea.scrollTop(textArea.prop('scrollHeight'));
  }

}

function sendMessage(event){
  var message;
  event.preventDefault();
  message=$('#chat-message').val();
  websocket.send(login+': '+message);

}
