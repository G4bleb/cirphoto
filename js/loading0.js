'use strict';
$(document).ready(function() {
  ajaxRequest('GET', 'php/request.php', loadPhotos);
  // createWebSocket();
});

function loadPhotos(ajaxResponse){
  var response = JSON.parse(ajaxResponse);
  console.log('Response length : '+response.length);
  for (var i = 0; i < response.length; i++) {
    $('#thumbnails').append('<div class="col-xs-2 col-md-2"><a href="#" class="thumbnail"><img id="photo-'+response[i]['id']+'"src="'+response[i]['src']+'"></a></div>');

    $('#photo-'+response[i]['id']).unbind('click').click(function(event){
      event.preventDefault();
      console.log(this);
      ajaxRequest('GET', 'php/request.php/photos/' + event.target.id.substring(6), loadPhoto);
      // ajaxRequest('GET', 'php/request.php/photos/', loadPhoto);
      console.log('Did the photo request '+ event.target.id.substring(6));
    });

    console.log('rebound photo '+i);
  }
  // $('#photo').html();
}

function buttonLoad(){
  ajaxRequest('GET', 'php/request.php', loadPhoto);
}

  function loadPhoto(ajaxResponse){
  response = JSON.parse(ajaxResponse);

  $('#photo').html('<div class="panel panel-default"><div class="panel-body"><h2>' + data[0]['title'] + '</h2><div class="row"><div class="col-xs-12 col-md-12"><a href="#" class="thumbnail"><img src="' + data[0]['src'] + '"></a></div></div></div></div>')

  // $('#photo').attr('photoid', data[0].id);
}
