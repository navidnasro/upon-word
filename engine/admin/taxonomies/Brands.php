<?php

namespace engine\admin\taxonomies;

use engine\utils\LabelUtils;
use engine\utils\ThemeUtils;
use WP_Term;

defined('ABSPATH') || exit;

class Brands implements Taxonomy
{
    private string $ID;
    private string $postType;
    private string $singular;
    private string $description;
    private bool $public;
    private bool $hierarchical;
    private bool $showAdminColumn;

    public function __construct()
    {
        $this->ID = 'brands';
        $this->singular = 'برند';
        $this->postType = 'product';
        $this->description = 'برند محصولات (درصورت وجود)';
        $this->public = true;
        $this->hierarchical = false;
        $this->showAdminColumn = false; //display a column for the taxonomy on its post type
    }

    public function createFields(): void
    {
        ?>
        <!-- Upload Thumbnail -->

        <div class="form-field">
            <label>
                تصویر برند
            </label>
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <input type="hidden" id="category_thumbnail_id" name="category_thumbnail" value="0" />
                    <button type="button" class="upload_image_button button">
                        آپلود/اضافه کردن تصویر
                    </button>
                    <button type="button" class="remove_image_button button">
                        حذف تصویر
                    </button>
                </div>
                <div id="category-thumbnail">
                    <img src="<?php echo esc_url( ThemeUtils::getDefaultImageUrl() ); ?>" width="60px" height="60px" alt="">
                </div>
            </div>
        </div>

        <script>

            jQuery(document).ready(function($){

                var thumbnail = $('#category-thumbnail').find('img');
                var thumbnailID = $('#category_thumbnail_id');
                var uploadButton = $('.upload_image_button');
                var removeButton = $('.remove_image_button');

                // If hidden input value is no img , there is no image so hide remove button

                if( thumbnailID.val() == '0' )
                    removeButton.hide();

                // Uploading files

                var mediaFrame;

                //Upload Button Click

                uploadButton.on('click',function(event){

                    event.preventDefault();

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
                        // Send the attachment URL to image input field
                        thumbnail.attr('src',attachment.url);
                        // Send the attachment id to our hidden input
                        thumbnailID.val(attachment.id);
                        // Remove upload image button
                        uploadButton.hide();
                        // Show remove image button
                        removeButton.show();

                    });

                    // Finally, open the modal on click
                    mediaFrame.open();
                });

                //Remove Button Click

                removeButton.on('click',function(event){

                    event.preventDefault();

                    // Set placeholder img
                    thumbnail.attr('src','<?php echo esc_url( ThemeUtils::getDefaultImageUrl() ); ?>');
                    // Reset hidden input id
                    thumbnailID.val('0');
                    // Remove remove image button
                    removeButton.hide();
                    // Show upload image button
                    uploadButton.show();

                });

            });

        </script>
        <?php
    }

    public function editFields(WP_Term $term): void
    {
        $slug = $term->slug;

        $thumbnail_ID = 0;

        if(has_term_meta($term->term_id))
            $thumbnail_ID = get_term_meta($term->term_id,'thumbnail_id',true);

        $has_thumbnail = ( $thumbnail_ID != 0 ) ? $thumbnail_ID : 0;

        if( $thumbnail_ID )
            $thumbnail = wp_get_attachment_thumb_url($thumbnail_ID);
        else
            $thumbnail = ThemeUtils::getDefaultImageUrl();

        // form to be displayed in taxonamy creation page
        ?>
        <!-- Upload Thumbnail -->

        <tr class="form-field">
            <th scope="row" valign="top">
                <label>
                    تصویر برند
                </label>
            </th>
            <td style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <input type="hidden" id="category_thumbnail_id" name="category_thumbnail" value="<?= $has_thumbnail ?>" />
                    <button type="button" class="upload_image_button button">
                        آپلود/اضافه کردن تصویر
                    </button>
                    <button type="button" class="remove_image_button button">
                        حذف تصویر
                    </button>
                </div>
                <div id="category-thumbnail">
                    <img src="<?php echo esc_url( $thumbnail ); ?>" width="60px" height="60px" alt="">
                </div>
            </td>
        </tr>

        <script>

            jQuery(document).ready(function($){

                var thumbnail = $('#category-thumbnail').find('img');
                var thumbnailID = $('#category_thumbnail_id');
                var uploadButton = $('.upload_image_button');
                var removeButton = $('.remove_image_button');

                // If hidden input value is no img , no image is uploaded so hide remove button
                if( thumbnailID.val() == '0' )
                    removeButton.hide();

                var mediaFrame;

                uploadButton.on('click',function(event){

                    event.preventDefault();

                    if( mediaFrame ){
                        mediaFrame.open();
                        return;
                    }

                    //create a new media frame
                    mediaFrame = wp.media({
                        title: 'انتخاب تصویر',
                        button: {
                            text: 'تایید'
                        },
                        multiple: false, // multiple file selection
                    });

                    //when an image is selected in media frame
                    mediaFrame.on('select',function(){

                        // Get media attachment details from the frame state
                        var attachment = mediaFrame.state().get('selection').first().toJSON();
                        // Send the attachment URL to image input field
                        thumbnail.attr('src',attachment.url);
                        // Send the attachment id to our hidden input
                        thumbnailID.val(attachment.id);
                        // Remove upload image button
                        uploadButton.hide();
                        // Show remove image button
                        removeButton.show();

                    });
                    // Finally, open the modal on click
                    mediaFrame.open();

                });

                //Remove Button Click

                removeButton.on('click',function(event){

                    event.preventDefault();

                    // Set placeholder img
                    thumbnail.attr('src','<?php echo esc_url( ThemeUtils::getDefaultImageUrl() ); ?>');
                    // Reset hidden input id
                    thumbnailID.val('0');
                    // Remove remove image button
                    removeButton.hide();
                    // Show upload image button
                    uploadButton.show();

                });
            });

        </script>
        <?php
    }

    public function insert(int $termID): void
    {
        // Image is uploaded
        if( $_POST['category_thumbnail'] != 0 )
            update_term_meta($termID,'thumbnail_id',$_POST['category_thumbnail']);

        // Image is not uploaded
        else
            update_term_meta($termID,'thumbnail_id',0);
    }

    public function update(int $termID): void
    {
        // Image is set
        if($_POST['category_thumbnail'] != 0)
        {
            // Image is being set in edit page , wasn't set in create page
            if(get_term_meta($termID,'thumbnail_id',true) == 0)
                update_term_meta($termID,'thumbnail_id',$_POST['category_thumbnail']);

            // Image was set in create page and now is changed in edit page
            else
                update_term_meta($termID,'thumbnail_id',$_POST['category_thumbnail']);
        }

        // Image is not set
        else
        {
            // Image was set in create page and is now removed in edit page
            if(get_term_meta($termID,'thumbnail_id',true) != 0)
                update_term_meta($termID,'thumbnail_id',$_POST['category_thumbnail']);
        }
    }

    public function getPostType(): string
    {
        return $this->postType;
    }

    public function getID(): string
    {
        return $this->ID;
    }

    public function getLabels(): array
    {
        return LabelUtils::getLabel($this->singular);
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function isHierarchical(): bool
    {
        return $this->hierarchical;
    }

    public function hasAdminColumn(): bool
    {
        return $this->showAdminColumn;
    }
}

Register::registerTaxonomy(new Brands());