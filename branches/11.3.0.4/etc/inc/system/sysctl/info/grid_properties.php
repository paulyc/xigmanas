<?php
/*
	grid_properties.php

	Part of XigmaNAS (https://www.xigmanas.com).
	Copyright © 2018-2019 XigmaNAS <info@xigmanas.com>.
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
namespace system\sysctl\info;

use system\sysctl as myparent;
use common\properties as myp;

class grid_properties extends myparent\grid_properties {
	protected $x_sysctlinfo;
	public function init_sysctlinfo(): myp\property_text {
		$property = $this->x_sysctlinfo = new myp\property_text($this);
		$property->
			set_name('sysctlinfo')->
			set_title(gettext('Description'));
		return $property;
	}
	public function get_sysctlinfo(): myp\property_text {
		return $this->x_sysctlinfo ?? $this->init_sysctlinfo();
	}
	protected $x_sysctltype;
	public function init_sysctltype(): myp\property_text {
		$property = $this->x_sysctltype = new myp\property_text($this);
		$property->
			set_name('sysctltype')->
			set_title(gettext('Type'));
		return $property;
	}
	public function get_sysctltype(): myp\property_text {
		return $this->x_sysctltype ?? $this->init_sysctltype();
	}
}