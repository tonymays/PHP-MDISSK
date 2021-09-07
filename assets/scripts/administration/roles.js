/*
 * Javascript library - JRoles
 * manages the Site role operations
 */
function JRoles(Ajax, Core, FormName)
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
            case 'roles_form':
                $('button[name=btnAddRole]').click({obj:obj}, function()
                {
                    var window_name = obj.Core.get_window_name('[Administration] Add Role');
                    obj.Core.suspend_click();
                    if (window_name == '')
                    {
                        obj.Core.launch('/mdissk/administration/roles/add_role', '[Administration] Add Role', 'add_roles_window', '/mdissk/core/help/show_help/administration/roles', '1', '-1', '-1', '-1', '-1', 'No', '-1', '-1', '-1', '-1', '-1');
                    }
                    else
                    {
                        obj.Core.bring_to_top(window_name);
                    }
                    obj.Core.populate_windows();
                });
                $('button[name=btnSaveRoles]').click({obj:obj}, function()
                {
                    var window_name = $(this).parents('div[name^="window-"]').last().attr('name');
                    var data_stream = $('#roles_form').serialize();
                    var result = obj.Ajax.post_form('/mdissk/administration/roles/process_form/roles_form', data_stream, window_name);
                    $('div[name=' + window_name + ']').find('div[name=roles_content]').html(result.responseText);
                    obj.Ajax.copy_form_to_mirror(window_name);
                });
                break;
            case 'add_role_form':
                $('button[name=btnSave]').click({obj:obj}, function()
                {
                    var window_name = $(this).parents('div[name^="window-"]').last().attr('name');
                    var data_stream = $('#add_role_form').serialize();
                    var result = obj.Ajax.post_form('/mdissk/administration/roles/process_form/add_role_form', data_stream, window_name);
                    $('div[name=' + window_name + ']').find('div[name=add_roles_content]').html(result.responseText);
                    window_name = obj.Core.get_window_name('[Administration] Roles');
                    if (window_name != '')
                    {
                        result = obj.Ajax.get_template('/mdissk/administration/roles/construct_roles_content/1');
                        $('div[name=' + window_name + ']').find('div[name=roles_content]').html(result.responseText);
                        obj.Ajax.copy_form_to_mirror(window_name);
                    }
                });
                break;
            case 'edit_role_form':
                $('button[name=btnSave]').click({obj:obj}, function()
                {
                    var window_name = $(this).parents('div[name^="window-"]').last().attr('name');
                    var data_stream = $('div[name=' + window_name + ']').find('#edit_role_form').serialize();
                    var result = obj.Ajax.post_form('/mdissk/administration/roles/process_form/edit_role_form', data_stream, window_name);
                    $('div[name=' + window_name + ']').find('div[name=edit_roles_content]').html(result.responseText);
                    window_name = obj.Core.get_window_name('[Administration] Roles');
                    if (window_name != '')
                    {
                        result = obj.Ajax.get_template('/mdissk/administration/roles/construct_roles_content/1');
                        $('div[name=' + window_name + ']').find('div[name=roles_content]').html(result.responseText);
                        obj.Ajax.copy_form_to_mirror(window_name);
                    }
                });
                break;
        }
    };

    this.edit = function(role_id)
    {
        var edit_role_window_name = this.Core.get_window_name('[Administration] Edit Role [' + role_id + ']');
        this.Core.suspend_click();
        if (edit_role_window_name == '')
        {
            this.Core.launch('/mdissk/administration/roles/edit_role/' + role_id, '[Administration] Edit Role [' + role_id + ']','add_roles_window', '/mdissk/core/help/show_help/administration/roles', '0', '-1', '-1', '-1', '-1', 'No', '-1', '-1', '-1', '-1', '-1');
        }
        else
        {
            this.Core.bring_to_top(edit_role_window_name);
        }
        this.Core.populate_windows();
    };
}