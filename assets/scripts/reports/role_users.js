/*
 * Javascript library - JRoleUsers
 * manages the Site Role Users Report
 */
function JRoleUsers(Ajax)
{
    this.obj = this;
    this.Ajax = Ajax;

    this.initialize = function()
    {
        this.configure_listeners(this.obj);
    };

    this.configure_listeners = function(obj)
    {
        $('select[name=roles]').change({obj:obj},function()
        {
            var window_name = $(this).parents('div[name^="window-"]').last().attr('name');
            console.log(window_name);
            var role = $('div[name=' + window_name + ']').find('select[name=roles]>option:selected').text();
            console.log(role);
            var result = obj.Ajax.get_template('/mdissk/reports/role_users/construct_role_users_content/' + role + '/1');
            $('div[name=' + window_name + ']').find('div[name=role_users_content]').html(result.responseText);
        });

        $('button[name=btnRefresh]').click({obj:obj},function()
        {
            var window_name = $(this).parents('div[name^="window-"]').last().attr('name');
            console.log(window_name);
            var role = $('div[name=' + window_name + ']').find('select[name=roles]>option:selected').text();
            console.log(role);
            var result = obj.Ajax.get_template('/mdissk/reports/role_users/construct_role_users_content/' + role + '/1');
            $('div[name=' + window_name + ']').find('div[name=role_users_content]').html(result.responseText);
        });
    };
}