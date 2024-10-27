/**
 * Tabs
 *
 * @instructions
 * 1.the tab items wrapper must have "tabs" class
 * 2.each tab item must have "tab-item" class
 * 3.each tab item must have "data-panel" attribute which links it to its panel
 * 4.the active tab item must have "active" class
 * 5.tabs content wrapper must have "tabs-content" class
 * 6.each tab content panel must have "tab-panel" class
 * 7.each tab content panel must have id with same value as "data-panel" attribute of its tab
 */

$(document).ready(function (){

    $('.tab-item').on('click',function (e){

        //if is active return
        if ($(this).hasClass('active'))
            return;

        $(this).siblings('.active').removeClass('active');
        $(this).addClass('active');

        //panel content element
        var panelID = $(this).attr('data-panel');
        var panelElement = $(this).parent().siblings('.tabs-content').children(panelID);

        //deactivate and hide
        panelElement.siblings('.active').addClass('hidden');
        panelElement.siblings('.active').removeClass('active');

        //active and show
        panelElement.addClass('active');
        panelElement.removeClass('hidden');


    });

});
