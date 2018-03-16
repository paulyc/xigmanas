<?php
/*
	properties.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2018 The NAS4Free Project <info@nas4free.org>.
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice, this
	   list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright notice,
	   this list of conditions and the following disclaimer in the documentation
	   and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
	ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
	ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

	The views and conclusions contained in the software and documentation are those
	of the authors and should not be interpreted as representing official policies,
	either expressed or implied, of the NAS4Free Project.
 */
require_once 'util.inc';

abstract class properties {
	protected $x_owner = NULL;
	protected $x_id = NULL;
	protected $x_name = NULL;
	protected $x_title = NULL;
	protected $x_description = NULL;
	protected $x_caption = NULL;
	protected $x_defaultvalue = NULL;
	protected $x_editableonadd = NULL;
	protected $x_editableonmodify = NULL;
	protected $x_filter = [];
	protected $x_message_error = NULL;
	protected $x_message_info = NULL;
	protected $x_message_warning = NULL;

	public function __construct($owner = NULL) {
		$this->set_owner($owner);
		return $this;
	}
/**
 *	Lazy call via magic get
 *	@param string $name the name of the property
 *	@return value Returns the value of the property
 *	@throws BadMethodCallException
 */
	public function &__get(string $name) {
		$method_name = 'get_' . $name;
		if(method_exists($this,$method_name)):
			$return_data = $this->$method_name();
		else:
			$message = htmlspecialchars(sprintf("Method '%s' for '%s' not found",$method_name,$name));
			throw new BadMethodCallException($message);
		endif;
		return $return_data;
	}
	abstract public function filter_use_default();
	public function set_owner($owner = NULL) {
		if(is_object($owner)):
			$this->x_owner = $owner;
		endif;
		return $this;
	}
	public function get_owner() {
		return $this->x_owner;
	}
	public function set_id(string $value = NULL) {
		$this->x_id = $value;
		return $this;
	}
	public function get_id() {
		return $this->x_id;
	}
	public function set_name(string $value = NULL) {
		$this->x_name = $value;
		return $this;
	}
	public function get_name() {
		return $this->x_name;
	}
	public function set_title(string $value = NULL) {
		$this->x_title = $value;
		return $this;
	}
	public function get_title() {
		return $this->x_title;
	}
	public function set_description($value = NULL) {
		$this->x_description = $value;
		return $this;
	}
	public function get_description() {
		return $this->x_description;
	}
	public function set_caption(string $value = NULL) {
		$this->x_caption = $value;
		return $this;
	}
	public function get_caption() {
		return $this->x_caption;
	}
	public function set_defaultvalue($value = NULL) {
		$this->x_defaultvalue = $value;
		return $this;
	}
	public function get_defaultvalue() {
		return $this->x_defaultvalue;
	}
	public function set_editableonadd(bool $value = NULL) {
		$this->x_editableonadd = $value;
		return $this;
	}
	public function get_editableonadd() {
		return $this->x_editableonadd;
	}
	public function set_editableonmodify(bool $value = NULL) {
		$this->x_editableonmodify = $value;
		return $this;
	}
	public function get_editableonmodify() {
		return $this->x_editableonmodify;
	}
	public function set_message_error(string $value = NULL) {
		$this->x_message_error = $value;
		return $this;
	}
	public function get_message_error() {
		return $this->x_message_error;
	}
	public function set_message_info(string $value = NULL) {
		$this->x_message_info = $value;
		return $this;
	}
	public function get_message_info() {
		return $this->x_message_info;
	}
	public function set_message_warning(string $value = NULL) {
		$this->x_message_warning = $value;
		return $this;
	}
	public function get_message_warning() {
		return $this->x_message_warning;
	}
/**
 * Method to set filter type.
 * @param int $value Filter type.
 * @param string $filter_name Name of the filter, default is 'ui'.
 * @return object Returns $this.
 */	
	public function set_filter($value = NULL,string $filter_name = 'ui') {
//		create array element if it doesn't exist.
		if(array_key_exists($filter_name,$this->x_filter)):
			$this->x_filter[$filter_name]['filter'] = $value;
		else:
			$this->x_filter[$filter_name] = ['filter' => $value,'flags' => NULL,'options' => NULL];
		endif;
		return $this;
	}
/**
 * Method to set filter flags.
 * @param type $value Flags for filter.
 * @param string $filter_name Name of the filter, default is 'ui'.
 * @return object Returns $this.
 */
	public function set_filter_flags($value = NULL,string $filter_name = 'ui') {
//		create array element if it doesn't exist.
		if(array_key_exists($filter_name,$this->x_filter)):
			$this->x_filter[$filter_name]['flags'] = $value;
		else:
			$this->x_filter[$filter_name] = ['filter' => NULL,'flags' => $value,'options' => NULL];
		endif;
		return $this;
	}
/**
 * Method to set filter options.
 * @param array $value Filter options.
 * @param string $filter_name Name of the filter, default is 'ui'.
 * @return object Returns $this.
 */
	public function set_filter_options($value = NULL,string $filter_name = 'ui') {
//		create array element if it doesn't exist.
		if(array_key_exists($filter_name,$this->x_filter)):
			$this->x_filter[$filter_name]['options'] = $value;
		else:
			$this->x_filter[$filter_name] = ['filter' => NULL,'flags' => NULL,'options' => $value];
		endif;
		return $this;
	}
/**
 * Method returns the filter settings of $filter_name:
 * @param string $filter_name Name of the filter, default is 'ui'.
 * @return array If $filter_name exists the filter configuration is returned, otherwise NULL is returned.
 */
	public function get_filter(string $filter_name = 'ui') {
		if(array_key_exists($filter_name,$this->x_filter)):
			return $this->x_filter[$filter_name];
		endif;
		return NULL;
	}
/**
 * Method to apply filter to an input element.
 * A list of filters will be processed until a filter does not return NULL.
 * @param int $input_type Input type. Check the PHP manual for supported input types.
 * @param mixed $filter Single filter name or list of filters names to validate, default is ['ui'].
 * @return mixed Filter result.
 */
	public function validate_input(int $input_type = INPUT_POST,$filter = ['ui']) {
		$result = NULL;
		$filter_names = [];
		if(is_array($filter)):
			$filter_names = $filter;
		elseif(is_string($filter)):
			$filter_names = [$filter];
		endif;
		foreach($filter_names as $filter_name):
			if(is_string($filter_name)):
				$filter_parameter = $this->get_filter($filter_name);
				if(isset($filter_parameter)):
					$action  = (isset($filter_parameter['flags']) ? 1 : 0) + (isset($filter_parameter['options']) ? 2 : 0);
					switch($action):
						case 3:
							$result = filter_input($input_type,$this->get_name(),$filter_parameter['filter'],['flags' => $filter_parameter['flags'],'options' => $filter_parameter['options']]);
							break;
						case 2:
							$result = filter_input($input_type,$this->get_name(),$filter_parameter['filter'],['options' => $filter_parameter['options']]);
							break;
						case 1:
							$result = filter_input($input_type,$this->get_name(),$filter_parameter['filter'],$filter_parameter['flags']);
							break;
						case 0:
							$result = filter_input($input_type,$this->get_name(),$filter_parameter['filter']);
							break;
					endswitch;
				endif;
				if(isset($result)):
					break; // foreach
				endif;
			endif;
		endforeach;
		return $result;
	}
/**
 * Method to validate a value.
 * A list of filters will be processed until a filter does not return NULL.
 * @param mixed $value The value to be tested.
 * @param mixed $filter Single filter name or list of filters names to validate, default is ['ui'].
 * @return mixed Filter result.
 */
	public function validate_value($value,$filter = ['ui']) {
		$result = NULL;
		$filter_names = [];
		if(is_array($filter)):
			$filter_names = $filter;
		elseif(is_string($filter)):
			$filter_names = [$filter];
		endif;
		foreach($filter_names as $filter_name):
			if(is_string($filter_name)):
				$filter_parameter = $this->get_filter($filter_name);
				if(isset($filter_parameter)):
					$action  = (isset($filter_parameter['flags']) ? 1 : 0) + (isset($filter_parameter['options']) ? 2 : 0);
					switch($action):
						case 3:
							$result = filter_var($value,$filter_parameter['filter'],['flags' => $filter_parameter['flags'],'options' => $filter_parameter['options']]);
							break;
						case 2:
							$result = filter_var($value,$filter_parameter['filter'],['options' => $filter_parameter['options']]);
							break;
						case 1:
							$result = filter_var($value,$filter_parameter['filter'],$filter_parameter['flags']);
							break;
						case 0:
							$result = filter_var($value,$filter_parameter['filter']);
							break;
					endswitch;
				endif;
				if(isset($result)):
					break; // foreach
				endif;
			endif;
		endforeach;
		return $result;
	}
/**
 * Method to validate an array value. Index is the name property of $this.
 * @param array $variable The variable to be tested.
 * @param mixed $filter Single filter name or list of filters names to validate, default is ['ui'].
 * @return mixed Filter result.
 */
	public function validate_array_element(array $variable,$filter = ['ui']) {
		$result = NULL;
		$filter_names = [];
		if(is_array($filter)):
			$filter_names = $filter;
		elseif(is_string($filter)):
			$filter_names = [$filter];
		endif;
		if(array_key_exists($this->get_name(),$variable)):
			$value = $variable[$this->get_name()];
		else:
			$value = NULL;
		endif;
		foreach($filter_names as $filter_name):
			if(is_string($filter_name)):
				$filter_parameter = $this->get_filter($filter_name);
				if(isset($filter_parameter)):
					$action  = (isset($filter_parameter['flags']) ? 1 : 0) + (isset($filter_parameter['options']) ? 2 : 0);
					switch($action):
						case 3:
							$result = filter_var($value,$filter_parameter['filter'],['flags' => $filter_parameter['flags'],'options' => $filter_parameter['options']]);
							break;
						case 2:
							$result = filter_var($value,$filter_parameter['filter'],['options' => $filter_parameter['options']]);
							break;
						case 1:
							$result = filter_var($value,$filter_parameter['filter'],$filter_parameter['flags']);
							break;
						case 0:
							$result = filter_var($value,$filter_parameter['filter']);
							break;
					endswitch;
				endif;
				if(isset($result)):
					break; // foreach
				endif;
			endif;
		endforeach;
		return $result;
	}
/**
 *	Returns the value if key exists and is not null, otherwise an empty string is returned.
 *	@param array $source
 *	@return string
 */
	public function validate_config(array $source) {
		$name = $this->get_name();
		if(array_key_exists($name,$source)):
			$return_data = $source[$name];
		else:
			$return_data = $this->get_defaultvalue();
		endif;
		return $return_data;
	}
}
class properties_text extends properties {
	public $x_maxlength = 0;
	public $x_placeholder = NULL;
	public $x_size = 40;
	
