<?php
/*
	system_syslogconf.php

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
require_once 'auth.inc';
require_once 'guiconfig.inc';
require_once 'co_sphere.php';
require_once 'properties_syslogconf.php';
require_once 'co_request_method.php';

function system_syslogconf_get_sphere() {
	global $config;

	$sphere = new co_sphere_grid('system_syslogconf','php');
	$sphere->modify->set_basename($sphere->get_basename() . '_edit');
	$sphere->set_notifier('syslogconf');
	$sphere->set_row_identifier('uuid');
	$sphere->enadis(true);
	$sphere->lock(true);
	$sphere->sym_add(gtext('Add Option'));
	$sphere->sym_mod(gtext('Edit Option'));
	$sphere->sym_del(gtext('Option is marked for deletion'));
	$sphere->sym_loc(gtext('Option is locked'));
	$sphere->sym_unl(gtext('Option is unlocked'));
	$sphere->cbm_delete(gtext('Delete Selected Options'));
	$sphere->cbm_delete_confirm(gtext('Do you want to delete selected options?'));
	$sphere->cbm_disable(gtext('Disable Selected Options'));
	$sphere->cbm_disable_confirm(gtext('Do you want to disable selected options?'));
	$sphere->cbm_enable(gtext('Enable Selected Options'));
	$sphere->cbm_enable_confirm(gtext('Do you want to enable selected options?'));
	$sphere->cbm_toggle(gtext('Toggle Selected Options'));
	$sphere->cbm_toggle_confirm(gtext('Do you want to toggle selected options?'));
	$sphere->grid = &array_make_branch($config,'system','syslogconf','param');
	return $sphere;
}
function syslogconf_process_updatenotification($mode,$data) {
	global $config;

	$retval = 0;
	$sphere = &system_syslogconf_get_sphere();
	switch($mode):
		case UPDATENOTIFY_MODE_NEW:
		case UPDATENOTIFY_MODE_MODIFIED:
			write_config();
			break;
		case UPDATENOTIFY_MODE_DIRTY_CONFIG:
		case UPDATENOTIFY_MODE_DIRTY:
			if(false !== ($sphere->row_id = array_search_ex($data,$sphere->grid,$sphere->get_row_identifier()))):
				unset($sphere->grid[$sphere->row_id]);
				write_config();
			endif;
			break;
	endswitch;
	return $retval;
}
//	init properties and sphere
$cop = new properties_syslogconf();
$sphere = &system_syslogconf_get_sphere();
//	silent fix identifier
if(false !== $sphere->get_row_identifier()):
	$updateconfig = false;
	foreach($sphere->grid as $sphere->row_id => $sphere->row):
		if(is_array($sphere->row)):
			if(is_null($cop->{$sphere->get_row_identifier()}->validate_array_element($sphere->row))):
				$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()] = $cop->{$sphere->get_row_identifier()}->get_defaultvalue();
				$updateconfig = true;
			endif;
		else:
			unset($sphere->grid[$sphere->row_id]);
			$updateconfig = true;
		endif;
	endforeach;
	if($updateconfig):
		write_config();
	endif;
endif;
$rmo = new co_request_method();
$rmo->add('POST','apply',PAGE_MODE_POST);
$rmo->add('POST',$sphere->get_cbm_button_val_delete(),PAGE_MODE_POST);
if($sphere->enadis() && method_exists($cop,'get_enable')):
	if($sphere->toggle()):
		$rmo->add('POST',$sphere->get_cbm_button_val_toggle(),PAGE_MODE_POST);
	else:
		$rmo->add('POST',$sphere->get_cbm_button_val_enable(),PAGE_MODE_POST);
		$rmo->add('POST',$sphere->get_cbm_button_val_disable(),PAGE_MODE_POST);
	endif;
endif;
$rmo->set_default('GET','view',PAGE_MODE_VIEW);
list($page_method,$page_action,$page_mode) = $rmo->validate();
switch($page_method):
	case 'POST':
		switch($page_action):
			case 'apply':
				$retval = 0;
				$retval |= updatenotify_process($sphere->get_notifier(),$sphere->get_notifier_processor());
				config_lock();
				$retval |= rc_restart_service('syslogd');
				config_unlock();
				$savemsg = get_std_save_message($retval);
				if($retval == 0):
					updatenotify_delete($sphere->get_notifier());
				endif;
				header($sphere->get_location());
				exit;
				break;
			case $sphere->get_cbm_button_val_delete():
				$sphere->cbm_grid = filter_input(INPUT_POST,$sphere->cbm_name,FILTER_DEFAULT,['flags' => FILTER_REQUIRE_ARRAY,'options' => ['default' => []]]);
				foreach($sphere->cbm_grid as $sphere->cbm_row):
					if(false !== ($sphere->row_id = array_search_ex($sphere->cbm_row,$sphere->grid,$sphere->get_row_identifier()))):
						$mode_updatenotify = updatenotify_get_mode($sphere->get_notifier(),$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
						switch ($mode_updatenotify):
							case UPDATENOTIFY_MODE_NEW:
								updatenotify_clear($sphere->get_notifier(),$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
								updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_DIRTY_CONFIG,$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
								break;
							case UPDATENOTIFY_MODE_MODIFIED:
								updatenotify_clear($sphere->get_notifier(),$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
								updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_DIRTY,$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
								break;
							case UPDATENOTIFY_MODE_UNKNOWN:
								updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_DIRTY,$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
								break;
						endswitch;
					endif;
				endforeach;
				header($sphere->get_location());
				exit;
				break;
			case $sphere->get_cbm_button_val_toggle():
				$name = $cop->get_enable()->get_name();
				$sphere->cbm_grid = filter_input(INPUT_POST,$sphere->cbm_name,FILTER_DEFAULT,['flags' => FILTER_REQUIRE_ARRAY,'options' => ['default' => []]]);
				$updateconfig = false;
				foreach($sphere->cbm_grid as $sphere->cbm_row):
					$sphere->row_id = array_search_ex($sphere->cbm_row,$sphere->grid,$sphere->get_row_identifier());
					if($sphere->row_id !== false):
						if(isset($sphere->grid[$sphere->row_id][$name]) && (is_bool($sphere->grid[$sphere->row_id][$name]) ? $sphere->grid[$sphere->row_id][$name] : true)):
							$sphere->grid[$sphere->row_id][$name] = false;
							unset($sphere->grid[$sphere->row_id][$name]);
						else:
							$sphere->grid[$sphere->row_id][$name] = true;
						endif;
						$updateconfig = true;
						$mode_updatenotify = updatenotify_get_mode($sphere->get_notifier(),$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
						if(UPDATENOTIFY_MODE_UNKNOWN == $mode_updatenotify):
							updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_MODIFIED,$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
						endif;
					endif;
				endforeach;
				if($updateconfig):
					write_config();
					$updateconfig = false;
				endif;
				header($sphere->get_location());
				exit;
				break;
			case $sphere->get_cbm_button_val_enable():
				$name = $cop->get_enable()->get_name();
				$sphere->cbm_grid = filter_input(INPUT_POST,$sphere->cbm_name,FILTER_DEFAULT,['flags' => FILTER_REQUIRE_ARRAY,'options' => ['default' => []]]);
				$updateconfig = false;
				foreach($sphere->cbm_grid as $sphere->cbm_row):
					$sphere->row_id = array_search_ex($sphere->cbm_row,$sphere->grid,$sphere->get_row_identifier());
					if($sphere->row_id  !== false):
						if(isset($sphere->grid[$sphere->row_id][$name]) && (is_bool($sphere->grid[$sphere->row_id][$name]) ? $sphere->grid[$sphere->row_id][$name] : true)):
						else:
							$sphere->grid[$sphere->row_id][$name] = true;
							$updateconfig = true;
							$mode_updatenotify = updatenotify_get_mode($sphere->get_notifier(),$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
							if(UPDATENOTIFY_MODE_UNKNOWN == $mode_updatenotify):
								updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_MODIFIED,$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
							endif;
						endif;
					endif;
				endforeach;
				if($updateconfig):
					write_config();
					$updateconfig = false;
				endif;
				header($sphere->get_location());
				exit;
				break;
			case $sphere->get_cbm_button_val_disable():
				$name = $cop->get_enable()->get_name();
				$sphere->cbm_grid = filter_input(INPUT_POST,$sphere->cbm_name,FILTER_DEFAULT,['flags' => FILTER_REQUIRE_ARRAY,'options' => ['default' => []]]);
				$updateconfig = false;
				foreach($sphere->cbm_grid as $sphere->cbm_row):
					$sphere->row_id = array_search_ex($sphere->cbm_row,$sphere->grid,$sphere->get_row_identifier());
					if($sphere->row_id !== false):
						if(isset($sphere->grid[$sphere->row_id][$name]) && (is_bool($sphere->grid[$sphere->row_id][$name]) ? $sphere->grid[$sphere->row_id][$name] : true)):
							$sphere->grid[$sphere->row_id][$name] = false;
							unset($sphere->grid[$sphere->row_id][$name]);
							$updateconfig = true;
							$mode_updatenotify = updatenotify_get_mode($sphere->get_notifier(),$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
							if(UPDATENOTIFY_MODE_UNKNOWN == $mode_updatenotify):
								updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_MODIFIED,$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
							endif;
						endif;
					endif;
				endforeach;
				if($updateconfig):
					write_config();
					$updateconfig = false;
				endif;
				header($sphere->get_location());
				exit;
				break;
		endswitch;
		break;
endswitch;
$pgtitle = [gtext('System'),gtext('Advanced'),gtext('syslog.conf')];
$record_exists = count($sphere->grid) > 0;
$a_col_width = ['5%','20%','20%','20%','10%','15','10%'];
$n_col_width = count($a_col_width);
//	prepare additional javascript code
$jcode = $sphere->doj(false);
if($record_exists):
	$document = new_page($pgtitle,$sphere->get_scriptname(),'tablesort');
else:
	$document = new_page($pgtitle,$sphere->get_scriptname());
endif;
//	get areas
$body = $document->getElementById('main');
$pagecontent = $document->getElementById('pagecontent');
//	add additional javascript code
if(isset($jcode)):
	$body->addJavaScript($jcode);
endif;
//	add tab navigation
$document->
	add_area_tabnav()->
		add_tabnav_upper()->
			ins_tabnav_record('system_advanced.php',gtext('Advanced'))->
			ins_tabnav_record('system_email.php',gtext('Email'))->
			ins_tabnav_record('system_email_reports.php',gtext('Email Reports'))->
			ins_tabnav_record('system_monitoring.php',gtext('Monitoring'))->
			ins_tabnav_record('system_swap.php',gtext('Swap'))->
			ins_tabnav_record('system_rc.php',gtext('Command Scripts'))->
			ins_tabnav_record('system_cron.php',gtext('Cron'))->
			ins_tabnav_record('system_loaderconf.php',gtext('loader.conf'))->
			ins_tabnav_record('system_rcconf.php',gtext('rc.conf'))->
			ins_tabnav_record('system_sysctl.php',gtext('sysctl.conf'))->
			ins_tabnav_record('system_syslogconf.php',gtext('syslog.conf'),gtext('Reload page'),true);
//	create data area
$content = $pagecontent->add_area_data();
//	display information, warnings and errors
$content->
	ins_input_errors($input_errors)->
	ins_info_box($savemsg)->
	ins_error_box($errormsg);
if(file_exists($d_sysrebootreqd_path)):
	$content->ins_info_box(get_std_save_message(0));
endif;
if(updatenotify_exists($sphere->get_notifier())):
	$content->ins_config_has_changed_box();
endif;
$table = $content->add_table_data_selection();
$table->ins_colgroup_with_styles('width',$a_col_width);
$thead = $table->addTHEAD();
$tbody = $table->addTBODY();
$tfoot = $table->addTFOOT();
$thead->ins_titleline(gtext('Overview'),$n_col_width);
$tr = $thead->addTR();
if($record_exists):
	$tr->
		push()->
		addTHwC('lhelc sorter-false parser-false')->
			ins_cbm_checkbox_toggle($sphere)->
		pop()->
		insTHwC('lhell',$cop->get_facility()->get_title())->
		insTHwC('lhell',$cop->get_level()->get_title())->
		insTHwC('lhell',$cop->get_value()->get_title())->
		insTHwC('lhelc sorter-false parser-false',gtext('Status'))->
		insTHwC('lhell',$cop->get_comment()->get_title())->
		insTHwC('lhebl sorter-false parser-false',gtext('Toolbox'));
else:
	$tr->
		insTHwC('lhelc')->
		insTHwC('lhell',$cop->get_facility()->get_title())->
		insTHwC('lhell',$cop->get_level()->get_title())->
		insTHwC('lhell',$cop->get_value()->get_title())->
		insTHwC('lhelc',gtext('Status'))->
		insTHwC('lhell',$cop->get_comment()->get_title())->
		insTHwC('lhebl',gtext('Toolbox'));
endif;
if($record_exists):
	foreach ($sphere->grid as $sphere->row):
		$notificationmode = updatenotify_get_mode($sphere->get_notifier(),$sphere->get_row_identifier_value());
		$is_notdirty = (UPDATENOTIFY_MODE_DIRTY != $notificationmode) && (UPDATENOTIFY_MODE_DIRTY_CONFIG != $notificationmode);
		$is_enabled = $sphere->enadis() ? (is_bool($test = $sphere->row[$cop->get_enable()->get_name()] ?? false) ? $test : true) : true;
		$is_notprotected = $sphere->lock() ? !(is_bool($test = $sphere->row[$cop->get_protected()->get_name()] ?? false) ? $test : true) : true;
		if($is_enabled):
			$src = $g_img['ena'];
			$title = gtext('Enabled');
			$dc = '';
		else:
			$src = $g_img['dis'];
			$title = gtext('Disabled');
			$dc = 'd';
		endif;
		$tbody->
			addTR()->
				push()->
				addTDwC('lcelc' . $dc)->
					ins_cbm_checkbox($sphere,!($is_notdirty && $is_notprotected))->
				pop()->
				insTDwC('lcell' . $dc,htmlspecialchars($sphere->row[$cop->get_facility()->get_name()] ?? ''))->
				insTDwC('lcell' . $dc,htmlspecialchars($sphere->row[$cop->get_level()->get_name()] ?? ''))->
				insTDwC('lcell' . $dc,htmlspecialchars($sphere->row[$cop->get_value()->get_name()] ?? ''))->
				push()->
				addTDwC('lcelc' . $dc)->
					addA(['title' => $title])->insIMG(['src' => $src,'alt' => '','class' => 'oneemhigh'])->
				pop()->
				insTDwC('lcell' . $dc,htmlspecialchars($sphere->row[$cop->get_comment()->get_name()] ?? ''))->
				add_toolbox_area()->
					ins_toolbox($sphere,$is_notprotected,$is_notdirty)->
					insTD()->
					insTD();
	endforeach;
else:
	$tbody->ins_no_records_found($n_col_width);
endif;
$tfoot->ins_record_add($sphere,$n_col_width);
$document->add_area_buttons()->ins_cbm_button_enadis($sphere)->ins_cbm_button_delete($sphere);
$document->render();
