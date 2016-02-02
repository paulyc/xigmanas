<?php
/*
	system_rcconf.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2016 The NAS4Free Project <info@nas4free.org>.
	All rights reserved.

	Portions of freenas (http://www.freenas.org).
	Copyright (c) 2005-2011 by Olivier Cochard <olivier@freenas.org>.
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
require("auth.inc");
require("guiconfig.inc");

$pgtitle = array(gettext("System"), gettext("Advanced"), gettext("rc.conf"));

if (false === (isset($config['system']['rcconf']['param']) && is_array($config['system']['rcconf']['param']))) {
	$config['system']['rcconf']['param'] = array();
}
$a_rcvar = &$config['system']['rcconf']['param'];
if (!empty($a_rcvar)) {
	$key1 = array_column($a_rcvar, "name");
	$key2 = array_column($a_rcvar, "uuid");
	array_multisort($key1, SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $key2, SORT_ASC, SORT_STRING | SORT_FLAG_CASE, $a_rcvar);
}

if ($_POST) {
	if (isset($_POST['apply']) && $_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			$retval |= updatenotify_process("rcconf", "rcconf_process_updatenotification");
			config_lock();
			$retval |= rc_exec_service("rcconf");
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			updatenotify_delete("rcconf");
		}
		header('Location: system_rcconf.php');
		exit;
	}
	if (isset($_POST['enable_selected_rows']) && $_POST['enable_selected_rows']) {
		$members = isset($_POST['members']) ? $_POST['members'] : array();
		$updateconfig = false;
		foreach ($members as $member) {
			if (false !== ($index = array_search_ex($member, $a_rcvar, "uuid"))) {
				if (false === isset($a_rcvar[$index]['enable'])) {
					$a_rcvar[$index]['enable'] = true;
					$updateconfig = true;
					$updatenotifymode = updatenotify_get_mode("rcconf", $a_rcvar[$index]['uuid']);
					switch ($updatenotifymode) {
						case UPDATENOTIFY_MODE_NEW:  
							break;
						case UPDATENOTIFY_MODE_MODIFIED:
							break;
						case UPDATENOTIFY_MODE_DIRTY:
							break;
						default:
							updatenotify_set("rcconf", UPDATENOTIFY_MODE_MODIFIED, $a_rcvar[$index]['uuid']);
							break;
					}
				}
			}
		}
		if (true === $updateconfig) {
			write_config();
			$updateconfig = false;
		}
		header('Location: system_rcconf.php');
		exit;
	}
	if (isset($_POST['disable_selected_rows']) && $_POST['disable_selected_rows']) {
		$members = isset($_POST['members']) ? $_POST['members'] : array();
		$updateconfig = false;
		foreach ($members as $member) {
			if (false !== ($index = array_search_ex($member, $a_rcvar, "uuid"))) {
				if (true === isset($a_rcvar[$index]['enable'])) {
					unset($a_rcvar[$index]['enable']);
					$updateconfig = true;
					$updatenotifymode = updatenotify_get_mode("rcconf", $a_rcvar[$index]['uuid']);
					switch ($updatenotifymode) {
						case UPDATENOTIFY_MODE_NEW:  
							break;
						case UPDATENOTIFY_MODE_MODIFIED:
							break;
						case UPDATENOTIFY_MODE_DIRTY:
							break;
						default:
							updatenotify_set("rcconf", UPDATENOTIFY_MODE_MODIFIED, $a_rcvar[$index]['uuid']);
							break;
					}
				}
			}
		}
		if (true === $updateconfig) {
			write_config();
			$updateconfig = false;
		}
		header('Location: system_rcconf.php');
		exit;
	}
	if (isset($_POST['toggle_selected_rows']) && $_POST['toggle_selected_rows']) {
		$members = isset($_POST['members']) ? $_POST['members'] : array();
		$updateconfig = false;
		foreach ($members as $member) {
			if (false !== ($index = array_search_ex($member, $a_rcvar, "uuid"))) {
				if (true === isset($a_rcvar[$index]['enable'])) {
					unset($a_rcvar[$index]['enable']);
				} else {
					$a_rcvar[$index]['enable'] = true;
				}
				$updateconfig = true;
				$updatenotifymode = updatenotify_get_mode("rcconf", $a_rcvar[$index]['uuid']);
				switch ($updatenotifymode) {
					case UPDATENOTIFY_MODE_NEW:
						break;
					case UPDATENOTIFY_MODE_MODIFIED:
						break;
					case UPDATENOTIFY_MODE_DIRTY:
						break;
					default:
						updatenotify_set("rcconf", UPDATENOTIFY_MODE_MODIFIED, $a_rcvar[$index]['uuid']);
						break;
				}
			}
		}
		if (true === $updateconfig) {
			write_config();
			$updateconfig = false;
		}
		header('Location: system_rcconf.php');
		exit;
	}
	if (isset($_POST['delete_selected_rows']) && $_POST['delete_selected_rows']) {
		$members = isset($_POST['members']) ? $_POST['members'] : array();
		foreach ($members as $member) {
			if (false !== ($index = array_search_ex($member, $a_rcvar, "uuid"))) {
				$updatenotifymode = updatenotify_get_mode("rcconf", $a_rcvar[$index]['uuid']);
				switch ($updatenotifymode) {
					case UPDATENOTIFY_MODE_NEW:  
						updatenotify_clear("rcconf", $a_rcvar[$index]['uuid']);
						updatenotify_set("rcconf", UPDATENOTIFY_MODE_DIRTY, $a_rcvar[$index]['uuid']);
						break;
					case UPDATENOTIFY_MODE_MODIFIED:
						updatenotify_clear("rcconf", $a_rcvar[$index]['uuid']);
						updatenotify_set("rcconf", UPDATENOTIFY_MODE_DIRTY, $a_rcvar[$index]['uuid']);
						break;
					case UPDATENOTIFY_MODE_DIRTY:
						break;
					default:
						updatenotify_set("rcconf", UPDATENOTIFY_MODE_DIRTY, $a_rcvar[$index]['uuid']);
						break;
				}
			}
		}
		header('Location: system_rcconf.php');
		exit;
	}
}

function rcconf_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFY_MODE_NEW:
		case UPDATENOTIFY_MODE_MODIFIED:
			break;
		case UPDATENOTIFY_MODE_DIRTY:
			if (is_array($config['system']['rcconf']['param'])) {
				$index = array_search_ex($data, $config['system']['rcconf']['param'], "uuid");
				if (false !== $index) {
					mwexec2("/usr/local/sbin/rconf attribute remove {$config['system']['rcconf']['param'][$index]['name']}");
					unset($config['system']['rcconf']['param'][$index]);
					write_config();
				}
			}
			break;
	}
	return $retval;
}
?>
<?php include("fbegin.inc");?>
<script type="text/javascript">
<!-- Begin JavaScript
function togglecheckboxesbyname(ego, byname) {
	var a_members = document.getElementsByName(byname);
	var i;
	for (i = 0; i < a_members.length; i++) {
		if (a_members[i].type === 'checkbox') {
			if (a_members[i].disabled == false) {
				a_members[i].checked = !a_members[i].checked;
			}
		}
	}
	if (ego.type == 'checkbox') {
		ego.checked = false;
	}
}
// End JavaScript -->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="system_advanced.php"><span><?=gettext("Advanced");?></span></a></li>
				<li class="tabinact"><a href="system_email.php"><span><?=gettext("Email");?></span></a></li>
				<li class="tabinact"><a href="system_proxy.php"><span><?=gettext("Proxy");?></span></a></li>
				<li class="tabinact"><a href="system_swap.php"><span><?=gettext("Swap");?></span></a></li>
				<li class="tabinact"><a href="system_rc.php"><span><?=gettext("Command scripts");?></span></a></li>
				<li class="tabinact"><a href="system_cron.php"><span><?=gettext("Cron");?></span></a></li>
				<li class="tabinact"><a href="system_loaderconf.php"><span><?=gettext("loader.conf");?></span></a></li>
				<li class="tabact"><a href="system_rcconf.php" title="<?=gettext("Reload page");?>"><span><?=gettext("rc.conf");?></span></a></li>
				<li class="tabinact"><a href="system_sysctl.php"><span><?=gettext("sysctl.conf");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="system_rcconf.php" method="post">
				<?php
					if (!empty($savemsg)) {
						print_info_box($savemsg);
					} else {
						if (file_exists($d_sysrebootreqd_path)) {
							print_info_box(get_std_save_message(0));
						}
					}
				?>
				<?php if (updatenotify_exists("rcconf")) { print_config_change_box(); }?>
				<div id="submit">
					<input name="enable_selected_rows" type="submit" class="formbtn" value="<?=gettext("Enable Selected Options");?>" onclick="return confirm('<?=gettext("Do you want to enable selected options?"); ?>')" />
					<input name="disable_selected_rows" type="submit" class="formbtn" value="<?=gettext("Disable Selected Options");?>" onclick="return confirm('<?= gettext("Do you want to disable selected options?"); ?>')" />
					<input name="toggle_selected_rows" type="submit" class="formbtn" value="<?=gettext("Toggle Selected Options");?>" onclick="return confirm('<?= gettext("Do you want to toggle selected options?"); ?>')" />
					<input name="delete_selected_rows" type="submit" class="formbtn" value="<?=gettext("Delete Selected Options");?>" onclick="return confirm('<?= gettext("Do you want to delete selected options?"); ?>')" />
				</div>
				<br />
				<br />
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
					<td width="1%" class="listhdrlr"><input type="checkbox" name="togglemembers" onclick="javascript:togglecheckboxesbyname(this,'members[]')"/></td>
						<td width="34%" class="listhdrlr"><?=gettext("Variable");?></td>
						<td width="20%" class="listhdrr"><?=gettext("Value");?></td>
						<td width="5%" class="listhdrr"><?=gettext("Status");?></td>
						<td width="30%" class="listhdrr"><?=gettext("Comment");?></td>
						<td width="10%" class="list"></td>
					</tr>
					<?php foreach ($a_rcvar as $r_rcvar):?>
						<tr>
							<?php $notificationmode = updatenotify_get_mode("rcconf", $r_rcvar['uuid']);?>
							<?php $enable = isset($r_rcvar['enable']);?>
							<?php if (UPDATENOTIFY_MODE_DIRTY != $notificationmode):?>
								<td class="<?=$enable ? "listlr" : "listlrd";?>"><input type="checkbox" name="members[]" value="<?=$r_rcvar['uuid'];?>" id="<?=$r_rcvar['uuid'];?>"/></td>
							<?php else:?>
								<td class="<?=$enable ? "listlr" : "listlrd";?>"><input type="checkbox" name="members[]" value="<?=$r_rcvar['uuid'];?>" id="<?=$r_rcvar['uuid'];?>" disabled="disabled"/></td>
							<?php endif;?>
							<td class="<?=$enable ? "listlr" : "listlrd";?>"><?=htmlspecialchars($r_rcvar['name']);?>&nbsp;</td>
							<td class="<?=$enable ? "listr" : "listrd";?>"><?=htmlspecialchars($r_rcvar['value']);?>&nbsp;</td>
							<td class="<?=$enable ? "listr" : "listrd";?>">
							<?php if ($enable): ?>
								<a title="<?=gettext("Enabled");?>"><img src="status_enabled.png" border="0" alt=""/></a>
							<?php else:?>
								<a title="<?=gettext("Disabled");?>"><img src="status_disabled.png" border="0" alt=""/></a>
							<?php endif;?>
							</td>
							<td class="listbg"><?= htmlspecialchars($r_rcvar['comment']);?>&nbsp;</td>
							<?php if (UPDATENOTIFY_MODE_DIRTY != $notificationmode):?>
								<td valign="middle" nowrap="nowrap" class="list">
									<a href="system_rcconf_edit.php?uuid=<?=$r_rcvar['uuid'];?>"><img src="e.gif" title="<?=gettext("Edit option");?>" border="0" alt="<?=gettext("Edit option");?>" /></a>
								</td>
							<?php else:?>
								<td valign="middle" nowrap="nowrap" class="list">
									<img src="del.gif" border="0" alt="" />
								</td>
							<?php endif;?>
						</tr>
					<?php endforeach;?>
					<tr>
						<td class="list" colspan="5"></td>
						<td class="list">
							<a href="system_rcconf_edit.php"><img src="plus.gif" title="<?=gettext("Add option");?>" border="0" alt="<?=gettext("Add option");?>" /></a>
						</td>
					</tr>
				</table>
				<div id="remarks">
					<?php html_remark("note", gettext("Note"), gettext("These option(s) will be added to /etc/rc.conf. This allow you to overwrite options used by various generic startup scripts."));?>
				</div>
				<?php include("formend.inc");?>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
