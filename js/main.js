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
    Id.closest('form').find('input[name=_w]').val(c.width);
    Id.closest('form').find('input[name=_h]').val(c.height);
    Id.closest('form').find('input[name=rotate]').val(c.rotate);
    Id.closest('form').find('input[name=scaleX]').val(c.scaleX);
    Id.closest('form').find('input[name=scaleY]').val(c.scaleY);
  }
  jQuery('.customImg').each(function() {
    width = jQuery(this).attr('customWidth');
    height = jQuery(this).attr('customHeight');
    original = jQuery(this).attr('original');
    if (original != 1) {
      jQuery(this).cropper({
         aspectRatio: width / height,
         maxWidth: width,
         maxHeight: height,
         guides: false,
         background: false,
         dragCrop: false,
         rotatable: false,
         scalable: false,
         cropBoxResizable: false,
         modal: true,
         strict: true,
         highlight: true,
         autoCrop: true,
         cropBoxMovable: true,
         crop: function (e) {
            updateCoords(jQuery(this), e);
          }
      });

      var cropBoxData = jQuery(this).cropper('getData');

      cropBoxData.width = parseInt(width);
      cropBoxData.height = parseInt(height);

      jQuery(this).cropper('setData', cropBoxData);

      cropBoxData = jQuery(this).cropper('getData');

      updateCoords(jQuery(this), cropBoxData);

    }


  });
  function post_form_data(data) {

        jQuery.ajax({
          type: 'POST',
          url: baseUrl + 'save.php',
          dataType:'json',
          data: {images: data},
          beforeSend: function() {
            jQuery("#saveAll").removeClass('btn-primary');
            jQuery("#saveAll").addClass('btn-success');
             jQuery("#saveAll").text('Saving...');
         },
          success: function(resp) {
            console.log(resp);
            var file = resp.file;
            var width = resp.width;
            var height = resp.height;
            window.location.href = baseUrl + "show.php?file=" + file + '&w=' + width  + '&h=' + height;
          },
          error: function(e) {
            console.log('error: ', e);
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
    post_form_data(customImages);

  });

});
// Simple event handler, called from onChange and onSelect
// event handlers, as per the Jcrop invocation above
function showCoords(c) {}