	public function set_maxlength(int $value = 0) {
		$this->x_maxlength = $value;
		return $this;
	}
	public function get_maxlength() {
		return $this->x_maxlength;
	}
	public function set_placeholder(string $value = NULL) {
		$this->x_placeholder = $value;
		return $this;
	}
	public function get_placeholder() {
		return $this->x_placeholder;
	}
	public function set_size(int $value = 40) {
		$this->x_size = $value;
		return $this;
	}
	public function get_size() {
		return $this->x_size;
	}
	public function filter_use_default() {
		//	not empty, must contain at least one printable character
		$filter_name = 'ui';
		$this->set_filter(FILTER_VALIDATE_REGEXP,$filter_name);
		$this->set_filter_flags(FILTER_REQUIRE_SCALAR,$filter_name);
		$this->set_filter_options(['default' => NULL,'regexp' => '/\S/'],$filter_name);
		return $this;
	}
}
class properties_textarea extends properties {
	public $x_cols = 65;
	public $x_maxlength = 0;
	public $x_placeholder = NULL;
	public $x_rows = 5;
	public $x_wrap = false;
	
	public function set_cols(int $n_cols = 65) {
		$this->x_cols = $n_cols;
		return $this;
	}
	public function get_cols() {
		return $this->x_cols;
	}
	public function set_maxlength(int $value = 0) {
		$this->x_maxlength = $value;
		return $this;
	}
	public function get_maxlength() {
		return $this->x_maxlength;
	}
	public function set_placeholder(string $value = NULL) {
		$this->x_placeholder = $value;
		return $this;
	}
	public function get_placeholder() {
		return $this->x_placeholder;
	}
	public function set_rows(int $rows = 5) {
		$this->x_rows = $rows;
		return $this;
	}
	public function get_rows() {
		return $this->x_rows;
	}
	public function set_wrap(bool $wrap = false) {
		$this->x_wrap = $wrap;
		return $this;
	}
	public function get_wrap() {
		return $this->x_wrap;
	}
	public function filter_use_default() {
		$filter_name = 'ui';
		$this->set_filter(FILTER_DEFAULT,$filter_name);
		return $this;
	}
}
class properties_ipaddress extends properties_text {
	public function __construct($owner = NULL) {
		parent::__construct($owner);
		$this->
			set_maxlength(45)->
			set_placeholder(gtext('Enter IP Address'))->
			set_size(60);
		return $this;
	}
	public function filter_use_default() {
		$filter_name = 'ui';
		$this->set_filter(FILTER_VALIDATE_IP,$filter_name);
		$this->set_filter_flags(FILTER_REQUIRE_SCALAR,$filter_name);
		$this->set_filter_options(['default' => NULL],$filter_name);
		return $this;
	}
}
class properties_ipv4 extends properties_text {
	public function __construct($owner = NULL) {
		parent::__construct($owner);
		$this->
			set_maxlength(15)->
			set_placeholder(gtext('Enter IP Address'))->
			set_size(20);
		return $this;
	}
	public function filter_use_default() {
		$filter_name = 'ui';
		$this->set_filter(FILTER_VALIDATE_IP,$filter_name);
		$this->set_filter_flags(FILTER_REQUIRE_SCALAR | FILTER_FLAG_IPV4,$filter_name);
		$this->set_filter_options(['default' => NULL],$filter_name);
		return $this;
	}
}
class properties_ipv6 extends properties_text {
	public function __construct($owner = NULL) {
		parent::__construct($owner);
		$this->
			set_maxlength(45)->
			set_placeholder(gtext('Enter IP Address'))->
			set_size(60);
		return $this;
	}
	public function filter_use_default() {
		$filter_name = 'ui';
		$this->set_filter(FILTER_VALIDATE_IP,$filter_name);
		$this->set_filter_flags(FILTER_REQUIRE_SCALAR | FILTER_FLAG_IPV6,$filter_name);
		$this->set_filter_options(['default' => NULL],$filter_name);
		return $this;
	}
}
class properties_int extends properties_text {
	public $x_min = NULL;
	public $x_max = NULL;

