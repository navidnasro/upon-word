<?php

namespace engine\admin\fields\productAttributes;

use engine\database\enums\Table;
use engine\database\QueryBuilder;
use engine\security\Escape;

defined('ABSPATH') || exit;

class Icon extends Base
{
    /**
     * @return void
     */
    public function addField(): void
    {
        ?>
        <div class="video-metabox" style="display: flex ; align-items: center; justify-content: flex-start;">
            <button id="pdf-preview-upload-btn" type="button" class="button" style="margin: 0 10px 0;">
                آپلود
            </button>
            <button id="pdf-preview-remove-btn" type="button" class="button" style="margin: 0 10px 0; display: none;">
                حذف
            </button>
            <p style="margin: 0 10px 0;">
                فایل آپلود شده:
            </p>
            <img id="file-name" src="">
        </div>
        <input id="uploaded-file-id" type="hidden" value="" name="uploaded-file-id">

        <script>

        jQuery(document).ready(function($){

            if($('#post-format-video').attr('checked'))
            {
                $('#video-file').show();
            }

            $('.post-format').on('click',function (){

                if($(this).attr('id') == 'post-format-video')
                    $('#video-file').show();

                else
                    $('#video-file').hide();
            });

            var uploadButton = $('#pdf-preview-upload-btn');
            var removeButton = $('#pdf-preview-remove-btn');
            var fileName = $('#file-name');
            var fileID = $('#uploaded-file-id');

            if( $('#uploaded-file-id').val() != '' ){

                uploadButton.hide();
                removeButton.show();

            }

            var pdfMediaFrame;

            uploadButton.on('click',function(event){

                event.preventDefault();

                if( pdfMediaFrame ){
                    pdfMediaFrame.open();
                    return;
                }

                //create a new media frame
                pdfMediaFrame = wp.media({
                            title: '<?php echo Escape::htmlWithTranslation("یک فایل انتخاب کنید") ?>',
                            button: {
                    text: '<?php echo Escape::htmlWithTranslation("آپلود") ?>'
                            },
                            multiple: false, // multiple file selection
                        });

                        //when a file is selected in media frame
                        pdfMediaFrame.on('select',function(){

                            // Get media attachment details from the frame state
                            var attachment = pdfMediaFrame.state().get('selection').first().toJSON();
                            // Send the attachment URL to image input field
                            fileName.attr('src',(attachment.url));
                            // Send the attachment id to our hidden input
                            fileID.val(attachment.id);
                            // Remove upload image button
                            uploadButton.hide();
                            // Show remove image button
                            removeButton.show();

                        });
                        // Finally, open the modal on click
                        pdfMediaFrame.open();

                    });

            //Remove Button Click

            removeButton.on('click',function(event){

                event.preventDefault();

                // Send the attachment id to our hidden input
                fileID.val('');
                // Send the attachment URL to image input field
                fileName.text('');
                // Remove remove image button
                removeButton.hide();
                // Show upload image button
                uploadButton.show();

            });

        });

        </script>
        <?php
    }

    /**
     * @return void
     */
    public function editField(): void
    {
        $id = $_GET['edit'];

        $builder = QueryBuilder::getInstance();

        $taxonomy = $builder->select('attribute_name')
            ->from(Table::WOOCOMMERCE_ATTRIBUTE_TAXONOMIES)
            ->where('attribute_id','=',$id)
            ->getVar();

//        $taxonomy = $wpdb->get_var(
//            "SELECT attribute_name
//             from {$wpdb->prefix}woocommerce_attribute_taxonomies
//             WHERE attribute_id={$id}"
//        );

        $icon = get_option('attribute_'.$id.'_icon');
        ?>
        <input
            type="hidden"
            name="taxonomy"
            value="<?php echo $taxonomy ?>">

        <div class="video-metabox" style="display: flex ; align-items: center; justify-content: flex-start;">
            <button id="pdf-preview-upload-btn" type="button" class="button" style="margin: 0 10px 0;">
                آپلود
            </button>
            <button id="pdf-preview-remove-btn" type="button" class="button"
                    style="margin: 0 10px 0;
                        display: <?php echo $icon && $icon != 0 ? 'block' : 'none' ?>;">
                حذف
            </button>
            <p style="margin: 0 10px 0;">
                فایل آپلود شده:
            </p>
            <img id="file-name" src="<?php echo $icon ? wp_get_attachment_image_url($icon) : '' ?>">
        </div>
        <input id="uploaded-file-id" type="hidden" value="" name="uploaded-file-id">

        <script>

            jQuery(document).ready(function($){

                var uploadButton = $('#pdf-preview-upload-btn');
                var removeButton = $('#pdf-preview-remove-btn');
                var fileName = $('#file-name');
                var fileID = $('#uploaded-file-id');

                if( $('#uploaded-file-id').val() != '' ){

                    uploadButton.hide();
                    removeButton.show();

                }

                var pdfMediaFrame;

                uploadButton.on('click',function(event){

                    event.preventDefault();

                    if( pdfMediaFrame ){
                        pdfMediaFrame.open();
                        return;
                    }

                    //create a new media frame
                    pdfMediaFrame = wp.media({
                        title: '<?php echo Escape::htmlWithTranslation("یک فایل انتخاب کنید") ?>',
                        button: {
                            text: '<?php echo Escape::htmlWithTranslation("آپلود") ?>'
                        },
                        multiple: false, // multiple file selection
                    });

                    //when a file is selected in media frame
                    pdfMediaFrame.on('select',function(){

                        // Get media attachment details from the frame state
                        var attachment = pdfMediaFrame.state().get('selection').first().toJSON();
                        // Send the attachment URL to image input field
                        fileName.attr('src',(attachment.url));
                        // Send the attachment id to our hidden input
                        fileID.val(attachment.id);
                        // Remove upload image button
                        uploadButton.hide();
                        // Show remove image button
                        removeButton.show();

                    });
                    // Finally, open the modal on click
                    pdfMediaFrame.open();

                });

                //Remove Button Click

                removeButton.on('click',function(event){

                    event.preventDefault();

                    // Send the attachment id to our hidden input
                    fileID.val('');
                    // Send the attachment URL to image input field
                    fileName.attr('src','');
                    // Remove remove image button
                    removeButton.hide();
                    // Show upload image button
                    uploadButton.show();

                });

            });

        </script>

        <?php
    }

    /**
     * @param int $id
     * @param array $data
     * @return void
     */
    public function save(int $id,array $data): void
    {
        if (isset($_POST['uploaded-file-id']))
            update_option('attribute_'.$id.'_icon',$_POST['uploaded-file-id']);
    }

    /**
     * @param int $id
     * @param array $data
     * @param string $oldSlug
     * @return void
     */
    public function update(int $id,array $data,string $oldSlug): void
    {
        if (isset($_POST['uploaded-file-id']))
            update_option('attribute_'.$id.'_icon',$_POST['uploaded-file-id']);
    }
}

new Icon();