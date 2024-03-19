/**
 * Does the favorite products collection
 *
 * @instructions
 * button must have:
 * 1."add-user-favorites" class
 * 2."data-product-id" attribute that hold product id
 * 4."added" class if it is already added in favorites
 */

$(document).ready(function (){

    $('.add-user-favorites').each(function (){

        $(this).on('click',function(){

            var favoriteElement = $(this);
            var productID = $(this).attr('data-product-id');
            var isFavorite = $(this).hasClass('added');

            $.ajax({
                type: "post",
                url: PHPVARS.ajaxurl,
                data: {
                    action: 'userFavorite',
                    product_id: productID,
                    is_favorite: isFavorite,
                },
                dataType: "json",
                success: function (response) {
                    if($.parseJSON(response.success))
                    {
                        favoriteElement.toggleClass('added');

                        if($.parseJSON(response.operation) == 'add')
                        {
                            $('body').append('<div class="toast fixed z-[99999999] space-x-2.5 space-x-reverse bottom-10 right-10 flex items-center justify-start border border-solid border-black/[.1] shadow-[0_0.25rem_0.75rem_rgba(0,0,0,.1)] rounded-[0.25rem] overflow-hidden px-[15px] py-1.5 md:w-[350px] bg-white/[.85]">' +
                                '<span class="w-5 h-5 bg-green-500 rounded-[0.25rem]"></span>' +
                                '<span class="text-xs font-medium text-right">به علاقه مندی ها اضافه شد</span>' +
                                '</div>');
                        }

                        else
                        {
                            $('body').append('<div class="toast fixed z-[99999999] bottom-10 right-10 flex space-x-2.5 space-x-reverse items-center justify-start border border-solid border-black/[.1] shadow-[0_0.25rem_0.75rem_rgba(0,0,0,.1)] rounded-[0.25rem] overflow-hidden md:w-[350px] px-[15px] py-1.5 bg-white/[.85]">' +
                                '<span class="w-5 h-5 bg-yellow-500 rounded-[0.25rem]"></span>' +
                                '<span class="text-xs font-medium text-right">از علاقه مندی ها حذف شد</span>' +
                                '</div>');
                        }

                        setTimeout(function (){

                            $('body > .toast').remove();

                        },30000);
                    }

                    else
                    {
                        if($.parseJSON(response.operation) == 'notlogged')
                        {
                            $('body').append('<div class="toast fixed z-[99999999] space-x-2.5 space-x-reverse bottom-10 right-10 flex items-center justify-start border border-solid border-black/[.1] shadow-[0_0.25rem_0.75rem_rgba(0,0,0,.1)] rounded-[0.25rem] overflow-hidden px-[15px] py-1.5 md:w-[350px] bg-white/[.85]">' +
                                '<span class="w-5 h-5 bg-green-500 rounded-[0.25rem]"></span>' +
                                '<span class="text-xs font-medium text-right">ابتدا باید به حساب خود ورود کنید</span>' +
                                '</div>');

                            setTimeout(function (){

                                $('body > .toast').remove();

                            },30000);
                        }
                    }
                }
            });
        });
    });
});