	public function set_min(int $value = NULL) {
		$this->x_min = $value;
		return $this;
	}
	public function get_min() {
		return $this->x_min;
	}
	public function set_max(int $value = NULL) {
		$this->x_max = $value;
		return $this;
	}
	public function get_max() {
		return $this->x_max;
	}
	public function filter_use_default() {
		$filter_name = 'ui';
		$options = [];
		$options['default'] = NULL;
		$min = $this->get_min();
		if(isset($min)):
			$options['min_range'] = $min;
		endif;
		$max = $this->get_max();
		if(isset($max)):
			$options['max_range'] = $max;
		endif;
		$this->set_filter(FILTER_VALIDATE_INT,$filter_name);
		$this->set_filter_flags(FILTER_REQUIRE_SCALAR,$filter_name);
		$this->set_filter_options($options,$filter_name);
		return $this;
	}
}
class property_toolbox extends properties_text {
	public function __construct($owner = NULL) {
		parent::__construct($owner);
		$this->set_title(gtext('Toolbox'));
	}
}
class property_uuid extends properties_text {
	public function __construct($owner = NULL) {
		parent::__construct($owner);
		$this->set_title(gtext('Universally Unique Identifier'));
		$this->
			set_id('uuid')->
			set_name('uuid')->
			set_description(gtext('The UUID of the record.'))->
			set_size(45)->
			set_maxlength(36)->
			set_placeholder(gtext('Enter Universally Unique Identifier'))->
			filter_use_default()->
			set_editableonadd(false)->
			set_editableonmodify(false)->
			set_message_error(sprintf('%s: %s',$this->get_title(),gtext('The value is invalid.')));
	}
	public function filter_use_default() {
		$filter_name = 'ui';
		$this->set_filter(FILTER_VALIDATE_REGEXP,$filter_name);
		$this->set_filter_flags(FILTER_REQUIRE_SCALAR,$filter_name);
		$this->set_filter_options(['default' => NULL,'regexp' => '/^[\da-f]{4}([\da-f]{4}-){2}4[\da-f]{3}-[89ab][\da-f]{3}-[\da-f]{12}$/i'],$filter_name);
		return $this;
	}
	public function get_defaultvalue() {
		return uuid();
	}
}
class properties_list extends properties {
	public $x_options = NULL;
	
