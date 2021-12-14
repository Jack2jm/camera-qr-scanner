<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Corona Strip</title>
  <link rel="stylesheet" href="{{ asset('assets/vendor/boilerplate/qr/qrcode-reader.min.css?env=dev') }}">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha512-SfTiTlX6kk+qitfevl/7LibUOeJWlt9rbyDn92a1DqWOw9vWG2MFoays0sgObmWazO5BQPiFucnnEAjpAB+/Sw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    body {
      font-family: 'Lato', sans-serif;
    }
    #qrr-button-container{margin: 0 auto; position: absolute; margin-left: -130px; left: 50%; width: 100%; bottom: 20px;display: flex;}
    .btn-retry{
      margin-left: 10px;
    }
    @media(max-width: 786px){
      #qrr-button-container{margin-left: -155px;}
    }
  </style>
</head>

<body>
  <div class="container text-center mt-5">
    <div>
      <h1>Corona Strip</h1>
      <form>
        <button class="qrcode-reader btn-primary btn-opn-camera-scanner btn mt-3" type="button" id="openreader-multi" 
          data-qrr-multiple="true" 
          data-qrr-repeat-timeout="0"
          data-qrr-line-color="#00FF00"
          data-qrr-targ et="#multiple"><i class="fa fa-qrcode" style="margin-right:10px"></i>Open Strip Scanner</button>
      </form>
      <div class="result_box text-center mt-4">
          <div class="loader_box"  style="display:none">
            <i class="fa fa-spin fa-spinner fa-2x"></i>
            <h6 class="mt-3">Please wait while we are fetching the result...</h6>
          </div>
          <div class="result_show_box mt-5" style="display:none">
            <h1>Result:</h1>
            <h6 class="mt-3"></h6>
          </div>
      </div>
      <img src="" id="downloadImage" style="display: none;max-height: 300px;">
    </div>
  </div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="{{ asset('assets/vendor/boilerplate/qr/qrcode-reader.min.js') }}"></script>

<script>
  
  $(function(){

    // overriding path of JS script and audio 
    $.qrCodeReader.jsQRpath = "{{ asset('assets/vendor/boilerplate/qr/jsQR.min.js') }}";
    $.qrCodeReader.beepPath = "{{ asset('assets/vendor/boilerplate/qr/dist_audio_beep.mp3') }}";

    // bind all elements of a given class
    $(".qrcode-reader").qrCodeReader();

    // bind elements by ID with specific options
    $("#openreader-multi2").qrCodeReader({multiple: true, target: "#multiple2", skipDuplicates: false});
    $("#openreader-multi3").qrCodeReader({multiple: true, target: "#multiple3"});

    // read or follow qrcode depending on the content of the target input
    $("#openreader-single2").qrCodeReader({callback: function(code) {
      if (code) {
        window.location.href = code;
      }  
    }}).off("click.qrCodeReader").on("click", function(){
      var qrcode = $("#single2").val().trim();
      if (qrcode) {
        window.location.href = qrcode;
      } else {
        $.qrCodeReader.instance.open.call(this);
      }
    });

    //$('#qrr-canvas').click()


  });
  
    $(document).on("click",".btn-opn-camera-scanner",function(e) {
      $('.loader_box').css('display', 'none');;
      $('.result_show_box').css('display', 'none');;
      $('#qrr-button-container').css('display', 'none');;
  });

  $(document).on("click",".btn-retry",function(e) {
      e.preventDefault();
      console.log('asdasd');
      $('#qrr-canvas').click();
      $('#qrr-button-container').addClass('d-none');
  });


  $(document).on("click",".btn-upload",function(e) {
      e.preventDefault();
      $('#qrr-close').click();
      console.log('jatni');
      $('.loader_box').css('display', 'block');;
      $('.result_show_box').css('display', 'none');;
      canvas_ = document.getElementById("qrr-canvas");
      var ctx = canvas_.getContext("2d");

      frame__ = document.getElementById("downloadImage");
      frame__.src  = canvas_.toDataURL('image/webp');;

      //$('#downloadImage').css('display', 'block');
          var ImageURL = $("#downloadImage").attr("src");
          // Split the base64 string in data and contentType
          var block = ImageURL.split(";");
          // Get the content type of the image
          var contentType = block[0].split(":")[1];// In this case "image/gif"
          // get the real base64 content of the file
          var realData = block[1].split(",")[1];// In this case "R0lGODlhPQBEAPeoAJosM...."

          // Convert it to a blob to upload
          var blob = b64toBlob(realData, contentType);

          // Create a FormData and append the file with "image" as parameter name
          var formDataToUpload = new FormData();
          formDataToUpload.append("_token", "{{ csrf_token() }}");
          formDataToUpload.append("image", blob);

          $.ajax({
              url:"{{ route('corona_image_upload') }}",
              data: formDataToUpload,
              type:"POST",
              contentType:false,
              processData:false,
              cache:false,
              dataType:"json",
              error:function(err){
                  console.error(err);
                  $('.result_show_box h4').html(err);
                  $('.loader_box').css('display', 'none');;
                  $('.result_show_box').css('display', 'block');;
              },
              success:function(data){
                console.log(data);
                result_message = (data.data.stripResult == 'Blur') ? 'Image is not clearly visible or strip lines are missing.' : data.data.stripResult;
                $('.result_show_box h6').html(result_message);
                $('.loader_box').css('display', 'none');;
                $('.result_show_box').css('display', 'block');;
                  /*{"success":true,"data":{"stripResult":"Blur","refNumber":2,"filePath":"https:\/\/coronatest.cf\/storage\/sample_1639121086xY_blob"},"message":"Result is: Blur"}*/
              },
              complete:function(){
                  console.log("Request finished.");
              }
          });
        });
    function b64toBlob(b64Data, contentType, sliceSize) {
            contentType = contentType || '';
            sliceSize = sliceSize || 512;

            var byteCharacters = atob(b64Data);
            var byteArrays = [];

            for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
                var slice = byteCharacters.slice(offset, offset + sliceSize);

                var byteNumbers = new Array(slice.length);
                for (var i = 0; i < slice.length; i++) {
                    byteNumbers[i] = slice.charCodeAt(i);
                }

                var byteArray = new Uint8Array(byteNumbers);

                byteArrays.push(byteArray);
            }

          var blob = new Blob(byteArrays, {type: contentType});
          return blob;
        }
</script>


</body>
</html>