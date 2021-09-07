/*
 * Javascript library - JPermissions
 * manages the Site permission operations
 */
function JPermissions(Ajax)
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
            var role = $('div[name=' + window_name + ']').find('div[name=permissions_content_header]').find('select[name=roles]>option:selected').text();
            var result = obj.Ajax.get_template('/mdissk/administration/permissions/construct_permissions_content/' + role + '/1');
            $('div[name=' + window_name + ']').find('div[name=permissions_content]').html(result.responseText);
            obj.Ajax.copy_form_to_mirror(window_name);
        });

        $('button[name=btnSave]').click({obj:obj},function()
        {
            var window_name = $(this).parents('div[name^="window-"]').last().attr('name');
            var data_stream = $('div[name=' + window_name + ']').find('#permissions_form').serialize();
            var result = obj.Ajax.post_form('/mdissk/administration/permissions/process_form', data_stream, window_name);
            $('div[name=' + window_name + ']').find('div[name=permissions_content]').html(result.responseText);
            obj.Ajax.copy_form_to_mirror(window_name);
        });
    };
}