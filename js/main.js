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
  var _dty_width;
  var _dty_height;
  var _dty_original;

  /*
   * Update dimensions of image when cropper starts/changes
   * @param Id Node unique id
   * @param c dimensions
   * @return void
   */
  function _dty_updateCoords(Id, c) {
    Id.closest('form').find('input[name=_x]').val(c.x);
    Id.closest('form').find('input[name=_y]').val(c.y);
    Id.closest('form').find('input[name=_w]').val(c.width);
    Id.closest('form').find('input[name=_h]').val(c.height);
  }

  /*
   * traverse each image and integrate cropper with the image
   */
  jQuery('.customImg').each(function() {
    _dty_width = jQuery(this).attr('customWidth');
    _dty_height = jQuery(this).attr('customHeight');
    _dty_original = jQuery(this).attr('original');
    // if the image is not original
    if (_dty_original != 1) {
      // destry the cropper if it's already there with the image
      jQuery(this).cropper('destroy');

      // attach the cropper with the image with required options set e.g. zoomable, can not be resized etc etc
      jQuery(this).cropper({
        aspectRatio: _dty_width / _dty_height,
        maxWidth: _dty_width,
        maxHeight: _dty_height,
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
          _dty_updateCoords(jQuery(this), e);
        },
        built: function(e) { // after the crop has been done

          // as it's a callback cant access gloabl variables so access current width, height and original in the each(loop)
          _dty_width = jQuery(this).attr('customWidth');
          _dty_height = jQuery(this).attr('customHeight');
          _dty_original = jQuery(this).attr('original');

          // get cropper image data (x,y,w,h)
          var getData = jQuery(this).cropper('getCropBoxData');
          //  console.log(getData);
          // convert string to integer
          getData.width = parseInt(_dty_width);
          getData.height = parseInt(_dty_height);
          // set new cropped area by new width and height
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
  function _dty_post_form_data(_dty_data) {
    Pace.track(function() {
      jQuery.ajax({
        type: 'POST',
        url: _dty_baseUrl + 'save.php',
        dataType: 'json',
        data: {
          images: _dty_data
        },
        beforeSend: function() {
          jQuery("._dty_saveAll").removeClass('btn-primary');
          jQuery("._dty_saveAll").addClass('btn-success');
          jQuery("._dty_saveAll").text('Saving...');
        },
        success: function(resp) {
          // console.log(resp);
          //  Pace.stop;
          var _dty_file = resp._dty_file;
          var _dty_width = resp.width;
          var _dty_height = resp.height;
          window.location.href = _dty_baseUrl + "show.php?file=" + _dty_file + '&w=' + _dty_width + '&h=' + _dty_height;
        },
        error: function(e) {
          // console.log('error: ', e);
          //  Pace.stop;
          jQuery("._dty_saveAll").removeClass('btn-success');
          jQuery("._dty_saveAll").addClass('btn-primary');
          jQuery("._dty_saveAll").text('Save all');
        }
      });
    });

  }
  // will be started when user will click on save all button to save cropped images
  jQuery("#_dty_saveAll, #_dty_saveAll2").on('click', function(e) {
    var _dty_customImages = [];
    jQuery('._dty_customizeImagesForms').each(function() {
      if (jQuery(this).find('img').attr('original') != 1) {
        // push cropped data in array one by one and searlize it
        _dty_customImages.push(jQuery(this).serializeArray());

      }
    });
    _dty_post_form_data(_dty_customImages);

  });

});
