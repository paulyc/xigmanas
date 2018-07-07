<?php
/*
	properties_services_ctl_sub_option.php

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
require_once 'properties.php';

class ctl_sub_option_properties extends co_property_container_param {
	protected $x_name;
	public function get_name() {
		return $this->x_name ?? $this->init_name();
	}
	public function init_name() {
		$property = $this->x_name = new property_text($this);
		$property->
			set_name('name')->
			set_title(gtext('Option Name'));
		return $property;
	}
	protected $x_value;
	public function get_value() {
		return $this->x_value ?? $this->init_value();
	}
	public function init_value() {
		$property = $this->x_value = new property_text($this);
		$property->
			set_name('value')->
			set_title(gtext('Option Value'));
		return $property;
	}
	protected $x_group;
	public function get_group() {
		return $this->x_group ?? $this->init_group();
	}
	public function init_group() {
		$property = $this->x_group = new property_list_multi($this);
		$property->
			set_name('group')->
			set_title(gtext('Portal Group'));
		return $property;
	}
}
class ctl_sub_option_edit_properties extends ctl_sub_option_properties {
	public function init_name() {
		$property = parent::init_name();
		$description = gtext('Name of the option.');
		$regexp = '/^\S{1,223}$/';
		$property->
			set_id('name')->
			set_description($description)->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(223)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_value() {
		$property = parent::init_value();
		$description = gtext('Value of the option.');
		$regexp = '/^.{1,223}$/';
		$property->
			set_id('value')->
			set_description($description)->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(223)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_group() {
		$property = parent::init_group();
		$description = gtext('Select portal groups.');
		$options = [];
		$property->
			set_id('group')->
			set_description($description)->
			set_defaultvalue([])->
			set_options($options)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
}