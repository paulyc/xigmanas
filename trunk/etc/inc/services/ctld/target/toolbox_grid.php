<?php
/*
	services\ctld\target\toolbox_grid.php

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
use services\ctld\utilities as myu;
/**
 *	Wrapper class for autoloading functions
 */
class toolbox_grid {
	const NOTIFICATION_PROCESSOR = 'process_notification';
/**
 *	Create the sphere object
 *	@global array $config
 *	@return \common\sphere\grid
 */
	public static function init_sphere() {
		global $config;

		$sphere = new mys\grid('services_ctl_target','php');
		$sphere->get_modify()->set_basename($sphere->get_basename() . '_edit');
		$sphere->
			set_notifier(__NAMESPACE__)->
			set_notifier_processor(sprintf('%s::%s',static::class,static::NOTIFICATION_PROCESSOR))->
			set_row_identifier('uuid')->
			set_enadis(true)->
			set_lock(false)->
			setmsg_sym_add(gettext('Add Target'))->
			setmsg_sym_mod(gettext('Edit Target'))->
			setmsg_sym_del(gettext('Target is marked for deletion'))->
			setmsg_sym_loc(gettext('Target is locked'))->
			setmsg_sym_unl(gettext('Target is unlocked'))->
			setmsg_cbm_delete(gettext('Delete Selected Targets'))->
			setmsg_cbm_disable(gettext('Disable Selected Targets'))->
			setmsg_cbm_enable(gettext('Enable Selected Targets'))->
			setmsg_cbm_toggle(gettext('Toggle Selected Targets'))->
			setmsg_cbm_delete_confirm(gettext('Do you want to delete selected targets?'))->
			setmsg_cbm_disable_confirm(gettext('Do you want to disable selected targets?'))->
			setmsg_cbm_enable_confirm(gettext('Do you want to enable selected targets?'))->
			setmsg_cbm_toggle_confirm(gettext('Do you want to toggle selected targets?'));
		$sphere->grid = &array_make_branch($config,'ctld','ctl_target','param');
		if(!empty($sphere->grid)):
			array_sort_key($sphere->grid,'name');
		endif;
		return $sphere;
	}
/**
 *	Create the request method object
 *	@param basic_properties $cop
 *	@param mys\grid $sphere
 *	@return \common\rmo\rmo The request method object
 */
	public static function init_rmo(basic_properties $cop,mys\grid $sphere) {
		$rmo = new myr\rmo();
		$rmo->add('POST','apply',PAGE_MODE_VIEW);
		$rmo->add('POST',$sphere->get_cbm_button_val_delete(),PAGE_MODE_POST);
		if($sphere->is_enadis_enabled() && method_exists($cop,'get_enable')):
			if($sphere->toggle()):
				$rmo->add('POST',$sphere->get_cbm_button_val_toggle(),PAGE_MODE_POST);
			else:
				$rmo->add('POST',$sphere->get_cbm_button_val_enable(),PAGE_MODE_POST);
				$rmo->add('POST',$sphere->get_cbm_button_val_disable(),PAGE_MODE_POST);
			endif;
		endif;
		$rmo->add('SESSION',$sphere->get_basename(),PAGE_MODE_VIEW);
		$rmo->set_default('GET','view',PAGE_MODE_VIEW);
		return $rmo;
	}
/**
 *	Create the property object
 *	@return \services\ctld\target\basic_properties
 */
	public static function init_properties() {
		$cop = new basic_properties();
		return $cop;
	}
/**
 *	Render the page
 *	@global string $d_sysrebootreqd_path
 *	@global stringtype $savemsg
 *	@param \services\ctld\target\basic_properties $cop
 *	@param \common\sphere\grid $sphere
 */
	public static function render(basic_properties $cop,mys\grid $sphere) {
		global $d_sysrebootreqd_path;
		global $savemsg;

		$input_errors = [];
		$errormsg = '';
		$pgtitle = [gettext('Services'),gettext('CAM Target Layer'),gettext('Targets')];
		$record_exists = count($sphere->grid) > 0;
		$use_tablesort = count($sphere->grid) > 1;
		$a_col_width = ['5%','25%','10%','15%','10%','25%','10%'];
		$n_col_width = count($a_col_width);
		if($use_tablesort):
			$document = new_page($pgtitle,$sphere->get_scriptname(),'tablesort');
		else:
			$document = new_page($pgtitle,$sphere->get_scriptname());
		endif;
		//	get areas
		$body = $document->getElementById('main');
		$pagecontent = $document->getElementById('pagecontent');
		//	add tab navigation
		$document->
			add_area_tabnav()->
				push()->
				add_tabnav_upper()->
					ins_tabnav_record('services_ctl.php',gettext('Global Settings'))->
					ins_tabnav_record('services_ctl_target.php',gettext('Targets'),gettext('Reload page'),true)->
					ins_tabnav_record('services_ctl_lun.php',gettext('LUNs'))->
					ins_tabnav_record('services_ctl_portal_group.php',gettext('Portal Groups'))->
					ins_tabnav_record('services_ctl_auth_group.php',gettext('Auth Groups'))->
				pop()->
				add_tabnav_lower()->
					ins_tabnav_record('services_ctl_target.php',gettext('Targets'),gettext('Reload page'),true)->
					ins_tabnav_record('services_ctl_sub_port.php',gettext('Ports'))->
					ins_tabnav_record('services_ctl_sub_lun.php',gettext('LUNs'));
		//	create data area
		$content = $pagecontent->add_area_data();
		//	display information, warnings and errors
		$content->
			ins_input_errors($input_errors)->
			ins_info_box($savemsg)->
			ins_error_box($errormsg);
		if(updatenotify_exists($sphere->get_notifier())):
			$content->ins_config_has_changed_box();
		endif;
		//	add content
		$table = $content->add_table_data_selection();
		$table->ins_colgroup_with_styles('width',$a_col_width);
		$thead = $table->addTHEAD();
		if($record_exists):
			$tbody = $table->addTBODY();
		else:
			$tbody = $table->addTBODY(['class' => 'donothighlight']);
		endif;
		$tfoot = $table->addTFOOT();
		$thead->ins_titleline(gettext('Overview'),$n_col_width);
		$tr = $thead->addTR();
		if($record_exists):
			$tr->
				push()->
				addTHwC('lhelc sorter-false parser-false')->
					ins_cbm_checkbox_toggle($sphere)->
				pop()->
				insTHwC('lhell',$cop->get_name()->get_title())->
				insTHwC('lhelc sorter-false parser-false',gettext('Status'))->
				insTHwC('lhell',$cop->get_redirect()->get_title())->
				insTHwC('lhell',$cop->get_alias()->get_title())->
				insTHwC('lhell',$cop->get_description()->get_title())->
				insTHwC('lhebl sorter-false parser-false',$cop->get_toolbox()->get_title());
		else:
			$tr->
				insTHwC('lhelc')->
				insTHwC('lhell',$cop->get_name()->get_title())->
				insTHwC('lhelc',gettext('Status'))->
				insTHwC('lhell',$cop->get_redirect()->get_title())->
				insTHwC('lhell',$cop->get_alias()->get_title())->
				insTHwC('lhell',$cop->get_description()->get_title())->
				insTHwC('lhebl',$cop->get_toolbox()->get_title());
		endif;
		if($record_exists):
			foreach($sphere->grid as $sphere->row_id => $sphere->row):
				$notificationmode = updatenotify_get_mode($sphere->get_notifier(),$sphere->get_row_identifier_value());
				$is_notdirty = (UPDATENOTIFY_MODE_DIRTY != $notificationmode) && (UPDATENOTIFY_MODE_DIRTY_CONFIG != $notificationmode);
				$is_enabled = $sphere->is_enadis_enabled() ? (is_bool($test = $sphere->row[$cop->get_enable()->get_name()] ?? false) ? $test : true): true;
				$is_notprotected = $sphere->is_lock_enabled() ? !(is_bool($test = $sphere->row[$cop->get_protected()->get_name()] ?? false) ? $test : true) : true;
				$dc = $is_enabled ? '' : 'd';
				$tbody->
					addTR()->
						push()->
						addTDwC('lcelc' . $dc)->
							ins_cbm_checkbox($sphere,!($is_notdirty && $is_notprotected))->
						pop()->
						insTDwC('lcell' . $dc,$sphere->row[$cop->get_name()->get_name()] ?? '')->
						ins_enadis_icon($is_enabled)->
						insTDwC('lcell' . $dc,$sphere->row[$cop->get_redirect()->get_name()] ?? '')->
						insTDwC('lcell' . $dc,$sphere->row[$cop->get_alias()->get_name()] ?? '')->
						insTDwC('lcell' . $dc,$sphere->row[$cop->get_description()->get_name()] ?? '')->
						add_toolbox_area()->
							ins_toolbox($sphere,$is_notprotected,$is_notdirty)->
							ins_maintainbox($sphere,false)->
							ins_informbox($sphere,false);
			endforeach;
		else:
			$tbody->ins_no_records_found($n_col_width);
		endif;
		$tfoot->ins_record_add($sphere,$n_col_width);
		$document->
			add_area_buttons()->
				ins_cbm_button_enadis($sphere)->
				ins_cbm_button_delete($sphere);
		//	additional javascript code
		$body->addJavaScript($sphere->get_js());
		$body->add_js_on_load($sphere->get_js_on_load());
		$body->add_js_document_ready($sphere->get_js_document_ready());
		$document->render();
	}
/**
 *	Process notifications
 *	@param int $mode
 *	@param string $data
 *	@return int
 */
	public static function process_notification(int $mode,string $data) {
		$sphere = self::init_sphere();
		$retval = myu::process_notification_row($mode,$data,$sphere);
		return $retval;
	}
}
