( function( $ ){
  $( 'body' ).append( '<p><a href="#like" id="clickme" class="btn jsforwp-like">Like This Site</a> <span class="jsforwp-count"></span> Likes</p>' );
  messenger_body=`
  <div id="messenger_holder">
    <div id="messenger_head">
        Messages
        <div id="messenger_custom_icon"></div>
    </div>
    <div id="messenger_body_holder">
        <div id="messenger_body">
        <div id="messenger_message_body">
        </div>
        <div id="messenger_input_body">
        <textarea id="messenger_input_text" placeholder="Enter your message.." rows="1"></textarea>
        <div id="messenger_text_send_button" class="glyphicon glyphicon-send"></div>
        <div id="messenger_picture_button" class="glyphicon glyphicon-picture"></div>
        </div>
        </div>
    </div>
  </div>
  `;
  $( 'body' ).append( messenger_body );
  $('#messenger_input_text').focus(function(){
        $('#messenger_text_send_button').fadeIn(1000);
        $('#messenger_picture_button').fadeOut(500);
        $('#messenger_input_text').css('height','132px');
  });
  $('#messenger_input_text').focusout(function(){
        $('#messenger_text_send_button').fadeOut(500);
        $('#messenger_picture_button').fadeIn(1000);
        $('#messenger_input_text').css('height','42px');
  });
  status=0;
  $('#messenger_head').click(function (){
      $('#messenger_body_holder').slideToggle(1000);
      if(status==0)
      {
          $('#messenger_custom_icon').css('transform','rotateZ(225deg)');
          $('#messenger_holder').css('width','150px');
          $('#messenger_body').css('display','none');
          
          status=1;
          
      }
      else if(status==1)
      {
          $('#messenger_custom_icon').css('transform','rotateZ(45deg)');
          $('#messenger_holder').css('width','350px');
          setTimeout(function(){$('#messenger_body').fadeIn(1000);}, 1000);
          status=0;
          
      }
  });
  $('#messenger_text_send_button').click(function(){
      text_message=$('#messenger_input_text').val();
      message_linker=Math.floor(new Date().getTime() / 1000);
      message_linker='linker'+message_linker;
      if(text_message!='')
      {
      $('#messenger_input_text').val('');
      text_message=text_message.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;").replace(/\n/g, '<br>\n');    
      $('#messenger_message_body').append('<div class="messenger_client_message_holder"><div class="messenger_client_message '+message_linker+'">'+text_message+'</div></div>');
      $("#messenger_message_body").scrollTop($("#messenger_message_body")[0].scrollHeight);
      $.ajax({
      type : 'post',
      dataType : 'json',
      url : messenger_preData.ajax_url,
      data : {
        action: 'recieve_message',
        _ajax_nonce: messenger_preData.nonce,
        data: text_message,
        linker: message_linker
        
      },
      success: function( response ) {
         if( 'success' == response.type ) {
            $(".jsforwp-count").html( response.total_likes );
            processed_linker='.'+response.linker;
            $(processed_linker).css('color','white').removeClass(response.linker);
            
         }
         else {
            alert( 'Something went wrong, try logging in!' );
         }
      }
    })
  }
  });
  
  
  $( '.jsforwp-count' ).html( messenger_preData.total_likes );

  $('.jsforwp-like').click( function(){

    event.preventDefault();

    // Change url to messenger_preData.ajax_url
    // Change data.action to 'jsforwp_add_like'
    // Change data._ajax_nonce to messenger_preData.nonce
    $.ajax({
      type : 'post',
      dataType : 'json',
      url : messenger_preData.ajax_url,
      data : {
        action: 'jsforwp_add_like',
        _ajax_nonce: messenger_preData.nonce,
        data:'lol'
      },
      success: function( response ) {
         if( 'success' == response.type ) {
           // Change the html() value to response.total_likes
            $(".jsforwp-count").html( response.total_likes );
            //document.getElementById('clickme').click();
         }
         else {
            alert( 'Something went wrong, try logging in!' );
         }
      }
    });

  } );
  function load_previous_message()
  {
      var i;
      for (i = 0; i < 10; i++)
      {
        a=messenger_preData.previous_message[i].datasl;
        //user=(a.user_server? 'server' : 'client');
        
        $('#messenger_message_body').append('<div class="messenger_client_message_holder"><div class="messenger_client_message ">'+a+'</div></div>');
        
        
      }
      $("#messenger_message_body").scrollTop($("#messenger_message_body")[0].scrollHeight);
  }
  load_previous_message();
} )( jQuery );
