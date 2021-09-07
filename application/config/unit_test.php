<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Initialize Unit Test
|--------------------------------------------------------------------------
|
| unit_test allows you to establish a codeigniter unit test process
| settings this variable to true will activate a unit test process;
| otherwise, setting unit_test to false will deactivate unit testing.
| 
| Note: your main controller; or, any controllers you wish to establish 
| unit tests within must contain the following code block:
|
| $this->load->library('unit_test');
| $this->unit->active($this->config->item('unit_test'));
|
| The above code block states: load the unit test library and either  
| activate or deactivate unit testing.
|
| An existing testing block could look like the following:
| // ======================  unit test code start ===========================	
| if ($this->config->item('unit_test'))
| {
|     print "<pre>"; print_r(get_config());		
|     echo $this->unit->run($this->data['time_zone_options'], 'is_array', 'Time Zone Options', implode(',', $this->data['time_zone_options']));
|     echo $this->unit->run($this->data['default_time_zone'], 'Eastern', 'Default Time Zone', $this->data['default_time_zone']);
| }
| // ======================  unit test code end =============================		
*/
$config['unit_test'] = false;

/* End of file config.php */
/* Location: ./application/config/config.php */
