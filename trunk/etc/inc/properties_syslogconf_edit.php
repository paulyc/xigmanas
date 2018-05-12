<?php
/*
	properties_syslogconf_edit.php

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
require_once 'properties_syslogconf.php';

class properties_syslogconf_edit extends properties_syslogconf {
	public function init_comment() {
		$property = parent::init_comment();
		$description = '';
		$placeholder = gtext('Enter a description');
		$property->
			set_id('comment')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(80)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_UNSAFE_RAW)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => ''])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_facility() {
		$property = parent::init_facility();
		$description = '';
		$placeholder = gtext('Enter facility name');
		$property->
			set_id('facility')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(80)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_level() {
		$property = parent::init_level();
		$description = '';
		$placeholder = gtext('Enter level name');
		$property->
			set_id('level')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(80)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function init_value() {
		$property = parent::init_value();
		$description = '';
		$placeholder = gtext('Enter destination');
		$property->
			set_id('value')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(80)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
}