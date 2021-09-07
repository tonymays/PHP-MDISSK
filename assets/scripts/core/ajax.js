/*
 * Javascript library - JAjax
 * manages the Site dispatch operations
 */
function JAjax()
{
    /*
     * get_template
     * obtains and returns the output of the requested url.  Will throw errors if unable to process.
     * @param string url - the url to execute
     * @param string querydata - params
     * @param bool async - a flag that determines if we are process sync or async
     * @return html
     */
    this.get_template = function(url, querydata, async)
    {
        async = typeof async !== 'undefined' ? async : false;
        $.ajaxSetup({async:async});
        var xmlhttp = $.post(url, querydata,function(data){}).done(function(){}).fail(function(){alert ("Failure performing lookup");}).always(function(){});
        return xmlhttp;
    };

    /*
     * post_form
     * posts a form back to server and will perform auditing if the posting page elects with audit_page css selector
     * @param string url - the url to execute
     * @param string datastream - the data to post back to server
     * @param string window_name - the name of the window containing the form
     * @return html
     */
    this.post_form = function(url, data_stream, window_name)
    {
        // post form
        var result = $.ajax({url: url,type: "POST", contentType: "application/x-www-form-urlencoded", processData: false, data: data_stream});

        // perform auditing if specified
        if (this.has_audit_tag(window_name))
        {
            var post = result.responseText;
            var has_error = $(post).find('.error_msg').attr('name');
            if (has_error == undefined)
            {
                var has_success = post.indexOf("class='success_msg'");
                if (has_success != -1)
                {
                    var module = $('div[name=' + window_name + ']').find('.window_header').text();
                    var mirror = $('form[name=' + window_name + '-Content-Form-Mirror]').serialize();
                    data_stream = data_stream.replace(/=/g, '__EQUALSIGN__');
                    data_stream = data_stream.replace(/\&/g, '__ANDSIGN__');
                    data_stream = data_stream.replace(/\+/g, '__PLUS__');
                    data_stream = data_stream.replace(/\</g, '__LESSTHAN__');
                    data_stream = data_stream.replace(/\>/g, '__GREATERTTHAN__');
                    data_stream = data_stream.replace(/\//g, '__FORWARDSLASH__');
                    mirror = mirror.replace(/=/g, '__EQUALSIGN__');
                    mirror = mirror.replace(/\&/g, '__ANDSIGN__');
                    mirror = mirror.replace(/\+/g, '__PLUS__');
                    mirror = mirror.replace(/\</g, '__LESSTHAN__');
                    mirror = mirror.replace(/\>/g, '__GREATERTTHAN__');
                    mirror = mirror.replace(/\//g, '__FORWARDSLASH__');
                    var xml = '<?xml version="1.0"?>\n';
                    xml += '<request>\n';
                    xml += '<module>' + module + '</module>\n';
                    xml += '<old>' + mirror + '</old>\n';
                    xml += '<new>' + data_stream + '</new>\n';
                    xml += '</request>';
                    this.post_xml('/mdissk/tools/audit/log', xml);
                    this.post_to_mirror(window_name, post);
                 }
            }
        }

        // return post results
        return result;
    };

    /*
     * post_xml
     * posts an xml document back to the server
     * @param string url - the url to execute
     * @param string xml - the xml document to post back
     * @return None
     */
    this.post_xml = function(url, xml)
    {
        $.ajax({url: url,type: "POST",contentType: "text/xml",processData: false,data: xml,success: function(result){return result;}});
    };

    /*
     * has_audit_tag
     * returns true if the specified window has the audit_page css selector; otherwise, returns false
     * @param string window - the name of the window to examine
     * @return bool
     */
    this.has_audit_tag = function(window_name)
    {
        var audit_tag = $('div[name=' + window_name + ']').find('.audit_page').attr('name');
        return (audit_tag != undefined);
    };

    /*
     * has_copy_audit_mirror
     * returns true if the specified window has the copy_mirror css selector; otherwise, returns false
     * @param string window - the name of the window to examine
     * @return bool
     */
    this.has_copy_audit_mirror = function(window_name)
    {
        var audit_tag = $('div[name=' + window_name + ']').find('div.copy_audit_mirror').attr('name');
        return (audit_tag != undefined);
    };

    /*
     * post_to_mirror
     * copies posted data to the audit mirror
     * @param string window - the name of the window to examine
     * @return None
     */
    this.post_to_mirror = function(window_name, posted_data)
    {
        if (this.has_copy_audit_mirror(window_name))
        {
            $('form[name=' + window_name + '-Content-Form-Mirror]').html(posted_data);
        }
    };

    /*
     * copy_form_to_mirror
     * copies posted data to the audit mirror - used in special cases where cross window communications are used
     * @param string window - the name of the window to examine
     * @return None
     */
    this.copy_form_to_mirror = function(window_name)
    {
        var posted_data = $('div[name=' + window_name + '-Content]').find('div.copy_audit_mirror').html();
        $('form[name=' + window_name + '-Content-Form-Mirror]').html(posted_data);
    };
}