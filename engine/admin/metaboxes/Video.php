<?php

namespace engine\admin\metaboxes;

use WP_Post;

defined('ABSPATH') || exit;

class Video implements MetaBox
{
    private string $ID;
    private string $title;
    private string $screen;
    private string $context;
    private string $priority;

    public function __construct()
    {
        $this->ID = 'video-file';
        $this->title = 'فایل ویدئو';
        $this->screen = 'post'; //screen or post-type   hint : get_post_types() => (adds metabox to all post types)
        $this->context = 'normal'; // Context
        $this->priority = 'high'; // position of meta box
    }

    /**
     * MetaBox UI for video upload if the post-format is video
     *
     * @param WP_Post $post
     * @return void
     */
    public function ui(WP_Post $post): void
    {
        $videoFile = get_post_meta($post->ID,'video_file',true);
        $attachmentUrl = '';

        if( $videoFile )
            $attachmentUrl = wp_get_attachment_url($videoFile);
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
            <p id="file-name" style="margin: 0 10px 0;">
                <?php echo $attachmentUrl ? $attachmentUrl : 'فایلی آپلود نشده!' ?>
            </p>
        </div>
        <input id="uploaded-file-id" type="hidden" value="<?php echo $videoFile ? $videoFile : '' ?>" name="uploaded-file-id">

        <script>

            jQuery(document).ready(function($){

                if($('#post-format-video').attr('checked'))
                {
                    $('#video-file').show();
                }

                $('.post-format').on('click',function (){

                    if($(this).attr('id') === 'post-format-video')
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
                        title: '<?php esc_html_e( 'یک فایل انتخاب کنید', 'themsah' ); ?>',
                        button: {
                            text: '<?php esc_html_e( 'آپلود', 'themsah' ); ?>'
                        },
                        multiple: false, // multiple file selection
                    });

                    //when a file is selected in media frame
                    pdfMediaFrame.on('select',function(){

                        // Get media attachment details from the frame state
                        var attachment = pdfMediaFrame.state().get('selection').first().toJSON();
                        // Send the attachment URL to image input field
                        fileName.text(attachment.url);
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

    public function save(int $postID): void
    {
        if (isset($_POST['uploaded-file-id']) && $_POST['uploaded-file-id'])
            update_post_meta($postID, 'video_file', $_POST['uploaded-file-id']);
    }

    public function getID(): string
    {
        return $this->ID;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getScreen(): string
    {
        return $this->screen;
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function uiArgs(): array | null
    {
        return null;
    }
}

Register::registerMetaBox(new Video());