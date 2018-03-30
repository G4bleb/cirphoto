'use strict';
// Request the photos thumbnails.
$(document).ready(function() {
  $('#chat').hide();
  if (Cookies.get('token') == 'undefined') {
        authentication();
  }else {
      ajaxRequest('GET', 'php/request.php/checkToken', loadPhotos);
      // ajaxRequest('GET', 'php/request.php', loadPhotos);
  }

    // ajaxRequest('GET', 'php/request.php/photos/', loadPhotos);



});
//------------------------------------------------------------------------------
//--- loadPhotos ---------------------------------------------------------------
//------------------------------------------------------------------------------
// Load photos thumbnails.
// \param ajaxResponse The data received via the Ajax request.
function loadPhotos(ajaxResponse)
{
  var data;
  $('#chat').show();
  // Parse JSON response.
  data = JSON.parse(ajaxResponse);

  // Create thumbnails.
  for (var i = 0; i < data.length; i++)
  {
    var element;

    // Create thumbnail.
    element = document.createElement('div');
    $(element).css('margin-top', '20px');
    element.className = 'col-xs-2 col-md-2';
    element.innerHTML = '<a href="#" class="thumbnail"><img src="' +
    data[i].src + '" id="photo-' + data[i].id + '"></a>';
    $('#thumbnails').append(element);

    // Create click callback.
    $('#photo-' + data[i].id).unbind('click').click(
      function (event)
      {
        var id = event.target.id.substr(6);
        event.preventDefault();
        ajaxRequest('GET', 'php/request.php/photos/' + id, loadPhoto);
        ajaxRequest('GET', 'php/request.php/comments/' + id, loadComments);

        // ajaxRequest('GET', 'php/request.php/photos/', loadPhoto, 'id=' + id);
        // ajaxRequest('GET', 'php/request.php/comments/', loadComments, 'id=' + id);
      });
    }
  }

  //------------------------------------------------------------------------------
  //--- loadPhoto ----------------------------------------------------------------
  //------------------------------------------------------------------------------
  // Load a photo.
  // \param ajaxResponse The data received via the Ajax request.
  function loadPhoto(ajaxResponse)
  {
    var data;
    var text;

    // Parse JSON response.
    data = JSON.parse(ajaxResponse);

    // Create photo.
    text = '<div class="panel panel-default"><div class="panel-body">';
    text += '<h2>' + data[0].title + '</h2>';
    text += '<div class="row"><div class="col-xs-12 col-md-12">';
    text += '<a href="#" class="thumbnail"><img src="' + data[0].src + '">';
    text += '</a></div></div></div></div>';
    $('#photo').html(text);
    $('#photo').attr('photoid', data[0].id);
  }

  //------------------------------------------------------------------------------
  //--- loadComments -------------------------------------------------------------
  //------------------------------------------------------------------------------
  // Load the commments.
  // \param ajaxResponse The data received via the Ajax request.
  function loadComments(ajaxResponse)
  {
    var comments;
    var data;
    var text;
    var div;

    // Parse JSON response.
    data = JSON.parse(ajaxResponse);

    // Load comments.
    comments = $('#comments');
    comments.html('');
    for (var i = 0; i < data.length; i++)
    {
      // Display comment.
      div = document.createElement('div');
      text = '<div class="panel panel-default"><div class="panel-body">';
      text += data[i].comment;
      text += '<span id=delete-' + data[i].id + ' class="glyphicon ';
      text += 'glyphicon-trash pull-right"></span>';
      text += '<span id=modify-' + data[i].id + ' class="glyphicon ';
      text += 'glyphicon-pencil pull-right">&nbsp;</span>';
      text += '</div></div>';
      div.innerHTML = text;
      comments.append(div);

      // Add modify callback.
      console.log("Adding modify callback");
      $('#modify-' + data[i].id).unbind('click').click(
        function (event)
        {
          var comment;
          var photoId;
          var id;

          comment = $('#comment').val();
          comment = "hehe yikes";
          photoId = $('#photo').attr('photoid');
          id = event.target.id.substr(7);
          event.preventDefault();
          console.log("comment : "+comment+" id : "+id+' photoId : '+photoId);
          if (comment != '' && id != undefined && photoId != undefined)
          {

            ajaxRequest('PUT', 'php/request.php/comments/' + id, function ()
            {
              ajaxRequest('GET', 'php/request.php/comments/', loadComments,'id=' + photoId);
            }, 'login=' + login + '&comment=' + comment);
          }
        });


        // Add delete callback.
        $('#delete-' + data[i].id).unbind('click').click(
          function (event)
          {
            var photoId;
            var id;

            photoId = $('#photo').attr('photoid');
            id = event.target.id.substr(7);
            event.preventDefault();
            if (id != undefined && photoId != undefined)
            {
              ajaxRequest('DELETE', 'php/request.php/comments/' + id, function ()
              {
                ajaxRequest('GET', 'php/request.php/comments/', loadComments,
                'id=' + photoId);
              });
            }
          });
        }



        // Add send callback
        $("#comments-add").show();
        $('#add').unbind('click').click(
          function (event)
          {
            var comment;
            var photoId;

            comment = $('#comment-add').val();
            photoId = $('#photo').attr('photoid');
            event.preventDefault();
            if (comment != '' && photoId != undefined)
            {
              ajaxRequest('POST', 'php/request.php/comments/', function ()
              {
                ajaxRequest('GET', 'php/request.php/comments/', loadComments,
                'id=' + photoId);
              }, 'id=' + photoId + '&comment=' + comment);
            }
          });
        }



        function httpErrors(errorNumber){
          $("#errors").show();
          $("#errors").html(errorNumber);
        }
