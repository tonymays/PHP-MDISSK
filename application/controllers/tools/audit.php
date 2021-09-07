<?php if (!defined('BASEPATH')) exit("direct access not allowed");
/*
 * Audit
 * Base controller class for audit operations
 * @author Anthony Mays
 * @category API Core
 */
class Audit extends MY_Controller
{
    /*
     * Class destructor
     * @param None
     * @return None
     */
    public function __construct()
    {
        parent::__construct();
    }

    /*
     * index
     * core controller method that loads the audit log report
     * @param None
     * @return html page
     */
    final public function index()
    {
        $toolbar = array();
        array_push($toolbar, array('name'=>'btnSearch', 'text'=>'Search', 'img'=>'assets/images/search.png', 'title'=>'Click to search audit log'));
        array_push($toolbar, array('name'=>'btnReset', 'text'=>'Reset', 'img'=>'assets/images/refresh.png', 'title'=>'Click to reset filters'));
        $this->data['toolbar'] = $this->construct_toolbar($toolbar);
        $this->data['users'] = $this->construct_users();
        $this->data['modules'] = $this->construct_modules();
        $this->data['content'] = $this->construct_audit_content('-1', 'All', 'All', 'All', 0);
        echo $this->parser->parse('tools/audit_log_header.html', $this->data, true);
    }

    /*
     * construct_audit_content
     * core controller method that loads the contents of an audit log report
     * @param string $user_name - the user_name used in the query
     * @param string $module - the module used in the query
     * @param string $start_date - the start date used in the query
     * @param string $end_date - the end date used in the query
     * @param integer $perform_echo - a flag indicating to echo (1) or return results as a variable (0)
     * @return html page
     */
    final public function construct_audit_content($user_name, $module, $start_date, $end_date, $perform_echo = 0)
    {
        $data = array('rows'=>array());
        $module = str_replace('__FORWARDBRACE__', '[', $module);
        $module = str_replace('__BACKBRACE__', ']', $module);
        $start_date = str_replace('__FORWARDSLASH__', '/', $start_date);
        $end_date = str_replace('__FORWARDSLASH__', '/', $end_date);
        $module = $this->Session->String->decode_url_chars($module);
        $data['rows'] = $this->get_log($user_name, $module, $start_date, $end_date);
        if ($perform_echo == 0)
        {
            return $this->parser->parse('tools/audit_log_content.html', $data, true);
        }
        else
        {
            echo $this->parser->parse('tools/audit_log_content.html', $data, true);
        }
    }

    /*
     * get_log
     * core controller method that gathers and loads the content records of an audit log report query
     * @param string $user_name - the user_name used in the query
     * @param string $module - the module used in the query
     * @param string $start_date - the start date used in the query
     * @param string $end_date - the end date used in the query
     * @return html page
     */
    final public function get_log($user_name = 'All', $module = 'All', $start_date = 'All', $end_date = 'All')
    {
        $result = array();
        $tmp = array('log'=>array());

        $where_clause = '';
        if ($user_name != 'All')
        {
            $where_clause .= " b.user_name = '" . $user_name . "'";
        }

        if ($module != 'All')
        {
            if ($where_clause != '')
            {
                $where_clause .= ' and ';
            }
            $where_clause .= " a.module = '" . $module . "'";
        }

        if ($start_date != 'All')
        {
            $segments = explode('/', $start_date);
            $time = mktime(0, 0, 0, $segments[0], $segments[1], $segments[2]);
            if ($where_clause != '')
            {
                $where_clause .= ' and ';
            }
            $where_clause .= " a.trx_date >= '" . $time . "'";
        }

        if ($end_date != 'All')
        {
            $segments = explode('/', $end_date);
            $time = mktime(23, 59, 59, $segments[0], $segments[1], $segments[2]);
            if ($where_clause != '')
            {
                $where_clause .= ' and ';
            }
            $where_clause .= " a.trx_date <= '" . $time . "'";
        }

        if ($where_clause == '')
        {
            $sql = "select a.audit_log_id, a.user_id, b.user_name, a.module, a.trx_date
                from audit_log a
                 left outer join users b on (a.user_id = b.user_id)
                order by a.trx_date, a.module, b.user_name";
        }
        else
        {
            $sql = "select a.audit_log_id, a.user_id, b.user_name, a.module, a.trx_date
                from audit_log a
                 left outer join users b on (a.user_id = b.user_id)" .
                " where " . $where_clause .
                " order by a.trx_date, a.module, b.user_name";
        }
        $set = $this->Session->DB->query($sql);
        foreach($set as $key=>$value)
        {
            if ($this->Session->permit('canedituser') == false)
            {
                $user_name = $value['user_name'];
            }
            else
            {
                $user_name = '<a title="View User Profile" href="javascript:UsersObj.edit(' . $value['user_id'] . ')">' . $value['user_name'] . '</a>';
            }
            array_push($tmp['log'],
                array
                (
                    'user_name'=>$user_name,
                    'module'=>'<a title="View Changes" href="javascript:AuditObj.diff(' . $value['audit_log_id'] . ')">' . $value['module'] . '</a>',
                    'trx_date'=>$this->Session->Date->convert_epoch($value['trx_date'])
                ));
        }
        array_push($result, $tmp);
        return $result;
    }

