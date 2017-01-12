<?php
// edit
/**
 * Jcrop image cropping plugin for jQuery
 * Example cropping script
 * @copyright 2008-2009 Kelly Hallman
 * More info: http://deepliquid.com/content/Jcrop_Implementation_Theory.html
 */
//var_dump($_SERVER["DOCUMENT_ROOT"]);
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$targ_w = $targ_h = 150;
	$jpeg_quality = 90;

	$src = $_POST['src'];
	$img_r = imagecreatefromjpeg($src);
	$dst_r = ImageCreateTrueColor( $_POST['w'],$_POST['h'] );

	imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],$_POST['w'],$_POST['h'],$_POST['w'],$_POST['h']);

	header('Content-type: image/jpeg');

	//imagejpeg($dst_r,null,$jpeg_quality);
    $url = '/2/img.jpg';
	imagejpeg($dst_r,$_SERVER["DOCUMENT_ROOT"].$url,$jpeg_quality);
    header("location: $url");
	exit;
}

// If not a POST request, display page below:

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="tapmodo-Jcrop-1902fbc/css/jquery.Jcrop.min.css">
    <link rel="stylesheet" href="Magnific-Popup-master/magnific-popup.css">
</head>
<body>




<div class="hidden">
    <div class="crop-popup" id="crop-popup">
        <form id="upload" enctype="multipart/form-data" method="post" action="upload.php" onsubmit="return checkForm()">
            <input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="300000" />
            <div class="input-otherfile-wr">
                <div class="input-otherfile-wr-wr">
                    <input type="file" name="image_file" id="image_file-1" onchange="fileSelectHandler(2)" >
                    <button class="top-button top-button-light">Select another</button>
                </div>
            </div>
            <div class="top-crop">
                <span>Cover image</span>
            </div>
            <button class="top-button top-button-black">Save</button>
            <div class="step-1" id="filedrag">
                <h3>Drop a cover image here</h3>
                <span>or</span>
                <div class="input-file-wr">
                    <button>Select an image from your computer</button>
                    <input type="file" name="image_file" id="image_file" onchange="fileSelectHandler(1)" >
                    <div id="submitbutton">
                        <button type="submit">Upload Files</button>
                    </div>

                </div>
            </div>
            <div class="step-2">

                <img id="preview">
                <div id="messages">
                    <p>Status Messages</p>
                </div>
            </div>
            <div class="step-3">

                <h5>Loading</h5>
                <div class="total-crop">
                    <div class="sold-crop"></div>
                </div>

            </div>
        </form>
        <form action="index.php" method="post" onsubmit="return checkCoords();">
            <input type="hidden" id="x" name="x" />
            <input type="hidden" id="y" name="y" />
            <input type="hidden" id="w" name="w" />
            <input type="hidden" id="h" name="h" />
            <input type="hidden" id="src" name="src" value=""/>
            <input type="submit" value="Crop Image" class="btn btn-large btn-inverse" />
        </form>
    </div>
</div>


<a href="#crop-popup" class="open-popup">Open Jcrop</div>


    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>

    <script>

        function updateCoords(c){
            $('#x').val(c.x);
            $('#y').val(c.y);
            $('#w').val(c.w);
            $('#h').val(c.h);
        };

        function checkCoords(){
            if (parseInt($('#w').val())) return true;
            alert('Please select a crop region then press submit.');
            return false;
        };

        // convert bytes into friendly format
        function bytesToSize(bytes) {
            var sizes = ['Bytes', 'KB', 'MB'];
            if (bytes == 0) return 'n/a';
            var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
            return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
        };

        // check for selected crop region
        function checkForm() {
            if (parseInt($('#w').val())) return true;
            $('.error').html('Please select a crop region and then press Upload').show();
            return false;
        };

        // Create variables (in this scope) to hold the Jcrop API and image size
        var jcrop_api, boundx, boundy;

        function fileSelectHandler(p, file) {



            // get selected file
            if(p == 1) {
                var oFile = $('#image_file')[0].files[0];
            } else if(p == 3) {
                var oFile = file;
            }else {
                var oFile = $('#image_file-1')[0].files[0];
            }


            //

            // hide all errors
            $('.error').hide();

            //check for image type (jpg and png are allowed)
            var rFilter = /^(image\/jpeg|image\/png)$/i;
            if (! rFilter.test(oFile.type)) {
                $('.error').html('Please select a valid image file (jpg and png are allowed)').show();
                return;
            }

            // check for file size
            if (oFile.size > 11250 * 111024) {
                $('.error').html('You have selected too big file, please select a one smaller image file').show();
                return;
            }

            // preview element
            var oImage = document.getElementById('preview');




            // prepare HTML5 FileReader
            var oReader = new FileReader();
            oReader.onload = function(e) {

                // e.target.result contains the DataURL which we can use as a source of the image
                oImage.src = e.target.result;
                oImage.onload = function () { // onload event handler

                    // display step 2


                    $('.top-button-black').show();
                    $('.input-otherfile-wr').show();
                    //$('.step2').fadeIn(500);
                    //$('.step-1').hide();
                    // display some basic image info
                    var sResultFileSize = bytesToSize(oFile.size);
                    $('#filesize').val(sResultFileSize);
                    $('#filetype').val(oFile.type);
                    $('#filedim').val(oImage.naturalWidth + ' x ' + oImage.naturalHeight);

                    // destroy Jcrop if it is existed
                    if (typeof jcrop_api != 'undefined') {
                        jcrop_api.destroy();
                        jcrop_api = null;
                        $('#preview').width(oImage.naturalWidth);
                        $('#preview').height(oImage.naturalHeight);
                    }


                    $('#filedrag').hide();
                    $('.step-2').show();
                    $('.step-3').show();

                    setTimeout(function(){
                        // initialize Jcrop
                        $('#preview').Jcrop({
                            aspectRatio: 1.5,
                            onSelect: updateCoords,
                            setSelect: [ 0, 72,
                                480,
                                ($('#preview').height() / 2) + 70 ],
                            bgColor: 'white',
                            bgOpacity: 0.2
                        }, function(){
                            // use the Jcrop API to get the real image size
                            var bounds = this.getBounds();
                            boundx = bounds[0];
                            boundy = bounds[1];

                            // Store the Jcrop API in the jcrop_api variable
                            jcrop_api = this;

                            $('.step-3').hide();
                            console.log('work');
                            $('#src').val($('#preview').attr('src'));

                        });
                    },3000);


                };

            };

            // read selected file as DataURL
            oReader.readAsDataURL(oFile);

        }
        // if($('.jcrop-tracker').length > 0){
        //     console.log(1);
        //     $('.step-3').hide();
        //     $('.step-2').show();
        // }
    </script>


    <script src="tapmodo-Jcrop-1902fbc/js/jquery.Jcrop.min.js"></script>
    <script src="Magnific-Popup-master/jquery.magnific-popup.min.js"></script>
    <script src="common.js"></script>
</body>
</html>