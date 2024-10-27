<?php

namespace engine\admin\metaboxes;

use engine\security\Escape;
use engine\utils\Request;
use engine\VarDump;
use WP_Post;

class ProductPreview implements MetaBox
{
    private string $ID;
    private string $title;
    private string $screen;
    private string $context;
    private string $priority;

    public function __construct()
    {
        $this->ID = 'product-preview';
        $this->title = Escape::htmlWithTranslation('پیش نمایش محصول');
        $this->screen = 'product'; //screen or post-type   hint : get_post_types() => (adds metabox to all post types)
        $this->context = 'normal'; // Context
        $this->priority = 'high'; // position of meta box
    }

    public function ui(WP_Post $post): void
    {
        $previewType = get_post_meta($post->ID,'preview-type',true);
        $previewFile = get_post_meta($post->ID,'preview-file',true);
        $previewURL = get_post_meta($post->ID,'preview-url',true);
        $previewTitle = get_post_meta($post->ID,'preview-title',true);

        $fileName = '';

        if(!empty($previewFile))
            $fileName = basename(wp_get_attachment_url($previewFile));
        ?>
        <div style="display: flex;flex-direction: column">
            <div style="margin-bottom: 8px;display: flex;align-items: center">
                <div style="display: flex;align-items: center;margin: 0 3px">
                    <span><?php echo Escape::htmlWithTranslation('قرار دادن لینک خارجی') ?></span>
                    <input id="preview-type-url" <?php echo $previewType == 'url' ? 'checked' : '' ?> type="radio" name="preview-type" value="url">
                </div>
                <div style="display: flex;align-items: center;margin: 0 3px">
                    <span><?php echo Escape::htmlWithTranslation('آپلود کردن فایل') ?></span>
                    <input id="preview-type-file" <?php echo $previewType == 'file' ? 'checked' : '' ?> type="radio" name="preview-type" value="file">
                </div>
                <div style="display: flex;align-items: center;margin: 0 3px">
                    <span><?php echo Escape::htmlWithTranslation('هیچ کدام') ?></span>
                    <input id="preview-type-none" type="radio" <?php echo empty($previewType) || $previewType == 'none' ? 'checked' : '' ?> name="preview-type" value="none">
                </div>
            </div>
            <div id="preview-link" style="margin-bottom: 8px;display: <?php echo $previewType == 'url' ? 'flex' : 'none' ?>;align-items: center;justify-content: start">
                <p style="margin: 0 5px">لینک پیش نمایش</p>
                <input type="text" name="preview-url" value="<?php echo !empty($previewURL) ? $previewURL : '' ?>" placeholder="<?php echo Escape::htmlWithTranslation('لینک را وارد کنید') ?>">
            </div>
            <div id="preview-file-uploader" style="display: <?php echo $previewType == 'file' ? 'flex' : 'none' ?> ;margin-bottom: 8px; align-items: center; justify-content: flex-start;">
                <button id="pdf-preview-upload-btn" type="button" class="button" style="margin: 0 10px 0;">
                    <?php echo Escape::htmlWithTranslation('آپلود فایل') ?>
                </button>
                <button id="pdf-preview-remove-btn" type="button" class="button" style="margin: 0 10px 0; display: <?php echo empty($previewFile) ? 'none' : 'block' ?>;">
                    <?php echo Escape::htmlWithTranslation('حذف فایل') ?>
                </button>
                <p style="margin: 0 10px 0;">
                    <?php echo Escape::htmlWithTranslation('فایل آپلود شده : ') ?>
                    <small>
                        <?php echo Escape::htmlWithTranslation('(این فایل پیش نمایش محصول هست که در صفحه سینگا نمایش داده میشود.اگر محصول شما دارای پیش نمایش نیست فایل آپلود نکنید)') ?>
                    </small>
                </p>
                <p id="file-name" style="margin: 0 10px 0;">
                    <?php echo !empty($fileName) ? $fileName : Escape::htmlWithTranslation('فایلی آپلود نشده!') ?>
                </p>
                <input id="uploaded-file-id" type="hidden" value="<?php echo !empty($previewFile) ? $previewFile : '' ?>" name="preview-file">
            </div>
            <div style="display: flex;align-items: center;justify-content: start">
                <p style="margin: 0 5px">عنوان پیش نمایش</p>
                <input type="text" name="preview-title" value="<?php echo !empty($previewTitle) ? $previewTitle : 'نگاهی به پیش نمایش محصول داشته باشید' ?>">
            </div>
        </div>

        <script>

            jQuery(document).ready(function($){

                $('#preview-type-none').on('click',function (){

                    $('#preview-link').hide();
                    $('#preview-file-uploader').hide();
                });

                $('#preview-type-url').on('click',function (){

                    $('#preview-link').css('display','flex');
                    $('#preview-file-uploader').hide();
                });

                $('#preview-type-file').on('click',function (){

                    $('#preview-file-uploader').css('display','flex');
                    $('#preview-link').hide();

                });

                var uploadButton = $('#pdf-preview-upload-btn');
                var removeButton = $('#pdf-preview-remove-btn');
                var fileName = $('#file-name');
                var fileID = $('#uploaded-file-id');

                if(fileID.val() != '')
                {
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
                        title: '<?php echo Escape::htmlWithTranslation('انتخاب فایل') ?>',
                        button: {
                            text: '<?php echo Escape::htmlWithTranslation('آپلود') ?>'
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
        $request = Request::post();

        if (is_null($request))
            return;

        // if type is set
        if ($request->has('preview-type'))
        {
            // if type is url and the field is filled and is not empty
            if ($request->getParam('preview-type') == 'url' &&
                $request->has('preview-url') &&
                !empty($request->getParam('preview-url')))
            {
                update_post_meta($postID,'preview-type','url');
                update_post_meta($postID,'preview-url',$request->getParam('preview-url'));
            }

            // if type is file and a file is uploaded , if bot uploaded contains empty string
            else if ($request->getParam('preview-type') == 'file' &&
                     $request->has('preview-file') &&
                     !empty($request->getParam('preview-file')))
            {
                update_post_meta($postID,'preview-type','file');
                update_post_meta($postID,'preview-file',$request->getParam('preview-file'));
            }

            // default option is none
            else
            {
                update_post_meta($postID,'preview-type','none');
            }
        }

        // if preview title is filled and is not empty
        if ($request->has('preview-title') &&
            !empty($request->getParam('preview-title')))
        {
            update_post_meta($postID,'preview-title',$request->getParam('preview-title'));
        }
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

    public function uiArgs(): array|null
    {
        return null;
    }
}

Register::registerMetaBox(new ProductPreview());