/*
 * Javascript library - JCore
 * manages the Site Core operations
 */
function JCore(Ajax)
{
    this.Ajax = Ajax;
    this.next_window = 1000;
    this.obj = this;
    this.suspend_click_event = false;

    this.initialize = function()
    {
    };

    /*===============================================================================================================
      Core Window Handler methods
      ===============================================================================================================*/
    this.launch = function(url, title, css_class, help_url, single_instance, original_top, original_left, original_width, original_height, maximized, actual_top, actual_left, actual_width, actual_height, minimized)
    {
        var result = this.Ajax.get_template(url);
        this.create(result.responseText, url, title, css_class, help_url, single_instance, original_top, original_left, original_width, original_height, maximized, actual_top, actual_left, actual_width, actual_height, minimized);
    };

    this.create = function(content, url, title, css_class, help_url, single_instance, original_top, original_left, original_width, original_height, original_maximized, actual_top, actual_left, actual_width, actual_height, minimized)
    {
        var window_name = '';
        if (single_instance == 1)
        {
            window_name = this.get_window_name(title);
        }
        if (window_name == '')
        {
            var window_name = 'window-' + this.next_window;
            var window_name_content = window_name + '-Content';
            if (minimized == '1')
            {
                var window = '<div name="' + window_name + '" class="ui-widget-content window hide ' + css_class + '">';
            }
            else
            {
                var window = '<div name="' + window_name + '" class="ui-widget-content window ' + css_class + '">';
            }
            window += '<div name="' + window_name + '-TitleBar" class="window_title_bar get_h">';
            window += '<h3 class="window_header left">' + title + '</h3>';
            window += '<span name="close_window_button" title="Close" class="window_button right"><img src="/mdissk/assets/images/circle_remove.png" height="16" width="16"></span>';
            window += '<span name="max_window_button" title="Maximize" class="window_button right"><img src="/mdissk/assets/images/circle_plus.png" height="16" width="16"></span>';
            window += '<span name="min_window_button" title="Minimize" class="window_button right"><img src="/mdissk/assets/images/circle_minus.png" height="16" width="16"></span>';
            if (css_class != 'help_window')
            {
                window += '<span name="help_window_button" title="Help" class="window_button right"><img src="/mdissk/assets/images/circle_question_mark.png" height="16" width="16"></span>';
                window += '<span name="favorites_window_button" title="Add to Favorites" class="window_button right"><img src="/mdissk/assets/images/circle_exclamation_mark.png" height="16" width="16"></span>';
            }
            window += '</div>';
            window += '<div style="clear:both"></div>';
            window += '<div name="' + window_name_content + '" class="windows_content"></div>';
            window += '</div>';
            $('div[name=site_contents]').append(window);
            if (original_left == '-1')
            {
                var left = $('div[name=' + window_name + ']').css('left').replace('px','');
                var top = $('div[name=' + window_name + ']').css('top').replace('px','');
                var width = $('div[name=' + window_name + ']').css('width').replace('px','');
                var height = $('div[name=' + window_name + ']').css('height').replace('px','');
                var maximized = 'No';
            }
            else
            {
                var left = original_left;
                var top = original_top;
                var width = original_width;
                var height = original_height;
                if (original_maximized == '0')
                {
                    var maximized = 'No';
                }
                else
                {
                    var maximized = 'Yes';
                }
                $("div[name=" + window_name + "]").css
                (
                    {
                        top: actual_top + 'px',
                        left: actual_left + 'px',
                        height: actual_height + 'px',
                        width: actual_width + 'px'
                    }
                );
            }
            // added for feature set benefit
            content = content + '<div name="window-info-div" class="hide">';
            content = content + '<p name="' + window_name + '-url">' + url + '</p>';
            content = content + '<p name="' + window_name + '-title">' + title + '</p>';
            content = content + '<p name="' + window_name + '-css_class">' + css_class + '</p>';
            content = content + '<p name="' + window_name + '-help_url">' + help_url + '</p>';
            content = content + '<p name="' + window_name + '-single_instance">' + single_instance + '</p>';
            content = content + '<p name="' + window_name + '-top">' + top + '</p>';
            content = content + '<p name="' + window_name + '-left">' + left + '</p>';
            content = content + '<p name="' + window_name + '-width">' + width + '</p>';
            content = content + '<p name="' + window_name + '-height">' + height + '</p>';
            content = content + '<p name="' + window_name + '-maximize">' + maximized + '</p>';
            content = content + '</div>';
            $('div[name=' + window_name_content + ']').html(content);

            if (this.has_audit_tag(window_name))
            {
                var div_name = $('div[name=' + window_name + ']').find('.audit_page').attr('name');
                var result = $('div[name=' + window_name + ']').find('div[name=' + div_name + ']').html();
                var audit = '<div name="' + window_name_content + '-Mirror" class="hide">';
                audit += '<form name="' + window_name_content + '-Form-Mirror">';
                audit += result;
                audit += '</form>';
                audit += '</div>';
                $('div[name=' + window_name_content + ']').append(audit);
            }

            this.init_event_handlers(this.obj, url, window_name, title, css_class, help_url, single_instance);
            if (minimized != 1)
            {
                this.bring_to_top(window_name);
            }
            this.next_window++;
        }
        else
        {
            this.show(window_name);
        }
    };

    this.has_audit_tag = function(window_name)
    {
        var audit_tag = $('div[name=' + window_name + ']').find('.audit_page').attr('name');
        return (audit_tag != undefined);
    };

    this.format_window = function(window_name)
    {
        // set height
        var window_height = parseInt($('div[name=' + window_name + ']').css('height').replace('px',''));
        var get_h_div_height = 0;
        $('div[name=' + window_name + ']').find('.get_h').each(function()
        {
            var height = parseInt($(this).css('height').replace('px',''));
            get_h_div_height += height;
        });
        var delta = window_height - get_h_div_height - 32;
        $('div[name=' + window_name + ']').find('.set_h').css('max-height', delta + 'px');


        var div_name = undefined;
        var get_width = 0;
        var get_sibling_width = 0;
        var set_width = 0;
        var new_width = 0;

        $('div[name=' + window_name + ']').find('.get_w').each(function()
        {
            div_name = $(this).attr('name');
            get_width = parseInt($('div[name='+div_name+']').css('width').replace('px', ''));
        });

        $('div[name=' + window_name + ']').find('.get_sibling_w').each(function()
        {
            div_name = $(this).attr('name');
            get_sibling_width = parseInt($('div[name='+div_name+']').css('width').replace('px', ''));
        });

        $('div[name=' + window_name + ']').find('.set_w').each(function()
        {
            div_name = $(this).attr('name');
            set_width = parseInt($('div[name='+div_name+']').css('width').replace('px', ''));
            delta = get_width - get_sibling_width - set_width;
            new_width = set_width + delta - 10;
            $('div[name=' + div_name + ']').css('width', new_width + 'px');
        });
    };

    this.init_event_handlers = function(obj, url, window_name, title, css_class, help_url, single_instance)
    {
        $('div[name='+window_name+']').resizable(
            {handles: "n,e,s,w,ne,se,sw,nw"},
            {autoHide: true},
            {obj:obj},
            {containment:"#site_contents"},
            {start: function(event, ui)
            {
                if (obj.exists(window_name))
                {
                    if ($(this).css('z-index')=='auto')
                    {
                        obj.bring_to_top(window_name);
                    }
                }
            },
            resize: function(event,ui)
            {
                obj.format_window(window_name);
            },
            stop: function(event, ui)
            {
                var height = $('div[name=' + window_name + ']').css('height').replace('px', '');
                var width = $('div[name=' + window_name + ']').css('width').replace('px', '');
                $('p[name=' + window_name + '-width]').text(width);
                $('p[name=' + window_name + '-height]').text(height);
                $('p[name=' + window_name + '-maximize]').text('No');
                obj.format_window(window_name);
            }}
        ).draggable(
            {obj:obj},
            {containment:"#site_contents"},
            {handle: 'div[name=' + window_name + '-TitleBar]'},
            {start: function(event, ui)
            {
                if (obj.exists(window_name))
                {
                    if ($(this).css('z-index')=='auto')
                    {
                        obj.bring_to_top(window_name);
                    }
                }
            },
            stop: function(event, ui)
            {
                var top = $('div[name=' + window_name + ']').css('top').replace('px', '');
                var left = $('div[name=' + window_name + ']').css('left').replace('px', '');
                $('p[name=' + window_name + '-top]').text(top);
                $('p[name=' + window_name + '-left]').text(left);
            }}
        );

        $('div[name=' + window_name + ']').click({obj:obj}, function(e)
        {
            if (obj.suspend_click_event)
            {
                obj.suspend_click_event = false;
            }
            else
            {
                if (obj.exists(window_name))
                {
                    obj.bring_to_top(window_name);
                }
            }
        });

        $('div[name=' + window_name + ']').find('span[name=min_window_button]').click({obj:obj}, function(e)
        {
            if (obj.exists(window_name))
            {
                $('div[name='+window_name+']').addClass('hide');
                obj.populate_windows();
            }
        });

        $('div[name=' + window_name + ']').find('span[name=max_window_button]').click({obj:obj}, function(e)
        {
            obj.maximize_window(window_name);
        });

        $('div[name=' + window_name + ']').find('span[name=close_window_button]').click({obj:obj}, function(e)
        {
            obj.close_window(window_name);
        });

        if (help_url != '')
        {
            $('div[name=' + window_name + ']').find('span[name=help_window_button]').click({obj:obj}, function(e)
            {
                if (obj.exists(window_name))
                {
                    obj.suspend_click_event = true;
                    var result = obj.Ajax.get_template(help_url);
                    var help_title = 'Help ' + title;
                    var help_window_name = obj.get_window_name(help_title);
                    if (help_window_name == '')
                    {
                        obj.create(result.responseText, help_url, help_title, 'help_window', '', 1, -1, -1, -1, -1, 'No', -1, -1, -1, -1, -1);
                    }
                    else
                    {
                        obj.show(help_window_name);
                    }
                }
            });
        }

        $('div[name=' + window_name + ']').find('span[name=favorites_window_button]').click({obj:obj}, function(e)
        {
            obj.add_favorite(url, title, css_class, help_url, single_instance);
        });
    };

    this.maximize_window = function(window_name)
    {
        if (this.exists(window_name))
        {
            if ($('div[name=' + window_name + ']').find('p[name=' + window_name + '-maximize]').text() == 'No')
            {
                var window_left = $('div[name=site_contents]').css("left");
                var window_top = $('div[name=site_contents]').css("top");
                var window_height = $('div[name=site_contents]').css("height");
                var window_width = $('div[name=site_contents]').css("width").replace('px', '');
                var adjusted_width = window_width - 2;
                $('div[name='+window_name+']').css
                (
                    {
                        left: window_left,
                        top: window_top,
                        width: adjusted_width + 'px',
                        height: window_height
                    }
                );
                $('div[name=' + window_name + ']').find('p[name=' + window_name + '-maximize]').text('Yes');
            }
            else
            {
                var window_left = $('div[name=' + window_name + ']').find('p[name=' + window_name + '-left]').text();
                var window_top = $('div[name=' + window_name + ']').find('p[name=' + window_name + '-top]').text();
                var window_height = $('div[name=' + window_name + ']').find('p[name=' + window_name + '-height]').text();
                var window_width = $('div[name=' + window_name + ']').find('p[name=' + window_name + '-width]').text();
                $('div[name='+window_name+']').css
                (
                    {
                        left: window_left + 'px',
                        top: window_top + 'px',
                        width: window_width + 'px',
                        height: window_height + 'px'
                    }
                );
                $('div[name=' + window_name + ']').find('p[name=' + window_name + '-maximize]').text('No');
            }
        }
        this.format_window(window_name);
    };

    this.close_window = function(window_name)
    {
        if (this.exists(window_name))
        {
            $('div[name='+window_name+']').unbind('click');
            $('div[name=' + window_name + ']').find('span[name=min_window_button]').unbind('click');
            $('div[name=' + window_name + ']').find('span[name=max_window_button]').unbind('click');
            $('div[name=' + window_name + ']').find('span[name=close_window_button]').unbind('click');
            $('div[name=' + window_name + ']').find('span[name=help_window_button]').unbind('click');
            $('div[name='+window_name+']').remove();
            this.populate_windows();
        }
    };

    this.exists = function(window_name)
    {
        var result = false;
        if ($('div[name='+window_name+']').length > 0)
        {
            result = true;
        }
        return result;
    };

    this.bring_to_top = function(window_name)
    {
        $(".ui-widget-content").each(function(e)
        {
            $(this).css('z-index','auto');
            $(this).find('.window_header').css('color', 'darkgray');
        });
        $('div[name=' + window_name + ']').css('z-index',8000);
        $('div[name=' + window_name + ']').find('.window_header').css('color', 'whitesmoke');
    };

    this.get_window_name = function(title)
    {
        var window_name = '';
        $('.window_header').each(function()
        {
            if ($(this).text() == title)
            {
                window_name = $(this).parent().attr('name');
                window_name = window_name.replace('-TitleBar','');
            }
        });
        return window_name;
    };

    this.show = function(window_name)
    {
        $('div[name=' + window_name + ']').removeClass('hide');
        this.format_window(window_name);
        this.bring_to_top(window_name);
        this.populate_windows();
    };

    this.minimize_windows = function()
    {
        var window_name = '';
        var obj = this.obj;
        $('div[name=site_contents]').find('.window_title_bar').each(function()
        {
            window_name = $(this).attr('name').replace('-TitleBar', '');
            $('div[name=' + window_name + ']').addClass('hide');
        });
        this.populate_windows();
    };

    this.close_windows = function()
    {
        var window_name = '';
        var obj = this.obj;
        $('div[name=site_contents]').find('.window_title_bar').each(function()
        {
            window_name = $(this).attr('name').replace('-TitleBar', '');
            obj.close_window(window_name);
        });
        this.populate_windows();
    };

    this.display_windows = function()
    {
        var window_name = '';
        var obj = this.obj;
        $('div[name=site_contents]').find('.window_title_bar').each(function()
        {
            window_name = $(this).attr('name').replace('-TitleBar', '');
            $('div[name=' + window_name + ']').removeClass('hide');
        });
        this.populate_windows();
    };

    this.has_desktop = function()
    {
        var request = "/mdissk/core/main/has_desktop";
        var result = this.Ajax.get_template(request);
        return (result.responseText == 'DESKTOP');
    };

    this.load_desktop = function()
    {
        if (this.has_desktop())
        {
            $('div[name=desktop-message-center]').html('Loading Desktop ... please wait...');
            $('div[name=desktop-message-center]').removeClass('hide');
            this.close_windows();
            var request = "/mdissk/core/main/load_desktop";
            var result = this.Ajax.get_template(request);
            $('div[name=site_contents]').append(result.responseText);
            var mods = new Array();
            $('div[name=desktop-request]').find('div').each(function()
            {
                $(this).find('p').each(function()
                {
                    mods.push($(this).text());
                })
            });
            var len_a = mods.length;
            var len_b = len_a / 18;
            var window_name = '';
            var top_window_name = ''
            for(index = 1; index <= len_b; index++)
            {
                var upper = (18 * index) - 1;
                var lower = upper - 17;
                this.launch(mods[lower+2], mods[lower+3], mods[lower+5], mods[lower+4], mods[lower+6], mods[lower+7], mods[lower+8], mods[lower+9], mods[lower+10], mods[lower+11], mods[lower+12], mods[lower+13], mods[lower+14], mods[lower+15], mods[lower+17]);
                window_name = this.get_window_name(mods[lower+3]);
                // format if the window is not minimized ... the show routine will also format
                if (mods[lower+17] != 1)
                {
                    this.format_window(window_name);
                }
                // set top window
                if (mods[lower+16] != 'auto')
                {
                    top_window_name = this.get_window_name(mods[lower+3]);
                }
            }
            if (top_window_name != '')
            {
                this.bring_to_top(top_window_name);
            }
            this.populate_windows();
            $('div[name=desktop-request]').remove();
            $('div[name=desktop-message-center]').html('Desktop successfully loaded...');
            $('div[name=desktop-message-center]').slideDown(500).delay(5000).slideUp(500);
        }
        else
        {
            $('div[name=desktop-message-center]').html('No Desktop Saved');
            $('div[name=desktop-message-center]').removeClass('hide');
            $('div[name=desktop-message-center]').slideDown(500).delay(5000).slideUp(500);
        }
    };

    this.save_desktop = function()
    {
        $('div[name=desktop-message-center]').html('Saving Desktop ... please wait...');
        $('div[name=desktop-message-center]').removeClass('hide');
        var xml = '<?xml version="1.0"?>\n';
        xml += '<request>\n';
        $('div[name=site_contents]').find($('div[name=window-info-div]')).each(function()
        {
            var window_name = ($(this).parent().attr('name').replace('-Content',''));
            xml += '<window>\n';
            xml += '<url>' + $('p[name=' + window_name + '-url]').text() + '</url>\n';
            xml += '<title>' + $('p[name=' + window_name + '-title]').text() + '</title>\n';
            xml += '<css_class>' + $('p[name=' + window_name + '-css_class]').text() + '</css_class>\n';
            if ($('p[name=' + window_name + '-help_url]').text().trim() == '')
            {
                xml += '<help_url>NODATA</help_url>\n';
            }
            else
            {
                xml += '<help_url>' + $('p[name=' + window_name + '-help_url]').text() + '</help_url>\n';
            }
            xml += '<single_instance>' + $('p[name=' + window_name + '-single_instance]').text() + '</single_instance>\n';
            xml += '<original_top>' + $('p[name=' + window_name + '-top]').text() + '</original_top>\n';
            xml += '<original_left>' + $('p[name=' + window_name + '-left]').text() + '</original_left>\n';
            xml += '<original_width>' + $('p[name=' + window_name + '-width]').text() + '</original_width>\n';
            xml += '<original_height>' + $('p[name=' + window_name + '-height]').text() + '</original_height>\n';
            xml += '<actual_left>' + $('div[name=' + window_name + ']').css('left').replace('px','') + '</actual_left>\n';
            xml += '<actual_top>' + $('div[name=' + window_name + ']').css('top').replace('px','') + '</actual_top>\n';
            xml += '<actual_width>' + $('div[name=' + window_name + ']').css('width').replace('px','') + '</actual_width>\n';
            xml += '<actual_height>' + $('div[name=' + window_name + ']').css('height').replace('px','') + '</actual_height>\n';
            xml += '<maximized>' + $('p[name=' + window_name + '-maximize]').text() + '</maximized>\n';
            xml += '<z_index>' + $('div[name=' + window_name + ']').css('z-index') + '</z_index>\n';
            if ($('div[name=' + window_name + ']').hasClass('hide'))
            {
                xml += '<minimized>1</minimized>\n';
            }
            else
            {
                xml += '<minimized>0</minimized>\n';
            }
            xml += '</window>\n';
        });
        xml += '</request>';
        this.Ajax.post_xml('/mdissk/core/main/save_desktop', xml);
        $('div[name=desktop-message-center]').html('Desktop successfully saved...');
        $('div[name=desktop-message-center]').slideDown(500).delay(5000).slideUp(500);
    };

    this.suspend_click = function()
    {
        this.suspend_click_event = true;
    };

    /*===============================================================================================================
     Core Sidebar Handler methods
     ===============================================================================================================*/
    this.is_side_bar_contents_visible = function()
    {
        return $('div[name=side_bar_contents]').is(":visible");
    };

    this.show_side_bar_contents = function()
    {
        var window_width = $(window).outerWidth();
        var side_bar_contents_width = $("div[name=side_bar_contents]").outerWidth();
        var side_bar_width = $("div[name=side_bar]").outerWidth();
        var body_width = window_width - side_bar_contents_width - side_bar_width - 4;
        $("div[name=site_contents]").css
        (
            {
                width: body_width  + 'px'
            }
        );
        $('div[name=side_bar_contents]').show();
    };

    this.hide_side_bar_contents = function()
    {
        $('div[name=side_bar_contents]').hide();
        var window_width = $(window).outerWidth();
        var side_bar_contents_width = 0;
        var side_bar_width = $("div[name=side_bar]").outerWidth();
        var body_width = window_width - side_bar_contents_width - side_bar_width - 4;
        $("div[name=site_contents]").css
        (
            {
                width: body_width + 'px'
            }
        );
    };

    this.toggle_side_bar = function()
    {
        if (this.is_side_bar_contents_visible())
        {
            this.hide_side_bar_contents();
        }
        else
        {
            this.show_side_bar_contents();
        }
    };

    this.show_side_bar_section = function(side_bar_section)
    {
        switch(side_bar_section)
        {
            case 'main_menu':
                this.show_side_bar_contents();
                $('div[name=side_bar_main_menu]').show();
                $('div[name=side_bar_windows_manager]').hide();
                $('div[name=side_bar_help_menu]').hide();
                $('div[name=side_bar_favorites]').hide();
                break;
            case 'window_manager':
                this.populate_windows();
                this.show_side_bar_contents();
                $('div[name=side_bar_windows_manager]').show();
                $('div[name=side_bar_main_menu]').hide();
                $('div[name=side_bar_help_menu]').hide();
                $('div[name=side_bar_favorites]').hide();
                break;
            case 'favorites':
                var request = '/mdissk/core/main/show_favorites';
                var result = this.Ajax.get_template(request);
                $('div[name=side_bar_favorite_contents]').html(result.responseText);
                this.show_side_bar_contents();
                $('div[name=side_bar_favorites]').show();
                $('div[name=side_bar_main_menu]').hide();
                $('div[name=side_bar_windows_manager]').hide();
                $('div[name=side_bar_help_menu]').hide();
                break;
            case 'help_menu':
                this.show_side_bar_contents();
                $('div[name=side_bar_help_menu]').show();
                $('div[name=side_bar_main_menu]').hide();
                $('div[name=side_bar_windows_manager]').hide();
                $('div[name=side_bar_favorites]').hide();
                break;
            case 'toggle_side_bar':
                if (this.is_side_bar_contents_visible())
                {
                    this.hide_side_bar_contents();
                }
                else
                {
                    this.show_side_bar_contents();
                }
                break;
        }
    };

    /*===============================================================================================================
     Core Sidebar Section methods
     ===============================================================================================================*/
    /* ------------------------------------------------ menus ------------------------------------------------------*/
    this.expand_menu = function(menu)
    {
        switch(menu)
        {
            case 'main':
                $('div[name=side_bar_main_menu] ul').each(function()
                {
                    $(this).slideDown();
                    $(this).find('img').addClass('menu_rotate');
                });
                break;
            case 'help':
                $('div[name=side_bar_help_menu] ul').each(function()
                {
                    $(this).slideDown();
                    $(this).find('img').addClass('menu_rotate');
                });
                break;
        }
    };

    this.collapse_menu = function(menu)
    {
        switch(menu)
        {
            case 'main':
                $('div[name=side_bar_main_menu] ul li').each(function()
                {
                    $(this).children('ul').slideUp();
                    $(this).find('img').removeClass('menu_rotate');
                });
                break;
            case 'help':
                $('div[name=side_bar_help_menu] ul li').each(function()
                {
                    $(this).children('ul').slideUp();
                    $(this).find('img').removeClass('menu_rotate');
                });
                break;
        }
    };

    /* -------------------------------------------- window manager -------------------------------------------------*/
    this.populate_windows = function()
    {
        var window_app_name;
        var window_id;
        $('div[name=side_bar_contents]').find('div[name=side_bar_windows_manager]').find('div[name=windows_content]').remove();
        var html ='<div name="windows_content"></div>';
        $('div[name=side_bar_contents]').find('div[name=side_bar_windows_manager]').append(html);
        $('div[name=site_contents] div').find('.window_header').each(function()
        {
            window_app_name = $(this).html();
            window_id = $(this).parent().attr('name').replace('-TitleBar','');

            if ($('div[name=' + window_id + ']').hasClass('hide'))
            {
                html = '<a href=javascript:Core.show("' + window_id + '")>' + window_app_name + ' <span class="window_helper">(minimized)</span></a><a title="Close Window" href=javascript:Core.close_window("' + window_id + '")> <span><img class="side_bar_section_img" src="/mdissk/assets/images/circle_remove.png" </span></a><br>';
            }
            else
            {
                html = '<a href=javascript:Core.show("' + window_id + '")>' + window_app_name + ' <span class="window_helper">(opened)</span></a><a title="Close Window" href=javascript:Core.close_window("' + window_id + '")> <span><img class="side_bar_section_img" src="/mdissk/assets/images/circle_remove.png" </span></a><br>';
            }
            $('div[name=side_bar_contents]').find('div[name=side_bar_windows_manager]').find('div[name=windows_content]').append(html);
        });
    };

    /* ---------------------------------------------- favorites ----------------------------------------------------*/
    this.load_favorites = function()
    {
        var mods = new Array();
        $("div[name=side_bar_favorite_contents] ul li a").each(function()
        {
            var href = $(this).attr('href');
            if (href.indexOf('javascript:Core.launch(') > -1)
            {
                var thunder = href.replace('javascript:Core.launch(', '').replace(')', '');
                mods.push(thunder);
            }
        });
        var len = mods.length;
        if (mods.length > 0)
        {
            for (index = 0; index < len; index++)
            {
                var module_element = mods[index];
                module_element.replace('javascript:Core.launch(', '').replace(')', '');
                var params = module_element.split(',');
                var url = params[0].replace(/\'/g, '').replace(/ /g, '');
                var title = params[1].replace(/\'/g, '').replace(/ /, '');
                var css_class = params[2].replace(/\'/g, '').replace(/ /g, '');
                var help_url = params[3].replace(/\'/g, '').replace(/ /g, '');
                var single_instance = params[4].replace(/\'/g, '').replace(/ /g, '');
                this.launch(url, title, css_class, help_url, single_instance)
            }
        }
    };

    this.add_favorite = function(url, title, css_class, help_url, single_instance)
    {
        url = url.replace(/\//g, 'FORWARDSLASH');
        title = title.replace(/\[/g, 'OPENBRACKET');
        title = title.replace(/\]/g, 'CLOSEBRACKET');
        title = title.replace(/ /g, 'SPACE');
        help_url = help_url.replace(/\//g, 'FORWARDSLASH');
        var request = '/mdissk/core/main/add_favorite/' + url + '/' + title + '/' + css_class + '/' + help_url + '/' + single_instance;
        var result = this.Ajax.get_template(request);
        if (result.responseText != 'DUPLICATE')
        {
            $('div[name=side_bar_favorite_contents]').html(result.responseText);
        }
    };

    this.delete_favorite = function(favorite_id)
    {
        var request = '/mdissk/core/main/delete_favorite/' + favorite_id;
        var result = this.Ajax.get_template(request);
        $('div[name=side_bar_favorite_contents]').html(result.responseText);
    };

    this.delete_favorites = function()
    {
        var request = '/mdissk/core/main/delete_favorites';
        var result = this.Ajax.get_template(request);
        $('div[name=side_bar_favorite_contents]').html(result.responseText);
    };
}