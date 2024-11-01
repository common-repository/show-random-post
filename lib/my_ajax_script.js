jQuery(document).ready( function() {
      function call_ajax(id) {
         var interval = []; 
         var actualid = jQuery('#showrandompost_widget-'+id+' .display-post').attr('data-postid');
         var cats = jQuery('#showrandompost_widget-'+id+' .display-post').attr('data-catg');
         timeexec = jQuery('#showrandompost_widget-'+id+' .display-post').attr('data-timeexec');
         interval[id] = (parseInt(timeexec) * 1000); 
        // console.log(interval[id]);
        jQuery.ajax({
            type: 'post',
            dataType : "json",
            context: this,
            url: myAjax.ajaxurl,
            data: {action: "my_ajax_post", actualid: actualid, cat: cats, widgetid: id},
         success: function(response) {
            if(response.type == "success") {
               jQuery("#showrandompost_widget-"+id+" .display-post img").attr("src", response.thumbnail);
               jQuery("#showrandompost_widget-"+id+" .display-post a").attr("href", response.permalink);
               jQuery('#showrandompost_widget-'+id+' .display-post').attr('data-postid', response.postid);
               jQuery("#showrandompost_widget-"+id+" .widget-post-title").html(response.titulo);
               jQuery("#showrandompost_widget-"+id+" .widget-content").html(response.contenido);
              // console.log(response.postid);
            }
            else {
               alert('error');
            }
         }
        });
        setTimeout(function(){
            call_ajax(id);
        },interval[id]);
      };
      jQuery('.display-post').each( function() {
          id = jQuery(this).attr('data-widgetid');
          call_ajax(id);
         // console.log(id);
     });
})
