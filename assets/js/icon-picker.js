jQuery(document).ready(function($){

    var isOpen = false;

    $('.icon-picker').each(function (){
        
        $(this).on('click',function(){

            var iconPicker = $(this);

            //icon picker is closed
            if(!isOpen)
            {
                $(this).siblings('.icon-wrapper').css('display','flex');

                isOpen = true;

                var hiddenInput = $(this).siblings('input');

                //for each icon
                $(this).siblings('.icon-wrapper').children('.icons').children('.icon').each(function(){

                    $(this).on('click',function(){

                        var iconClass = $(this).attr('data-icon');

                        //relevant icon holder
                        var iconHolder = iconPicker.children('span');

                        //clear icon picker
                        iconHolder.children('svg').remove();
                        iconHolder.children('i').remove();

                        iconHolder.append('<i class="'+iconClass+'"></i>');

                        //set icon class for hidden input
                        hiddenInput.val(iconClass);

                    });

                });
            }

            //icon picker is opened
            else
            {
                $(this).siblings('.icon-wrapper').css('display','none');
                isOpen = false;
            }
        });
        
    });
        

    //search input
    $('.icon-search').each(function (){

        var searchWrapper = $(this);

        $(this).children('input').on('keyup',function(){

            var text = $(this).val();

            //filter according to input text
            searchWrapper.siblings('.icons').children().each(function(){

                if($(this).attr('data-icon').indexOf(text) >= 0)
                    $(this).css('display','flex');

                else
                    $(this).css('display','none');
            });
        });

    });


    //upload button
    $('.icon-upload').each(function (){

        $(this).children('button').on('click',function(event){

            event.preventDefault();

            //clicked upload button
            var button = $(this);

            var mediaFrame;

            //if media frame already exists , reopen it
            if( mediaFrame ){
                mediaFrame.open();
                return;
            }

            //create a new media frame
            mediaFrame = wp.media({
                title: 'یک تصویر انتخاب کنید',
                button: {
                    text: 'انتخاب تصویر'
                },
                multiple: false, // multiple file selection
            });

            //when an image is selected in media frame
            mediaFrame.on('select',function(){

                // Get media attachment details from the frame state
                var attachment = mediaFrame.state().get('selection').first().toJSON();

                if(attachment.subtype != 'svg+xml')
                {
                    alert('فقط فایل با پسوند svg می توانید آپلود کنید');

                    return;
                }

                //relevant icon picker
                var iconPicker = button.parent().parent().siblings('button');
                //relevant icon holder
                var iconHolder = iconPicker.children('span');
                //relevant hidden input
                var hiddenInput = iconPicker.siblings('input');

                //load the content of uploaded svg file into relevant icon holder (get svg code)
                iconHolder.load(attachment.url);

                setTimeout(function(){
                    console.log(123);
                    //set icon class for hidden input
                    hiddenInput.val(iconHolder.html());

                },3000);

            });

            // Finally, open the modal on click
            mediaFrame.open();

        });

    });

    //Remove Button Click
    $('.icon-remove').each(function (){

        $(this).children('button').on('click',function(event){

            event.preventDefault();

            var iconPicker = $(this).parent().parent().siblings('button');
            var hiddenInput = iconPicker.siblings('input');

            //clear icon picker
            iconPicker.children('svg').remove();
            iconPicker.children('i').remove();

            // Reset hidden input id
            hiddenInput.val('');

        });

    });

    //pagination
    $('.icon-pagination').each(function (){

        $(this).on('click',function (){

            var iconsElement = $(this).parent().siblings('.icons');
            var iconPicker = iconsElement.parent().siblings('.icon-picker');
            var hiddenInput = iconPicker.siblings('input');

            var page = $(this).attr('data-page');

            $.ajax(
                {
                    type: 'get',
                    url: url.ajax_url,
                    data: {
                        action: 'iconPagination',
                        page: page,
                    },
                    dataType: 'html',

                    success: function (response)
                    {
                        iconsElement.empty();
                        iconsElement.append(response);

                        //for each newly added icon set on click event
                        iconsElement.children('.icon').each(function(){

                            $(this).on('click',function(){

                                var iconClass = $(this).attr('data-icon');

                                //relevant icon holder
                                var iconHolder = iconPicker.children('span');

                                //clear icon picker
                                iconHolder.children('svg').remove();
                                iconHolder.children('i').remove();

                                iconHolder.append('<i class="'+iconClass+'"></i>');

                                //set icon class for hidden input
                                hiddenInput.val(iconClass);

                            });

                        });
                    }
                }
            );

        });

    });

});