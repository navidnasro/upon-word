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
            var nonce = $(this).children('input[name="add-favorite"]').val();

            $.ajax({
                type: "post",
                url: PHPVARS.ajaxurl,
                data: {
                    action: 'userFavorite',
                    product_id: productID,
                    nonce: nonce,
                    is_favorite: isFavorite,
                },
                success: function (response) {

                    if(response.success)
                    {
                        favoriteElement.toggleClass('added');

                        if(response.operation == 'add')
                        {
                            favoriteElement.find('span:last-child svg').empty().append('<svg xmlns="http://www.w3.org/2000/svg" width="15" height="18" viewBox="0 0 15 18" fill="none"><path d="M12.8212 17.6165L12.8212 17.6165H12.8236C13.2288 17.6165 13.6084 17.4555 13.888 17.1688C14.1671 16.8827 14.3232 16.4954 14.3232 16.0924V4.50544C14.3232 3.30602 13.9067 2.38392 13.1495 1.76509C12.3968 1.15003 11.3265 0.85 10.0448 0.85H5.12844C3.82823 0.85 2.75657 1.16373 2.00789 1.80537C1.25555 2.45015 0.85 3.4083 0.85 4.65091V16.0924H0.849981L0.850019 16.0947C0.854064 16.3492 0.913689 16.5902 1.02502 16.8081L1.02467 16.8083L1.03032 16.8176L1.10742 16.9448L1.112 16.9524L1.11744 16.9593L1.19815 17.0628L1.19804 17.0629L1.20228 17.0678C1.66667 17.6124 2.41441 17.7673 3.03369 17.4373L3.03371 17.4373L3.03662 17.4357L7.56544 14.8914L12.1129 17.4305L12.1128 17.4305L12.116 17.4322C12.3423 17.5514 12.5792 17.6126 12.8212 17.6165Z" fill="#0E1935" stroke="#0E1935" stroke-width="0.3"></path></svg>');
                            fireMessage('با موقیت به علاقه مندی ها اضافه شد','success');
                        }

                        else
                        {
                            favoriteElement.find('span:last-child svg').empty().append('<svg xmlns="http://www.w3.org/2000/svg" width="15" height="18" viewBox="0 0 15 18" fill="none"><path d="M2.1625 16.0824L2.1625 15.9938C2.18648 15.9933 2.20648 15.9987 2.22064 16.0043C2.25031 16.0162 2.26783 16.0344 2.2723 16.0391C2.27908 16.0462 2.28343 16.0522 2.28515 16.0547L2.24451 16.0811L2.1625 16.0824ZM2.1625 16.0824C2.16273 16.0964 2.16418 16.1103 2.16534 16.1214C2.16575 16.1253 2.16612 16.1288 2.16639 16.1319L2.24246 16.0824L2.1625 16.0824ZM12.8212 17.6165L12.8212 17.6165H12.8236C13.2288 17.6165 13.6084 17.4555 13.888 17.1688C14.1671 16.8827 14.3232 16.4954 14.3232 16.0924V4.50544C14.3232 3.30602 13.9067 2.38392 13.1495 1.76509C12.3968 1.15003 11.3265 0.85 10.0448 0.85H5.12844C3.82823 0.85 2.75657 1.16373 2.00789 1.80537C1.25555 2.45015 0.85 3.4083 0.85 4.65091V16.0924H0.849981L0.850019 16.0947C0.854064 16.3492 0.913689 16.5902 1.02502 16.8081L1.02467 16.8083L1.03032 16.8176L1.10742 16.9448L1.112 16.9524L1.11744 16.9593L1.19815 17.0628L1.19804 17.0629L1.20228 17.0678C1.66667 17.6124 2.41441 17.7673 3.03369 17.4373L3.03371 17.4373L3.03662 17.4357L7.56544 14.8914L12.1129 17.4305L12.1128 17.4305L12.116 17.4322C12.3423 17.5514 12.5792 17.6126 12.8212 17.6165ZM2.31739 16.1075L2.31418 16.1021C2.31413 16.1017 2.31408 16.1012 2.31403 16.1007C2.31319 16.0927 2.31265 16.0875 2.3125 16.081L2.31258 4.65091C2.31258 3.8423 2.55066 3.27657 3.00101 2.90814C3.45743 2.53474 4.15804 2.34197 5.12844 2.34197H10.0448C11.0031 2.34197 11.7058 2.52713 12.1663 2.88109C12.6194 3.22931 12.8607 3.75869 12.8607 4.50544V16.0924C12.8607 16.1021 12.8568 16.1107 12.8512 16.1165C12.8462 16.1217 12.8402 16.1242 12.8345 16.1246L12.8318 16.1242C12.8293 16.1239 12.8261 16.1232 12.8224 16.1222C12.8151 16.1202 12.8075 16.1173 12.8005 16.1137L8.18955 13.5395L8.18911 13.5393C7.79948 13.3235 7.33144 13.3233 6.94323 13.5384L6.94246 13.5388L2.34634 16.1204C2.34525 16.1205 2.34209 16.1208 2.33676 16.1194C2.32937 16.1175 2.32246 16.1132 2.31739 16.1075Z" fill="#0E1935" stroke="#0E1935" stroke-width="0.3"></path></svg>');
                            fireMessage('با موقیت از علاقه مندی ها حذف شد','success');
                        }
                    }

                    else
                    {
                        if(response.operation == 'notlogged')
                        {
                            fireMessage('ابتدا باید به حساب خود '+'<a href="'+response.link+'">ورود کنید</a>','error');
                        }
                        
                        else if(response.operation == 'spam')
                        {
                            fireMessage('تعداد درخواست های شما بیش از حد شده.لطفا بعدا دوباره تلاش کنید','error');
                        }
                    }
                }
            });
        });
    });

    $('.remove-from-user-favorites').each(function ()
    {
        $(this).on('click', function ()
        {
            var button = $(this);
            var parent = button.attr('data-parent');
            var productId = button.attr('data-id');
            var nonce = button.children('input[name="remove-user-favorite"]').val();

            $.ajax({
                type: "post",
                url: PHPVARS.ajaxurl,
                data: {
                    action: 'removeUserFavorite',
                    product_id: productId,
                    nonce: nonce,
                },
                success: function (response)
                {
                    if(response.success)
                    {
                        if(response.operation == 'add')
                        {
                            button.parents(parent).remove();

                            if (response.message)
                                $(parent).parent().append(response.message);

                            fireMessage('با موقیت از علاقه مندی ها حذف شد','success');
                        }
                    }

                    else
                    {
                        if(response.operation == 'spam')
                        {
                            fireMessage('تعداد درخواست های شما بیش از حد شده.لطفا بعدا دوباره تلاش کنید','error');
                        }

                        else
                        {
                            fireMessage('خطایی رخ داد!','error');
                        }
                    }
                },

                error: function (response)
                {
                    fireMessage('خطایی رخ داد!','error');
                }
            });
        });
    });
});