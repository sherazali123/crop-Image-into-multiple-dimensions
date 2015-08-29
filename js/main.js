/*
 * Handling all the cropping actions in main.js
 */

// when the whole document is ready
jQuery(document).ready(function() {
  jQuery(document).ajaxStart(function() {
    Pace.restart();
  });
  var x = 0;
  var y = 0;
  var width;
  var height;
  var original;

  /*
   * Update dimensions of image when cropper starts/changes
   * @param Id Node unique id
   * @param c dimensions
   * @return void
   */
  function updateCoords(Id, c) {
    Id.closest('form').find('input[name=_x]').val(c.x);
    Id.closest('form').find('input[name=_y]').val(c.y);
    Id.closest('form').find('input[name=_w]').val(c.width);
    Id.closest('form').find('input[name=_h]').val(c.height);
  }

  /*
   * traverse each image and integrate cropper with the image
   */
  jQuery('.customImg').each(function() {
    width = jQuery(this).attr('customWidth');
    height = jQuery(this).attr('customHeight');
    original = jQuery(this).attr('original');
    if (original != 1) {

      jQuery(this).cropper('destroy');

      jQuery(this).cropper({
        aspectRatio: width / height,
        maxWidth: width,
        maxHeight: height,
        guides: false,
        background: false,
        dragCrop: false,
        rotatable: false,
        scalable: false,
        modal: false,
        cropBoxResizable: false,
        zoomable: true,
        guides: true,
        responsive: true,
        strict: true,
        highlight: true,
        autoCrop: true,
        cropBoxMovable: true,
        crop: function(e) { // on i think every event drag/ preview change etc
          updateCoords(jQuery(this), e);
        },
        built: function(e) { // after the crop has been done

          width = jQuery(this).attr('customWidth');
          height = jQuery(this).attr('customHeight');
          original = jQuery(this).attr('original');


          var getData = jQuery(this).cropper('getCropBoxData');
          //  console.log(getData);
          getData.width = parseInt(width);
          getData.height = parseInt(height);

          jQuery(this).cropper('setCropBoxData', getData);

          getData = jQuery(this).cropper('getCropBoxData');

          //  console.log(getData);
        },

      });



    }


  });
  /*
   * POST data to save.php and save cropped images
   * @param data cropped content
   * @return void
   */
  function post_form_data(data) {
    Pace.track(function() {
      jQuery.ajax({
        type: 'POST',
        url: baseUrl + 'save.php',
        dataType: 'json',
        data: {
          images: data
        },
        beforeSend: function() {
          jQuery(".saveAll").removeClass('btn-primary');
          jQuery(".saveAll").addClass('btn-success');
          jQuery(".saveAll").text('Saving...');
        },
        success: function(resp) {
          // console.log(resp);
          //  Pace.stop;
          var file = resp.file;
          var width = resp.width;
          var height = resp.height;
          window.location.href = baseUrl + "show.php?file=" + file + '&w=' + width + '&h=' + height;
        },
        error: function(e) {
          // console.log('error: ', e);
          //  Pace.stop;
          jQuery(".saveAll").removeClass('btn-success');
          jQuery(".saveAll").addClass('btn-primary');
          jQuery(".saveAll").text('Save all');
        }
      });
    });

  }
  // will be started when user will click on save all button to save cropped images
  jQuery("#saveAll, #saveAll2").on('click', function(e) {
    var customImages = [];
    jQuery('.customizeImagesForms').each(function() {
      if (jQuery(this).find('img').attr('original') != 1) {

        customImages.push(jQuery(this).serializeArray());

      }
    });
    post_form_data(customImages);

  });

});
