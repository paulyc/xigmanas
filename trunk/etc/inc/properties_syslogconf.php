<?php
/*
	properties_syslogconf.php

	Part of XigmaNAS (http://www.xigmanas.com).
	Copyright (c) 2018 XigmaNAS <info@xigmanas.com>.
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
require_once 'properties.php';

class properties_syslogconf extends co_property_container {
	protected $x_comment;
	protected $x_enable;
	protected $x_facility;
	protected $x_level;
	protected $x_protected;
	protected $x_uuid;
	protected $x_value;
	protected $x_toolbox;

	public function get_comment() {
		return $this->x_comment ?? $this->init_comment();
	}
	public function init_comment() {
		$property = $this->x_comment = new property_text($this);
		$property->
			set_name('comment')->
			set_title(gtext('Description'));
		return $property;
	}
	public function get_enable() {
		return $this->x_enable ?? $this->init_enable();
	}
	public function init_enable() {
		$property = $this->x_enable = new property_enable($this);
		return $property;
	}
	public function get_facility() {
		return $this->x_facility ?? $this->init_facility();
	}
	public function init_facility() {
		$property = $this->x_facility= new property_text($this);
		$property->
			set_name('facility')->
			set_title(gtext('Facility'));
		return $property;
	}
	public function get_level() {
		return $this->x_level ?? $this->init_level();
	}
	public function init_level() {
		$property = $this->x_level = new property_text($this);
		$property->
			set_name('level')->
			set_title(gtext('Level'));
		return $property;
	}
	public function get_protected() {
		return $this->x_protected ?? $this->init_protected();
	}
	public function init_protected() {
		$property = $this->x_protected = new property_protected($this);
		return $property;
	}
	public function get_toolbox() {
		return $this->x_toolbox ?? $this->init_toolbox();
	}
	public function init_toolbox() {
		$property = $this->x_toolbox = new property_toolbox($this);
		return $property;
	}
	public function get_uuid() {
		return $this->x_uuid ?? $this->init_uuid();
	}
	public function init_uuid() {
		$property = $this->x_uuid = new property_uuid($this);
		return $property;
	}
	public function get_value() {
		return $this->x_value ?? $this->init_value();
	}
	public function init_value() {
		$property = $this->x_value = new property_text($this);
		$property->
			set_name('value')->
			set_title(gtext('Destination'));
		return $property;
	}
}
