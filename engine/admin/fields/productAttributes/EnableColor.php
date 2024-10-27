<?php

namespace engine\admin\fields\productAttributes;

defined('ABSPATH') || exit;

class EnableColor extends Base
{
    /**
     * Adds a checkbox in attribute create page
     * that determines the attribute is going to be color variated
     */
    public function addField()
    {
?>
        <div class="form-field">
            <input type="checkbox" id="color_enable" name="color_checkbox">
            <label for="color_enable" style="display: inline;">
                ویژگی رنگ
            </label>
            <input type="hidden" name="color" value="disabled">
        </div>
        <script>
            jQuery(document).ready(function($){
                $('#color_enable').on('click',function(){
                    var hiddenInput = $(this).siblings('input');

                    if(hiddenInput.val() == 'disabled')
                        hiddenInput.val('enabled');

                        else
                        hiddenInput.val('disabled');
                });
            });
        </script>
<?php
    }

    public function editField(): void
    {
        $id = $_GET['edit'];

        $attributes = get_option('color_attributes');
?>
        <div class="form-field">
            <input type="checkbox" id="color_enable" name="color_checkbox" <?php echo $attributes && in_array($id,$attributes) ? 'checked' : '' ?>>
            <label for="color_enable" style="display: inline;">
                ویژگی رنگ
            </label>
            <input type="hidden" name="color" value="<?php echo in_array($id,$attributes) ? 'enabled' : 'disabled' ?>">
        </div>
        <script>
            jQuery(document).ready(function($){
                $('#color_enable').on('click',function(){
                    var hiddenInput = $(this).siblings('input');

                    if(hiddenInput.val() == 'disabled')
                        hiddenInput.val('enabled');

                    else
                        hiddenInput.val('disabled');
                });
            });
        </script>
<?php
    }

    /**
     * saves attribute "color enabled" field in the database
     * 
     * @param int $id Added attribute ID.
     * @param array $data Attribute data.
     * @return void
     */
    public function save(int $id,array $data): void
    {
        //if color checkbox is checked
        if (isset($_POST['color_checkbox']) && ($_POST['color'] == 'enabled'))
        {
            //getting color attributes stored in the database
            $attributes = get_option('color_attributes');
            
            //if option exists and id is not already included
            if($attributes && !in_array($id,$attributes))
            {
                //adding the current attribute among other color attributes
                $attributes[$data['attribute_name']] = $id;

                //inserting the updated array of ids back into the database
                update_option('color_attributes',$attributes);
            }

            //if option doesn't exist
            else
            {
                //create color attribute array
                $attributes[$data['attribute_name']] = $id;

                //insert the array into the database
                add_option('color_attributes',$attributes);
            }
        }
    }

    /**
     * @param int $id Added attribute ID.
     * @param array $data Attribute data.
     * @param string $oldSlug
     * @return void
     */
    public function update(int $id,array $data,string $oldSlug): void
    {
        $attributes = get_option('color_attributes');

        // if enable checkbox is present and is checked
        if(isset($_POST['color_checkbox']) && $_POST['color'] == 'enabled')
        {
            //if option exists and id is not already included
            if($attributes && !in_array($id,$attributes))
            {
                //adding the current attribute among other color attributes
                $attributes[$data['attribute_name']] = $id;

                //inserting the updated array of ids back into the database
                update_option('color_attributes',$attributes);
            }

            //if option doesn't exist
            else
            {
                //create color attribute array
                $attributes[$data['attribute_name']] = $id;

                //insert the array into the database
                add_option('color_attributes',$attributes);
            }
        }

        // if enable checkbox is present and is unchecked
        else if (isset($_POST['color']) && $_POST['color'] == 'disabled')
        {
            //if there are ids and this id is also included
            if ($attributes && isset($attributes[$data['attribute_name']]))
            {
                //removing the id from array
                unset($attributes[$data['attribute_name']]);
                //inserting the updated array of ids back into the database
                update_option('color_attributes',$attributes);
            }
        }
    }
}

new EnableColor();