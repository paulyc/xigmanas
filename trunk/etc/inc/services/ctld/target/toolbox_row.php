<?php
/*
	services\ctld\target\toolbox_row.php

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
namespace services\ctld\target;
use common\rmo as myr;
use common\sphere as mys;
/**
 *	Wrapper class for autoloading functions
 */
class toolbox_row {
/**
 *	Create the sphere object
 *	@global array $config
 *	@return \common\sphere\row The sphere object
 */
	public static function init_sphere() {
		global $config;

		$sphere = new mys\row('services_ctl_target_edit','php');
		$sphere->get_parent()->set_basename('services_ctl_target');
		$sphere->
			set_notifier(__NAMESPACE__)->
			set_row_identifier('uuid')->
			set_enadis(false)->
			set_lock(false);
		$sphere->grid = &array_make_branch($config,'ctld','ctl_target','param');
		return $sphere;
	}
/**
 *	Create the request method object
 *	@param \services\ctld\target\extended_properties $cop
 *	@param \common\sphere\row $sphere
 *	@return \common\rmo\rmo The request method object
 */
	public static function init_rmo(extended_properties $cop,mys\row $sphere) {
		$rmo = new myr\rmo();
		$rmo->
			add('GET','add',PAGE_MODE_ADD)->
			add('GET','edit',PAGE_MODE_EDIT)->
			add('POST','add',PAGE_MODE_ADD)->
			add('POST','cancel',PAGE_MODE_POST)->
			add('POST','clone',PAGE_MODE_CLONE)->
			add('POST','edit',PAGE_MODE_EDIT)->
			add('POST','save',PAGE_MODE_POST)->
			set_default('POST','cancel',PAGE_MODE_POST);
		return $rmo;
	}
/**
 *	Create the properties object
 *	@return \services\ctld\target\extended_properties The properties object
 */
	public static function init_properties() {
		$cop = new extended_properties();
		return $cop;
	}
}
