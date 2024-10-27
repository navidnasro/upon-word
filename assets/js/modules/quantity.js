/**
 * Auto increment and decrement module
 *
 * @example
 *
 *  <div class="quantity">
 *      <span class="plus">+</span>
 *      <input type="number" class="quantity-input" min="1">
 *      <span class="minus">-</span>
 *  </div>
 *
 */

$(document).ready(function ()
{
    $('.plus').each(function ()
    {
        $(this).on('click',function ()
        {
            var element = $(this).siblings('input');
            var quantity = parseInt(element.val());
            element.val(++quantity);
        });
    });

    $('.minus').each(function ()
    {

        $(this).on('click',function (){

            var element = $(this).siblings('input');
            var quantity = parseInt(element.val());
            var min = parseInt(element.attr('min'));

            if(quantity === min)
                return;

            element.val(--quantity);

        });

    });

});