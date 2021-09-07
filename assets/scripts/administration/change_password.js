/*
 * Javascript library - JUsers
 * manages the Site user operations
 */
function JChangePassword(Ajax)
{
    this.obj = this;
    this.Ajax = Ajax;
    this.initialize = function()
    {
        this.configure_listeners(this.obj);
    };

    this.configure_listeners = function(obj)
    {
        $('button[name=btnSavePasswordChange]').click({obj:obj}, function()
        {
            var window_name = $(this).parents('div[name^="window-"]').last().attr('name');
            var data_stream = $('#change_password_form').serialize();
            var result = obj.Ajax.post_form('/mdissk/administration/change_password/process_form', data_stream, window_name);
            $('div[name=' + window_name + ']').find('div[name=change_password_content]').html(result.responseText);
        });
    };
}