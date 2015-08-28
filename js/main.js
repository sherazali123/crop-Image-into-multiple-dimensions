jQuery(function($) {
  var x = 0;
  var y = 0;
  var width;
  var height;
  var original;

  function jcrop_target(Id) {
    return function(c) {
      updateCoords(Id, c);
    };
  }

  function updateCoords(Id, c) {
    Id.closest('form').find('input[name=_x]').val(c.x);
    Id.closest('form').find('input[name=_y]').val(c.y);
    Id.closest('form').find('input[name=_w]').val(c.w);
    Id.closest('form').find('input[name=_h]').val(c.h);
  }
  jQuery('.customImg').each(function() {
    width = jQuery(this).attr('customWidth');
    height = jQuery(this).attr('customHeight');
    original = jQuery(this).attr('original');

    if (original !== 1) {
      jQuery(this).Jcrop({
        setSelect: [0, 0, width, height],
        borderStyle: 'dotted',
        allowResize: false,
        allowSelect: false,
        // addClass: 'jcrop-dark',
        aspectRatio: width / height,
        onChange: jcrop_target($(this)),
        onSelect: jcrop_target($(this))
      });



    }


  });

  function post_form_data(data) {
    jQuery.ajax({
      type: 'POST',
      url: baseUrl + 'save.php',
      data: {images: data},
      beforeSend: function() {
        jQuery("#saveAll").removeClass('btn-primary');
        jQuery("#saveAll").addClass('btn-success');
         jQuery("#saveAll").text('Saving...');
     },
      success: function(res) {
        console.log(res);
        var resp = JSON.parse(res);
        console.log(resp);
        var file = resp.file;
        var width = resp.width;
        var height = resp.height;
        window.location.href = baseUrl + "show.php?file=" + file + '&w=' + width  + '&h=' + height;
      },
      error: function() {
        jQuery("#saveAll").removeClass('btn-success');
        jQuery("#saveAll").addClass('btn-primary');
         jQuery("#saveAll").text('Save all');
      }
    });
  }
  jQuery("#saveAll").on('click', function(e) {
    var customImages = [];
    jQuery('.customizeImagesForms').each(function() {
      if (jQuery(this).find('img').attr('original') != 1) {

        customImages.push(jQuery(this).serializeArray());

      }
    });
    // console.log(customImages);
    post_form_data(customImages);
  });

});
// Simple event handler, called from onChange and onSelect
// event handlers, as per the Jcrop invocation above
function showCoords(c) {}
