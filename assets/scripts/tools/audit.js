/*
 * Javascript library - JAudit
 * manages the Site Audit Report
 */
function JAudit(Ajax, Core, FormName)
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
        $('button[name=btnSearch]').click({obj:obj},function()
        {
            var window_name = $(this).parents('div[name^="window-"]').last().attr('name');
            var user_name = $('div[name=' + window_name + ']').find('select[name=users]>option:selected').text();
            var module = $('div[name=' + window_name + ']').find('select[name=modules]>option:selected').text();
            var start_date = $('div[name=' + window_name + ']').find('#start_date').val();
            var end_date = $('div[name=' + window_name + ']').find('#end_date').val();
            if (start_date == '')
            {
                $('div[name=' + window_name + ']').find('#start_date').val('All');
                start_date = $('div[name=' + window_name + ']').find('#start_date').val();
            }
            if (end_date == '')
            {
                $('div[name=' + window_name + ']').find('#end_date').val('All');
                start_date = $('div[name=' + window_name + ']').find('#end_date').val();
            }
            module = module.replace(/\[/g, '__FORWARDBRACE__');
            module = module.replace(/\]/g, '__BACKBRACE__');
            start_date = start_date.replace(/\//g, '__FORWARDSLASH__');
            end_date = end_date.replace(/\//g, '__FORWARDSLASH__');
            var request = "/mdissk/tools/audit/construct_audit_content/" + user_name + "/" + module + "/" + start_date + "/" + end_date + "/1";
            var result = obj.Ajax.get_template(request);
            $('div[name=' + window_name + ']').find('div[name=audit_log_content]').html(result.responseText);
        });
        $('button[name=btnReset]').click({obj:obj},function()
        {
            var window_name = $(this).parents('div[name^="window-"]').last().attr('name');
            $('div[name=' + window_name + ']').find('select[name=users]').val('All');
            $('div[name=' + window_name + ']').find('select[name=modules]').val('All');
            $('div[name=' + window_name + ']').find('#start_date').val('All');
            $('div[name=' + window_name + ']').find('#end_date').val('All');
        });
    };

    this.diff = function(audit_log_id)
    {
        this.Core.launch('/mdissk/tools/audit/show_diff/' + audit_log_id, '[Administration] Audit Diff', 'audit_log_diff_window', '/mdissk/core/help/show_help/tools/audit_diff', '0', '-1', '-1', '-1', '-1', 'No', '-1', '-1', '-1', '-1', '-1');
    }
}