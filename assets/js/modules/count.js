/**
 * Auto increment and decrement module
 *
 * @example
 * <span class="plus">+</span>
 *      <div class="quantity">
 *          <input type="number" class="quantity-input" min="1">
 *      </div>
 * <span class="minus">-</span>
 */

$(document).ready(function (){

    $('.plus').each(function (){

        $(this).on('click',function (){

            var element = $(this).siblings('.quantity').children('.quantity-input');
            var quantity = parseInt(element.val());
            element.val(++quantity);

        });

    });

    $('.minus').each(function (){

        $(this).on('click',function (){

            var element = $(this).siblings('.quantity').children('.quantity-input');
            var quantity = parseInt(element.val());
            var min = parseInt(element.attr('min'));

            if(quantity === min)
                return;

            element.val(--quantity);

        });

    });

});