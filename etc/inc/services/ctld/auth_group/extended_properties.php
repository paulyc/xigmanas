<?php
/*
	services\ctld\auth_group\extended_properties.php

	Part of XigmaNAS (https://www.xigmanas.com).
	Copyright (c) 2018-2019 XigmaNAS <info@xigmanas.com>.
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
	of the authors and should not be interpreted as representing official policies
	of XigmaNAS, either expressed or implied.
*/
namespace services\ctld\auth_group;

final class extended_properties extends basic_properties {
	public function init_name() {
		$property = parent::init_name();
		$description = gettext('Name of the Auth Group.');
		$placeholder = gettext('Auth Group Name');
		$regexp = '/^\S{1,223}$/';
		$property->
			set_id('name')->
			set_description($description)->
			set_defaultvalue('')->
			set_placeholder($placeholder)->
			set_size(60)->
			set_maxlength(223)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_auth_type() {
		$property = parent::init_auth_type();
		$description = gettext('Sets the authentication type.');
		$options = [
			'' => gettext('Undefined'),
			'none' => gettext('None'),
			'deny' => gettext('Deny'),
			'chap' => gettext('CHAP'),
			'chap-mutual' => gettext('CHAP-Mutual')
		];
		$property->
			set_id('auth_type')->
			set_description($description)->
			set_defaultvalue('')->
			set_options($options)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
	public function init_auxparam() {
		$property = parent::init_auxparam();
		$description = gettext('These parameters will be added to this auth-group.');
		$placeholder = gettext('Enter additional parameters');
		$property->
			set_id('auxparam')->
			set_description($description)->
			set_placeholder($placeholder)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_defaultvalue('')->
			filter_use_default()->
			set_message_error(sprintf('%s: %s',$property->get_title(),gettext('The value is invalid.')));
		return $property;
	}
}
