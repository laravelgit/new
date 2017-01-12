$( document ).ready(function() {


    // getElementById
    function $id(id) {
        return document.getElementById(id);
    }

    //
    // output information
    function Output(msg) {
        var m = $id("messages");
        m.innerHTML = msg + m.innerHTML;
    }

    // call initialization file
    if (window.File && window.FileList && window.FileReader) {
        Init();
    }

    //
    // initialize
    function Init() {

        var fileselect = $id("image_file"),
            filedrag = $id("filedrag"),
            submitbutton = $id("submitbutton");

        // // file select
        fileselect.addEventListener("change", FileSelectHandler, false);

        // is XHR2 available?
        var xhr = new XMLHttpRequest();
        if (xhr.upload) {
        
            // file drop
            filedrag.addEventListener("dragover", FileDragHover, false);
            filedrag.addEventListener("dragleave", FileDragHover, false);
            filedrag.addEventListener("drop", FileSelectHandler, false);
            filedrag.style.display = "block";
            
            // remove submit button
            submitbutton.style.display = "none";
        }

    }


    // file drag hover
    function FileDragHover(e) {
        e.stopPropagation();
        e.preventDefault();
        e.target.className = (e.type == "dragover" ? "hover" : "");
    }

    // file selection
    function FileSelectHandler(e) {

        // cancel event and hover styling
        FileDragHover(e);

        // fetch FileList object
        var files = e.target.files || e.dataTransfer.files;

        // process all File objects
        for (var i = 0, f; f = files[i]; i++) {
            //ParseFile(f);
            fileSelectHandler(3, f);
        }

    }

    function ParseFile(file) {

        console.log('12');
        Output(
            "<p>File information: <strong>" + file.name +
            "</strong> type: <strong>" + file.type +
            "</strong> size: <strong>" + file.size +
            "</strong> bytes</p>"
        );
        
    }


    $('.open-popup').magnificPopup({
		type: 'inline',
		preloader: false,
		closeOnBgClick: false,
		focus: '#name',
        maxSize: [480, 1000]
	});


    $('.total-crop').progressBar();

});



$.fn.extend({
    progressBar: function () {
        //data = {total:x, sold:y};
        var a = 100;
        var b = 100;
        var progress = a/b;
        var width = (progress*100);

        //var hueMax = 140;
        //var hue = hueMax*progress;

        var $progressBar = $(this).children().first();
        //$progressBar.css({position: 'relative'});
        //$progressBar.append('<span class="percent">0%</span>');
        //var $percent = $progressBar.find('.percent').css({position: 'absolute', 'right': 0});

        $progressBar.animate({'width': width+'%'},

        {
            duration: 20000
            // progress: function (animation, _progress, remainingMs) {
            //     var color = 'hsl('+(hue*_progress)+', 100%, 50%)';
            //     $progressBar.css('background', color);
            //     var percent = Math.round(width*_progress);
            //     $percent.html(percent+'%')
            // }
        });
    }
});