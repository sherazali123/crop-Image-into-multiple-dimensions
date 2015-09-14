/*
 * Handling all the cropping actions in main.js
 */
var croppFollowingImages = true;
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
    // console.log('C', c);
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
          // console.log("crop", e);
          _dty_updateCoords(jQuery(this), e);


        },
        built: function(e) { // after the crop has been done
          // console.log("built", e);
          // as it's a callback cant access gloabl variables so access current width, height and original in the each(loop)
          _dty_width = jQuery(this).attr('customWidth');
          _dty_height = jQuery(this).attr('customHeight');
          _dty_original = jQuery(this).attr('original');

          // get cropper image data (x,y,w,h)
          var getData = jQuery(this).cropper('getData');
          var getCropBoxData = jQuery(this).cropper('getCropBoxData');
          //  console.log(getData);
          // convert string to integer
          getData.width = parseInt(_dty_width);
          getData.height = parseInt(_dty_height);

          getCropBoxData.width = parseInt(_dty_width);
          getCropBoxData.height = parseInt(_dty_height);

          // starting cropper from top-left corner by setting it to zero
          getData.x = 0;
          getData.y = 0;
          getCropBoxData.left = 0;
          getCropBoxData.top = 0;
          // set new cropped area by new width and height
          jQuery(this).cropper('setData', getData);
          jQuery(this).cropper('setCropBoxData', getCropBoxData);
          //  console.log(getData);
        },
        cropmove: function(e){
          console.log("cropmove", e);
          // as it's a callback cant access gloabl variables so access current width, height and original in the each(loop)
          _dty_width = jQuery(this).attr('customWidth');
          _dty_height = jQuery(this).attr('customHeight');
          _dty_original = jQuery(this).attr('original');

          // if(_dty_width !== _dty_height){
          var _dty_minus = 100,
              _dty_tempHeight = _dty_height,
              _dty_currentImagecanvasData, _dty_imagecanvasData,
              _dty_croppedImage, _dty_croppedImageData, _dty_currentImageCroppedData;

          _dty_currentImageCroppedData = jQuery(this).cropper('getData');
          _dty_currentImagecanvasData = jQuery(this).cropper('getCanvasData')

          for (var i = 0; i < _dty_height / 100; i++) {
            // cropper object
            _dty_croppedImage = jQuery("#croppedImage_" + _dty_width + "_" + _dty_tempHeight);

            // get data
            _dty_croppedImageData = _dty_croppedImage.cropper('getData');

            // get canvas data
            _dty_imagecanvasData = _dty_croppedImage.cropper("getCanvasData");

            // set data
            _dty_croppedImageData.x = _dty_currentImageCroppedData.x;
            _dty_croppedImageData.y = _dty_currentImageCroppedData.y;

            // set canvas data
            _dty_imagecanvasData.left = _dty_currentImagecanvasData.left;
            _dty_imagecanvasData.top = _dty_currentImagecanvasData.top;

            // set data
            _dty_croppedImage.cropper('setData', _dty_croppedImageData);

            // set canvas data
            _dty_croppedImage.cropper('setCanvasData', _dty_imagecanvasData);

            _dty_tempHeight = _dty_tempHeight - _dty_minus;
          }

          // }
        },
        zoom: function(e){
          // console.log("zoom", e);

          // as it's a callback cant access gloabl variables so access current width, height and original in the each(loop)
          _dty_width = jQuery(this).attr('customWidth');
          _dty_height = jQuery(this).attr('customHeight');
          _dty_original = jQuery(this).attr('original');

          var _dty_minus = 100, _dty_ratio = e.ratio,
              _dty_tempHeight = _dty_height,
              _dty_croppedImage, _dty_croppedImageData, _dty_currentImageCroppedData;

          // console.log(_dty_height);
          for (var i = 0; i < _dty_height / 100; i++) {
            _dty_tempHeight = _dty_tempHeight - _dty_minus;
            _dty_height = _dty_tempHeight;
            _dty_croppedImage = jQuery("#croppedImage_" + _dty_width + "_" + _dty_tempHeight);
            _dty_croppedImage.cropper('zoom', _dty_ratio);
          }

        }

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
          var _dty_width = resp._dty_width;
          var _dty_height = resp._dty_height;
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
  // will be started when user will cl3ick on save all button to save cropped images
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