    /*
     * log
     * logs the results of a transaction when specified by the corresponding XML document
     * @param None
     * @return None
     */
    final public function log()
    {
        if (isset($GLOBALS['HTTP_RAW_POST_DATA']))
        {
            $xml_doc = (string)$GLOBALS['HTTP_RAW_POST_DATA'];
            $array = $this->Session->XML->to_array($xml_doc);
            if (!empty($array))
            {
                // cleanup array
                $array['old'] = str_replace('__EQUALSIGN__', '=', $array['old']);
                $array['old'] = str_replace('__ANDSIGN__', '&', $array['old']);
                $array['old'] = str_replace('__PLUS__', ' ', $array['old']);
                $array['old'] = str_replace('__LESSTHAN__', '<', $array['old']);
                $array['old'] = str_replace('__GREATERTHAN__', '>', $array['old']);
                $array['old'] = str_replace('__FORWARDSLASH__', '/', $array['old']);
                $array['new'] = str_replace('__EQUALSIGN__', '=', $array['new']);
                $array['new'] = str_replace('__ANDSIGN__', '&', $array['new']);
                $array['new'] = str_replace('__PLUS__', ' ', $array['new']);
                $array['new'] = str_replace('__LESSTHAN__', '<', $array['new']);
                $array['new'] = str_replace('__GREATERTHAN__', '>', $array['new']);
                $array['new'] = str_replace('__FORWARDSLASH__', '/', $array['new']);
                $array['old'] = $this->Session->String->decode_url_chars($array['old']);
                $array['new'] = $this->Session->String->decode_url_chars($array['new']);

                // format data for writes
                $user_id = $this->Session->get_user_id();
                $trx_date = strtotime("now");
                $module = $array['module'];

                // write data
                $sql = "insert into audit_log select null, ?, ?, ?";
                $pdo = $this->Session->DB->prepare($sql);
                $this->Session->DB->execute($pdo, array($user_id, $module, $trx_date));
                $audit_log_id = $this->get_audit_log_id($user_id, $module, $trx_date);
                $sql = "insert into audit_log_data select ?, ?, ?";
                $pdo = $this->Session->DB->prepare($sql);

                // filter log
                $array['old'] = $this->filter_log($array['old']);
                $array['new'] = $this->filter_log($array['new']);

                // save log entry
                $this->Session->DB->execute($pdo, array($audit_log_id, $array['old'], $array['new']));
            }
        }
    }

