/**
 * Does the compare functionality
 * 1.adding to compare via ajax call (mostly in product card)
 * 2.adding product to compare in compare page
 * 3.removing product from compare in compare page
 *
 * @instructions
 * button must have:
 * 1."compare" class
 * 2."data-product-id" attribute that hold product id
 * 3."data-page-link" attribute that hold compare page link
 * 4."added" class if it is already added in compare
 */

$(document).ready(function (){

    $('.compare').each(function (){

        $(this).on('click',function(){

            var compareElement = $(this);
            var productID = $(this).attr('data-product-id');
            var link = $(this).attr('data-page-link');
            var isAdded = $(this).hasClass('added');

            $.ajax({
                type: "post",
                url: PHPVARS.ajaxurl,
                data: {
                    action: 'compareProducts',
                    product_id: productID,
                },
                dataType: "json",
                success: function (response) {

                    if($.parseJSON(response.success))
                    {
                        compareElement.toggleClass('added');

                        if($.parseJSON(response.operation) == 'add')
                        {
                            $('body').append('<div class="toast fixed z-[99999999] space-x-2.5 space-x-reverse bottom-10 right-10 flex items-center justify-start border border-solid border-black/[.1] shadow-[0_0.25rem_0.75rem_rgba(0,0,0,.1)] rounded-[0.25rem] overflow-hidden px-[15px] py-1.5 md:w-[350px] bg-white/[.85]">' +
                                    '<span class="w-5 h-5 bg-green-500 rounded-[0.25rem]"></span>' +
                                    '<a href="'+link+'" class="text-xs font-medium text-right">به مقایسه اضافه شد</a>' +
                                '</div>');
                        }

                        else
                        {
                            $('body').append('<div class="toast fixed z-[99999999] bottom-10 right-10 flex space-x-2.5 space-x-reverse items-center justify-start border border-solid border-black/[.1] shadow-[0_0.25rem_0.75rem_rgba(0,0,0,.1)] rounded-[0.25rem] overflow-hidden md:w-[350px] px-[15px] py-1.5 bg-white/[.85]">' +
                                    '<span class="w-5 h-5 bg-yellow-500 rounded-[0.25rem]"></span>' +
                                    '<a href="'+link+'" class="text-xs font-medium text-right">از مقایسه حذف شد</a>' +
                                '</div>');
                        }

                        setTimeout(function (){

                            $('body > .toast').remove();

                        },30000);
                    }
                }
            });
        });

    });

    $('.add-product-compare').each(function (){

        $(this).on('click',function (){

            var productID = $(this).attr('data-product-id');

            $.ajax({
                type: "get",
                url: PHPVARS.ajaxurl,
                data: {
                    action: 'addProductToCompare',
                    product_id: productID,
                },
                dataType: "html",
                success: function (response)
                {
                    $('.comparing-products').append(response);
                    location.reload();
                }
            });

        });

    });

    $('.remove-from-compare').each(function (){

        $(this).on('click',function (){

            var productID = $(this).attr('data-product-id');

            $.ajax({
                type: "get",
                url: PHPVARS.ajaxurl,
                data: {
                    action: 'removeProductFromCompare',
                    product_id: productID,
                },
                dataType: "html",
                success: function (response)
                {
                    $('.comparing-products').append(response);
                    location.reload();
                }
            });

        });

    });

});