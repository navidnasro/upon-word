$(document).ready(function ()
{
    const isCart = $('.cart-items-holder').length != 0;
    const debounceFilterProducts = debounce(updateCart, 1000);

    function debounce(func, wait)
    {
        let timeout;
        return function executedFunction(...args)
        {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    $('.add-to-cart-ajax').each(function ()
    {
        $(this).on('click',function (e)
        {
            e.preventDefault();

            var productID = $(this).attr('data-product');
            var nonce = $(this).children('input[name="add_to_cart"]').val();

            $.ajax(
            {
                type: "post",
                url: PHPVARS.ajaxurl,
                data: {
                    action: 'addToCart',
                    product_id: productID,
                    nonce: nonce
                },
                success: function (response)
                {
                    if(response.success)
                    {
                        // $('.cart-totals-text').text(response.data.count);

                        fireMessage(response.message,'success');
                    }

                    else
                    {
                        fireMessage(response.message,'error')
                    }
                },
                error: function (response)
                {
                    fireMessage(response.message,'error')
                }
            });
        });
    });

    $('.delete-product-from-cart').each(function ()
    {
        $(this).on('click',function ()
        {
            var cartKey = $(this).attr('data-product');
            var nonce = $(this).children('input[name="delete-product-from-cart"]').val();
            var cartItem = $(this).parents('.cart-item');

            $.ajax(
            {
                type: "post",
                url: PHPVARS.ajaxurl,
                data: {
                    action: 'removeFromCart',
                    cartitemkey: cartKey,
                    nonce: nonce
                },
                success: function (response)
                {
                    if(response.success)
                    {
                        // $('.cart-totals-text').text(response.data.count);

                        fireMessage(response.message,'success');

                        cartItem.remove();
                    }

                    else
                    {
                        fireMessage(response.message,'error')
                    }
                },
                error: function (response)
                {
                    fireMessage(response.message,'error')
                }
            });
        });
    });

    $('.plus').each(function ()
    {
        $(this).on('click',function ()
        {
            if (!isCart)
                return;

            var cartItem = $(this).parents('.cart-item');
            var quantity = $(this).siblings('input[type="number"]').val();
            var cartKey = $(this).siblings('input[type="number"]').attr('data-item-key');
            var min = $(this).siblings('input[type="number"]').attr('min');

            debounceFilterProducts(cartKey,quantity,min,cartItem);
        });
    });

    $('.minus').each(function ()
    {
        $(this).on('click',function ()
        {
            if (!isCart)
                return;

            var cartItem = $(this).parents('.cart-item');
            var quantity = $(this).siblings('input[type="number"]').val();
            var cartKey = $(this).siblings('input[type="number"]').attr('data-item-key');
            var min = $(this).siblings('input[type="number"]').attr('min');

            debounceFilterProducts(cartKey,quantity,min,cartItem);
        });
    });

    $('.cart-quantity input[type="number"]').each(function ()
    {
        $(this).on('input',function ()
        {
            if (!isCart)
                return;

            var cartItem = $(this).parents('.cart-item');
            var quantity = $(this).val();
            var cartKey = $(this).attr('data-item-key');
            var min = $(this).attr('min');

            debounceFilterProducts(cartKey,quantity,min,cartItem);
        });
    });

    function updateCart(cartKey, quantity, min, parentElement)
    {
        if (quantity < min)
        {
            fireMessage('تعداد محصول کمتر از حد مجاز است','error');
            return;
        }

        $.ajax(
        {
            type: "post",
            url: PHPVARS.ajaxurl,
            data: {
                action: 'updateCart',
                cartitemkey: cartKey,
                quantity: quantity
            },
            success: function (response)
            {
                if(response.success)
                {
                    // $('.cart-totals-text').text(response.data.count);

                    fireMessage(response.message,'success');

                    if (response.operation == 'delete')
                        parentElement.remove();

                    $('.total-regular-price-element').text(response.totalRegularPrice);
                    $('.total-discount-price-element').text(response.totalDiscountPrice);
                    $('.total-payable-price-element').text(response.totalPayablePrice);

                    parentElement.find('.item-benefit-price-element').text(response.productDiscountPrice);
                    parentElement.find('.item-payable-price-element').text(response.productPayablePrice);
                }

                else
                {
                    fireMessage(response.message,'error')
                }
            },
            error: function (response)
            {
                fireMessage(response.message,'error')
            }
        });
    }
});