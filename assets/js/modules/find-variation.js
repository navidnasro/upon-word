$(document).ready(function ()
{
    var selectedVarablesCount = 0;
    var productID = $('.variations_form').attr('data-product_id');
    var totalVariables = $('.product-variable-set').length;
    var selectedVariables = [];

    //product variation
    $('.product-variation').on('click',function()
    {
        var attributeName = $(this).attr('data-attribute-name');
        var attributeValue = $(this).attr('data-attribute-value');

        if(!$(this).hasClass('selected'))
        {
            var regularPrice;
            var salePrice;
            var salePercentage;
            var imgSrc;
            var variationID;

            // if is not already in list, increase the number of attributes
            if (!selectedVariables.hasOwnProperty(attributeName))
            {
                selectedVarablesCount++;
            }
            // update the list
            selectedVariables[attributeName] = attributeValue;

            if (selectedVarablesCount == totalVariables)
            {
                var formData = new FormData();
                formData.append('action','findVariation');
                formData.append('productid',productID);

                for (var key in selectedVariables)
                    formData.append(key,selectedVariables[key]);

                $.ajax(
                    {
                        type: 'POST',
                        url: PHPVARS.ajaxurl,
                        data: formData,
                        processData: false, // Still needed to prevent processing
                        contentType: false, // Still needed for correct content type

                        success: function (response)
                        {
                            if (response.success == true)
                            {
                                regularPrice = response.regularPrice;
                                salePrice = response.salePrice;
                                salePercentage = response.salePercentage;
                                imgSrc = response.image;
                                variationID = response.id;

                                //changing the product main img
                                $('#product-thumb img').attr('src',imgSrc);

                                //changing the price in sidebar
                                if (salePercentage != 0)
                                {
                                    $('#buy-box .product-normal-price').parent().removeClass('hidden');
                                    $('#buy-box .product-normal-price').text(regularPrice);
                                    $('#buy-box .product-sale-percentage').text('%'+salePercentage);
                                }

                                else
                                {
                                    $('#buy-box .product-normal-price').parent().addClass('hidden');
                                }

                                $('#buy-box .product-sale-price span:first-child').text(salePrice);

                                //changing the price in purchase card
                                $('.purchase-box-sale-percentage').text('%'+salePercentage);
                                $('.purchase-box-sale-price').text(salePrice);

                                //changing the variation id of form's hidden input field
                                $('.variation_id').val(variationID);
                                $("#add-to-cart").removeAttr("disabled");

                                fireMessage('محصول با ویژگی های انتخاب شده یافت شد!','success');
                            }

                            else
                            {
                                fireMessage('محصول با ویژگی های انتخاب شده یافت نشد!','error');
                            }
                        }
                    }
                );
            }

            $(this).siblings('.product-variation.selected').removeClass('selected');
            $(this).addClass('selected');
        }

        else
        {
            selectedVarablesCount--;
        }
    });
});