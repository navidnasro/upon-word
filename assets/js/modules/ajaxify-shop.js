$(document).ready(function ()
{
    /**
     * Ajaxifying shop page
     */

    const pageId = $('#product-tax-status').attr('data-term-id');
    const pageTaxonomy = $('#product-tax-status').attr('data-taxonomy');

    var inStock = false;
    var filters = [];
    var search = '';
    var orderBy = 'date';
    var paged = 1;
    var minPrice = 0;
    var maxPrice = 999999999999;
    var allowedAjax = false;

    const debounceFilterProducts = debounce(filterProducts, 1000);

    function debounce(func, wait)
    {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function filterProducts()
    {
        var formData = new FormData();
        formData.append('action','filterProducts');
        formData.append('orderby',orderBy);
        formData.append('instock',inStock);
        formData.append('search',search);
        formData.append('paged',paged);
        formData.append('minprice',minPrice);
        formData.append('maxprice',maxPrice);

        if (pageId != 0)
        {
            // then it is an archive page

            formData.append('pageid',pageId);
            formData.append('pagetaxonomy',pageTaxonomy);
        }

        for (var key in filters)
        {
            formData.append(key,filters[key]);
        }

        $.ajax(
            {
                type: 'POST',
                url: PHPVARS.ajaxurl,
                data: formData,
                processData: false, // Still needed to prevent processing
                contentType: false, // Still needed for correct content type

                success: function (response)
                {
                    $('#nothing-found-message').remove();

                    if (response.success)
                    {
                        /**
                         * response.count => cart count
                         * response.content => matched products html
                         * response.pagination => pagination html
                         */

                        $('#archive-products-container').empty();
                        $('.woocommerce-pagination > div.pagination-numbers').empty();
                        $('#product-tax-status span').text('('+response.count+')');
                        $('.woocommerce-pagination > div.pagination-numbers').append(response.pagination);
                        $('#archive-products-container').append(response.content);
                    }

                    else
                    {
                        /**
                         * response.message => returned message by server
                         */

                        fireMessage(response.message,'error');
                    }
                },
            }
        );
    }

    $('.price_slider_amount').on('input','input',function ()
    {
        minPrice = $('#min_price').val();
        maxPrice = $('#max_price').val();

        debounceFilterProducts(); // one second delay then sends ajax
    });

    $(document).on('price_slider_updated',function ()
    {
        minPrice = $('#min_price').val();
        maxPrice = $('#max_price').val();

        if (allowedAjax)
        {
            debounceFilterProducts(); // one second delay then sends ajax
        }

        else
        {
            allowedAjax = true;
        }
    });

    $('#order-by-search input').on('input',function()
    {
        search = $(this).val();
        debounceFilterProducts();
    });

    $('.woocommerce-pagination').on('click','ul.page-numbers .page-numbers',function (e)
    {
        e.preventDefault();

        if (isNaN($(this).text()))
        {
            if ($(this).hasClass('prev'))
            {
                paged = parseInt(paged) - 1;
            }

            else if ($(this).hasClass('next'))
            {
                paged = parseInt(paged) + 1;
            }
        }

        else
        {
            paged = $(this).text();
        }

        filterProducts();
    });

    //catalog filtering (product ordering options)
    $('#order-by-options .option').on('click',function(e)
    {
        e.preventDefault();

        if(!$(this).hasClass('active'))
        {
            orderBy = $(this).val();

            $(this).siblings('.active').removeClass('active');
            $(this).addClass('active');

            debounceFilterProducts();
        }
    });

    $('.search-items .search-item').each(function ()
    {
        $(this).on('click',function (){

            $(this).find('.search-item-checkbox').toggleClass('bg-[var(--theme-secondary)]');

            var termId = $(this).attr('data-termId');
            var taxonomy = $(this).attr('data-taxonomy');

            if (filters.hasOwnProperty(taxonomy))
            {
                if (filters[taxonomy].includes(termId))
                {
                    var index = filters[taxonomy].indexOf(termId);
                    filters[taxonomy].splice(index,1);
                }

                else
                {
                    filters[taxonomy].push(termId);
                }
            }

            else
            {
                filters[taxonomy] = [termId];
            }

            filterProducts();
        });
    });
});