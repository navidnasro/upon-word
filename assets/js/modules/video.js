/**
 * Video player functionality
 */

$(document).ready(function (){

    let lastVolume = 1;
    var fullscreen = false;

    $('.video').each(function (){

        $(this).on('loadedmetadata', function() {
            //set video properties
            var duration = $(this).siblings('.controls').find('.total-time');
            var current = $(this).siblings('.controls').find('.current-time');
            var video = $(this).closest('.controls').siblings('.video').get(0);

            current.text(timeFormat(0));
            duration.text(timeFormat(video.duration));

        });

    });

    $('.play-pause').each(function (){

        $(this).on('click',function (){

            $(this).children('svg').each(function (){

                if ($(this).hasClass('hidden'))
                    $(this).removeClass('hidden');

                else
                    $(this).addClass('hidden');
            });

            var video = $(this).closest('.controls').siblings('.video').get(0);

            if (video.paused)
                video.play();

            else
                video.pause();

            $(this).toggleClass('paused');

        });

    });

    $('.video').each(function (){

        $(this).on('click',function (){

            var video = $(this).get(0);
            var playPause = $(this).siblings('.controls').find('.play-pause');

            if (video.paused)
                video.play();

            else
                video.pause();

            playPause.toggleClass('paused');

        });

    });

    $('.video').each(function (){

        $(this).on('play',function (){

            var playPause = $(this).siblings('.controls').find('.play-pause');
            var duration = $(this).siblings('.controls').find('.total-time');
            var video = $(this).get(0);

            playPause.addClass('playing');
            duration.text(timeFormat(video.duration));

        });

    });

    $('.video').each(function (){

        $(this).on('pause',function (){

            var playPause = $(this).siblings('.controls').find('.play-pause');

            playPause.removeClass('playing');

        });

    });

    $('.video').each(function (){

        $(this).on('ended',function (){

            var playPause = $(this).siblings('.controls').find('.play-pause');

            playPause.removeClass('playing');

        });

    });

    $('.video').each(function (){

        $(this).on('timeupdate',function (){

            var video = $(this).get(0);
            var progressBar = $(this).siblings('.controls').find('.progress-bar').children('div');
            var current = $(this).siblings('.controls').find('.current-time');
            var minutes = Math.floor((video.currentTime % 3600)/60);
            var seconds = Math.floor(video.currentTime % 60);
            seconds = seconds > 9 ? seconds :`0${seconds}`;

            progressBar.css('width',video.currentTime/video.duration*100+'%');

            current.text(minutes+':'+seconds);

        });

    });

    $('.video').each(function (){

        $(this).on('canplay',function (){

            var video = $(this).get(0);
            var progressBar = $(this).siblings('.controls').find('.progress-bar').children('div');
            var current = $(this).siblings('.controls').find('.current-time');
            var duration = $(this).siblings('.controls').find('.total-time');
            var minutes = Math.floor((video.currentTime % 3600)/60);
            var seconds = Math.floor(video.currentTime % 60);
            seconds = seconds > 9 ? seconds :`0${seconds}`;

            progressBar.css('width',video.currentTime/video.duration*100+'%');

            current.text(minutes+':'+seconds);
            duration.text(timeFormat(video.duration));

        });

    });

    //
    $('.volume-slider').each(function (){

        // Get the video element
        var video = $(this).closest('.controls').siblings('.video').get(0);

        // Initialize volume variables
        var volume = 1; // Full volume
        var isDragging = false;

        // Volume slider functionality
        $('.volume-slider').on('mousedown', function(e) {
            isDragging = true;
            adjustVolume(e.clientX);

            $(document).on('mousemove', function(e) {
                if (isDragging) {
                    adjustVolume(e.clientX);
                }
            });

            $(document).on('mouseup', function() {
                isDragging = false;
                $(document).off('mousemove');
                $(document).off('mouseup');
            });
        });

        function adjustVolume(clientX) {
            var slider = $('.volume-slider');
            var sliderWidth = slider.width();
            var newVolume = (clientX - slider.offset().left) / sliderWidth;
            newVolume = Math.max(0, Math.min(1, newVolume)); // Ensure volume is between 0 and 1

            video.volume = newVolume;
            volume = newVolume;

            updateVolumeBallPosition(newVolume, sliderWidth);
        }

        function updateVolumeBallPosition(newVolume, sliderWidth) {
            var ball = $('.volume-ball');
            var ballPosition = newVolume * sliderWidth;
            ball.css('left', ballPosition + 'px');
        }

    });

    //progress bar moving click
    $('.progress-bar').each(function (){

        $(this).on('click',function (e){

            var video = $(this).closest('.controls').siblings('.video').get(0);
            var progressSlider = $(this).get(0);
            const newTime = e.offsetX/progressSlider.offsetWidth;

            $(this).css('width',newTime*100+'%');
            video.currentTime = newTime*video.duration;

        });

    });

    //fullscreen the video player
    $('.fullscreen').each(function (){

        $(this).on('click',function (){

            if (fullscreen) {
                if(document.exitFullscreen)
                    document.exitFullscreen();
                else if(document.mozCancelFullScreen)
                    document.mozCancelFullScreen();
                else if(document.webkitExitFullscreen)
                    document.webkitExitFullscreen();
            } else {
                var element = $(this).closest('.video-wrapper')[0]; // Get the DOM element

                if (element.requestFullscreen)
                    element.requestFullscreen();
                else if (element.mozRequestFullScreen)
                    element.mozRequestFullScreen();
                else if (element.webkitRequestFullscreen)
                    element.webkitRequestFullscreen();
                else if (element.msRequestFullscreen)
                    element.msRequestFullscreen();
            }

            fullscreen = !fullscreen;
        });

    });

    //window click to play/pause video
    // $(window).on('keydown',function (e){
    //
    //     var video = $('.video').get(0);
    //
    //     switch (e.key)
    //     {
    //         case " ":
    //             if (video.paused)
    //                 video.play();
    //             else
    //                 video.pause();
    //
    //             break;
    //         case "ArrowRight":
    //             video.currentTime += 5;
    //
    //             break;
    //         case "ArrowLeft":
    //             video.currentTime -= 5;
    //
    //             break;
    //         default:
    //             return;
    //     }
    //
    // });

    //forward 5 seconds
    $('.forward').each(function (){

        $(this).on('click',function (){

            var video = $(this).closest('.controls').siblings('.video').get(0);
            video.currentTime = video.currentTime + 5;
            video.play();

        });

    });

    //backward 5 seconds
    $('.backward').each(function (){

        $(this).on('click',function (){

            var video = $(this).closest('.controls').siblings('.video').get(0);
            video.currentTime = video.currentTime - 5;
            video.play();

        });

    });

    var timeFormat = function(seconds){
        var m = Math.floor(seconds/60)<10 ? "0"+Math.floor(seconds/60) : Math.floor(seconds/60);
        var s = Math.floor(seconds-(m*60))<10 ? "0"+Math.floor(seconds-(m*60)) : Math.floor(seconds-(m*60));
        return m+":"+s;
    };

});