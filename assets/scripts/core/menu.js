/*
 * Javascript library - JMenu
 * manages the Site menu operations
 */
function JMenu()
{
    var item_clicked = false;

    /*
     * initialize
     * initializes the library set
     * @param None
     * @return None
     */
    this.initialize = function()
    {
        this.initialize_listeners(this);
    };

    /*
     * initialize_listeners
     * initializes menu listeners
     * @param object obj - the this pointer
     * @return None
     */
    this.initialize_listeners = function(obj)
    {
        $("#menu li").filter(".menu_header").on('click', function()
        {
            if (item_clicked == false)
            {
                if ($(this).children('img').hasClass('menu_rotate'))
                {
                    $(this).children('img').removeClass('menu_rotate').end().children('ul').slideUp(500);
                }
                else
                {
                    $(this).children('img').addClass('menu_rotate').end().children('ul').slideDown(500);
                }
            }
            else
            {
                $(this).addClass('menu_spotlight');
            }
            item_clicked = false;
        });

        $("#menu li").filter(".menu_item").on('click', function()
        {
            item_clicked = true;
            $(".menu_header").removeClass('menu_spotlight');
            $(".menu_item").find('span').removeClass('menu_spotlight');
            $(this).find("span").addClass('menu_spotlight');
        });
    };
}