    /*
     * get_audit_log_id
     * returns the audit log id associated with the specified user_id, module and trx_date; otherwise, returns -1
     * if an audit log id could not be found
     * @param string $user_id - the user_id used in the query
     * @param string $module - the module used in the query
     * @param string $trx_date - the trx date used in the query
     * @return integer
     */
    final public function get_audit_log_id($user_id, $module, $trx_date)
    {
        $result = -1;
        $sql = "select audit_log_id from audit_log where user_id = ? and module = ? and trx_date = ?";
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($user_id, $module, $trx_date));
        $set = $this->Session->DB->fetch_all($pdo);
        if (!empty($set))
        {
            $result = $set[0]['audit_log_id'];
        }
        return $result;
    }

    /*
     * show_diff
     * returns an html page of the before and after picture of the specified transaction
     * @param string $audit_log_id - the audit log id used for the query
     * @return html template
     */
    final public function show_diff($audit_log_id)
    {
        $data['old'] = '';
        $data['new'] = '';
        $data['trx_date'] = '';
        $sql = "select b.user_name, a.trx_date from audit_log a left outer join users b on (a.user_id = b.user_id) where audit_log_id = ?";
        $pdo = $this->Session->DB->prepare($sql);
        $this->Session->DB->execute($pdo, array($audit_log_id));
        $set = $this->Session->DB->fetch_all($pdo);
        if (!empty($set))
        {
            $data['user_name'] = $set[0]['user_name'];
            $data['trx_date'] = $this->Session->Date->convert_epoch($set[0]['trx_date']);
            $sql = "select old, new from audit_log_data where audit_log_id = ?";
            $pdo = $this->Session->DB->prepare($sql);
            $this->Session->DB->execute($pdo, array($audit_log_id));
            $set = $this->Session->DB->fetch_all($pdo);
            if (!empty($set))
            {
                $old_array = explode('&', $set[0]['old']);
                $new_array = explode('&', $set[0]['new']);
                $old_results = '';
                $new_results = '';
                foreach($old_array as $key=>$value)
                {
                    if (in_array($value, $new_array))
                    {
                        $old_results .= "<div>$value</div>";
                    }
                    else
                    {
                        $old_results .= "<div style='background-color:#ffcccc'>$value</div>";
                    }
                }
                foreach($new_array as $key=>$value)
                {
                    if (in_array($value, $old_array))
                    {
                        $new_results .= "<div>$value</div>";
                    }
                    else
                    {
                        $new_results .= "<div style='background-color:#ccffcc'>$value</div>";
                    }
                }
                $data['old'] = $old_results;
                $data['new'] = $new_results;
            }
        }
        echo $this->parser->parse('tools/audit_diff.html', $data, true);
    }

    /*
     * construct_users
     * constructs the users drop down used on the Audit Log Report
     * @param None
     * @return html drop down
     */
    private function construct_users()
    {
        try
        {
            $result = '<select name=users>';
            $result .= "<option selected='selected' class='option_actions' value='All'>All</option>";
            $set = $this->Session->DB->query("select user_name from users order by user_name");
            foreach($set as $key=>$value)
            {
                $result .= "<option class='audit_log_drop_downs' value='" . $value['user_name'] . "'>" . $value['user_name'] . "</option>";
            }
            $result .= '</select>';
            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * construct_modules
     * constructs the modules drop down used on the Audit Log Report
     * @param None
     * @return html drop down
     */
    private function construct_modules()
    {
        try
        {
            $result = '<select name=modules>';
            $result .= "<option selected='selected' class='option_actions' value='All'>All</option>";
            $set = $this->Session->DB->query("select distinct module from audit_log order by module");
            if (!empty($set))
            {
                foreach($set as $key=>$value)
                {
                    $result .= "<option class='audit_log_drop_downs' value='" . $value['module'] . "'>" . $value['module'] . "</option>";
                }
            }
            $result .= '</select>';
            return $result;
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    /*
     * filter_log
     * takes a data array an removes sensitive information from the audit transaction (ie. passwords) and returns the
     * new string (could be empty if all data is sensitive)
     * @param array $data - the data array (before or after picture) of a transactions
     * @return string
     */
    private function filter_log($data)
    {
        $result = '';
        $result_array = array();
        $array = explode('&', $data);
        foreach($array as $key=>$value)
        {
            $store = true;
            if ($this->Session->String->contains($value, 'password'))
            {
                $store = false;
            }

            if ($store)
            {
                array_push($result_array, $value);
            }
        }

        if (count($result_array > 0))
        {
            foreach($result_array as $key=>$value)
            {
                if ($result != '')
                {
                    $result .= '&';
                }
                $result .= $value;
            }
        }
        return $result;
    }
}