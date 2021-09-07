/*
 * Javascript library - JUsers
 * manages the Site user operations
 */
function JUsers(Ajax, Core, FormName)
{
    this.obj = this;
    this.Ajax = Ajax;
    this.Core = Core;
    this.FormName = FormName;
    this.initialize = function()
    {
        this.configure_listeners(this.obj);
    };

    this.configure_listeners = function(obj)
    {
        switch (obj.FormName)
        {
            case 'users_form':
                $('button[name=btnAddUser]').click({obj:obj}, function()
                {
                    var window_name = obj.Core.get_window_name('[Administration] Add User');
                    obj.Core.suspend_click();
                    if (window_name == '')
                    {
                        obj.Core.launch('/mdissk/administration/users/add_user', '[Administration] Add User', 'add_users_window', '/mdissk/core/help/show_help/administration/users', 0);
                    }
                    else
                    {
                        obj.Core.bring_to_top(window_name);
                    }
                    obj.Core.populate_windows();
                });
                $('button[name=btnSaveUsers]').click({obj:obj}, function()
                {
                    var window_name = $(this).parents('div[name^="window-"]').last().attr('name');
                    var data_stream = $('#users_form').serialize();
                    var result = obj.Ajax.post_form('/mdissk/administration/users/process_form/users_form', data_stream, window_name);
                    $('div[name=' + window_name + ']').find('div[name=users_content]').html(result.responseText);
                    obj.Ajax.copy_form_to_mirror(window_name);
                });
                break;
            case 'add_user_form':
                $('button[name=btnSave]').click({obj:obj}, function()
                {
                    var window_name = $(this).parents('div[name^="window-"]').last().attr('name');
                    var data_stream = $('#add_user_form').serialize();
                    var result = obj.Ajax.post_form('/mdissk/administration/users/process_form/add_user_form', data_stream, window_name);
                    $('div[name=' + window_name + ']').find('div[name=add_user_content]').html(result.responseText);
                    window_name = obj.Core.get_window_name('[Administration] Users');
                    if (window_name != '')
                    {
                        result = obj.Ajax.get_template('/mdissk/administration/users/construct_users_content/1');
                        $('div[name=' + window_name + ']').find('div[name=users_content]').html(result.responseText);
                        obj.Ajax.copy_form_to_mirror(window_name);
                    }
                });
                break;
            case 'edit_user_form':
                $('button[name=btnSave]').click({obj:obj}, function()
                {
                    var window_name = $(this).parents('div[name^="window-"]').last().attr('name');
                    var data_stream = $('div[name=' + window_name + ']').find('#edit_user_form').serialize();
                    var result = obj.Ajax.post_form('/mdissk/administration/users/process_form/edit_user_form', data_stream, window_name);
                    $('div[name=' + window_name + ']').find('div[name=edit_user_content]').html(result.responseText);
                    window_name = obj.Core.get_window_name('[Administration] Users');
                    if (window_name != '')
                    {
                        result = obj.Ajax.get_template('/mdissk/administration/users/construct_users_content/1');
                        $('div[name=' + window_name + ']').find('div[name=users_content]').html(result.responseText);
                        obj.Ajax.copy_form_to_mirror(window_name);
                    }
                });
                break;
        }
    };

    this.edit = function(user_id)
    {
        var window_name = this.Core.get_window_name('[Administration] Edit User [' + user_id + ']');
        this.Core.suspend_click();
        if (window_name == '')
        {
            this.Core.launch('/mdissk/administration/users/edit_user/' + user_id, '[Administration] Edit User [' + user_id + ']','add_users_window', '/mdissk/core/help/show_help/administration/users', 0);
        }
        else
        {
            this.Core.bring_to_top(window_name);
        }
        this.Core.populate_windows();
    };
}