	public function set_options(array $value = NULL) {
		$this->x_options = $value;
		return $this;
	}
	public function get_options() {
		return $this->x_options;
	}
	public function validate_option($option) {
		if(array_key_exists($option,$this->get_options())):
			return $option;
		else:
			return NULL;
		endif;
	}
	public function validate_config(array $source) {
		$name = $this->get_name();
		if(array_key_exists($name,$source)):
			$option = $source[$name];
			if(array_key_exists($option,$this->get_options())):
				$return_data = $option;
			else:
				$return_data = '';
			endif;
		else:
			$return_data = $this->get_defaultvalue();
		endif;
		return $return_data;
	}
/**
 * Method to apply the default class filter to a filter name.
 * The filter is a regex to match any of the option array keys.
 * @param string $filter_name Name of the filter, default = 'ui'.
 * @return object Returns $this.
 */
	public function filter_use_default() {
		$filter_name = 'ui';
		$this->set_filter(FILTER_CALLBACK,$filter_name);
		$this->set_filter_options([$this,'validate_option'],$filter_name);
		return $this;
	}
}
class properties_bool extends properties {
	public function filter_use_default() {
		$filter_name = 'ui';
		$this->set_filter(FILTER_VALIDATE_BOOLEAN,$filter_name);
		$this->set_filter_flags(FILTER_NULL_ON_FAILURE,$filter_name);
		$this->set_filter_options(['default' => false],$filter_name);
		return $this;
	}
	public function validate_config(array $source) {
		$name = $this->get_name();
		if(array_key_exists($name,$source)):
			$value = $source[$name];
			$return_data = is_bool($value) ? $value : true;
		else:
			$return_data = false;
		endif;
		return $return_data;
	}
}
class property_enable extends properties_bool {
	public function __construct($owner = NULL) {
		parent::__construct($owner);
		$this->set_title(gtext('Enable Setting'));
		$this->
			set_id('enable')->
			set_name('enable')->
			set_caption(gtext('Enable'))->
			set_description('')->
			set_defaultvalue(true)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$this->get_title(),gtext('The value is invalid.')));
		return $this;
	}
}
class property_protected extends properties_bool {
	public function __construct($owner = NULL) {
		parent::__construct($owner);
		$this->set_title(gtext('Protect Setting'));
		$this->
			set_id('protected')->
			set_name('protected')->
			set_caption(gtext('Protect'))->
			set_description('')->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$this->get_title(),gtext('The value is invalid.')));
		return $this;
	}
}
abstract class co_property_container {
/**
 *	protected $x_propertyname; 
 */
	public function __construct() {
		$this->reset();
	}
	public function __destruct() {
		$this->reset();
	}
/**
 *	Sets all 'x_*' properties to NULL
 *	@return $this
 */
	public function reset() {
		foreach($this as $key => $value):
			if(strncmp($key,'x_',2) === 0):
				$this->$key = NULL;
			endif;
		endforeach;
		return $this;
	}
/**
 *	Initializes all 'x_*' properties by calling their 'get_*' method.
 *	The 'get_*' method itself calls the related 'init_*' method if necessary.
 *	@return $this
 */
	public function init_all() {
		foreach($this as $key => $value):
			unset($matches);
			if(1 === preg_match('/^x_(.+)/',$key,$matches)):
				$method_name = 'get_' . $matches[1];
				$this->$method_name();
			endif;
		endforeach;
		return $this;
 	}
/**
 *	Lazy call via magic get
 *	@param string $name the name of the property
 *	@return properties Returns a properties object
 *	@throws BadMethodCallException
 */
	public function &__get(string $name) {
		$method_name = 'get_' . $name;
		if(method_exists($this,$method_name)):
			$return_data = $this->$method_name();
		else:
			$message = htmlspecialchars(sprintf("Method '%s' for '%s' not found",$method_name,$name));
			throw new BadMethodCallException($message);
		endif;
		return $return_data;
	}
/*	
 *	public function get_propertyname() {
 *		return $this->x_propertyname ?? $this->init_propertyname();
 *	}
 *	public function init_propertyname() {
 *		$this->x_propertyname = new properties_nnn($this);
 *		...
 *		return $this->x_propertyname;
 *	}
 */
}