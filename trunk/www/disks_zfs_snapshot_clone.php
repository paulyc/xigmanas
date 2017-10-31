<?php
/*
	disks_zfs_snapshot_clone.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2017 The NAS4Free Project <info@nas4free.org>.
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
require_once 'zfs.inc';

$pgtitle = [gtext('Disks'),gtext('ZFS'),gtext('Snapshots'),gtext('Clone')];

$a_snapshot = &array_make_branch($config,'zfs','snapshots','snapshot');
if(empty($a_snapshot)):
else:
	array_sort_key($a_snapshot,'name');
endif;

function get_zfs_clones() {
	$result = [];
	$cmd = 'zfs list -pH -o name,origin,creation -t filesystem,volume 2>&1';
	mwexec2($cmd,$rawdata);
	foreach($rawdata as $line):
		$a = preg_split('/\t/', $line);
		$r = [];
		$name = $a[0];
		$r['path'] = $name;
		if (preg_match('/^([^\/\@]+)(\/([^\@]+))?$/', $name, $m)) {
			$r['pool'] = $m[1];
		} else {
			$r['pool'] = 'unknown'; // XXX
		}
		$r['origin'] = $a[1];
		if ($r['origin'] == '-'):
			continue;
		endif;
		$r['creation'] = $a[2];
		$result[] = $r;
	endforeach;
	return $result;
}
$a_clone = get_zfs_clones();

if ($_POST) {
	$pconfig = $_POST;

	if (isset($_POST['apply']) && $_POST['apply']) {
		$ret = ['output' => [],'retval' => 0];

		if (!file_exists($d_sysrebootreqd_path)) {
			// Process notifications
			$ret = zfs_updatenotify_process("zfsclone", "zfsclone_process_updatenotification");

		}
		$savemsg = get_std_save_message($ret['retval']);
		if ($ret['retval'] == 0) {
			updatenotify_delete("zfsclone");
			header("Location: disks_zfs_snapshot_clone.php");
			exit;
		}
		updatenotify_delete("zfsclone");
		$errormsg = implode("\n", $ret['output']);
	}
}

if (isset($_GET['act']) && $_GET['act'] === "del") {
	$clone = [];
	$clone['path'] = $_GET['path'];
	updatenotify_set("zfsclone", UPDATENOTIFY_MODE_DIRTY, serialize($clone));
	header("Location: disks_zfs_snapshot_clone.php");
	exit;
}

function zfsclone_process_updatenotification($mode, $data) {
	global $config;

	$ret = ['output' => [],'retval' => 0];

	switch ($mode) {
		case UPDATENOTIFY_MODE_NEW:
			//$data = unserialize($data);
			//$ret = zfs_clone_configure($data);
			break;

		case UPDATENOTIFY_MODE_MODIFIED:
			//$data = unserialize($data);
			//$ret = zfs_clone_properties($data);
			break;

		case UPDATENOTIFY_MODE_DIRTY:
			$data = unserialize($data);
			$ret = zfs_clone_destroy($data);
			break;
	}

	return $ret;
}
?>
<?php include 'fbegin.inc';?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="disks_zfs_zpool.php"><span><?=gtext("Pools");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_dataset.php"><span><?=gtext("Datasets");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_volume.php"><span><?=gtext("Volumes");?></span></a></li>
				<li class="tabact"><a href="disks_zfs_snapshot.php" title="<?=gtext('Reload page');?>"><span><?=gtext("Snapshots");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_config.php"><span><?=gtext("Configuration");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav2">
				<li class="tabinact"><a href="disks_zfs_snapshot.php"><span><?=gtext("Snapshot");?></span></a></li>
				<li class="tabact"><a href="disks_zfs_snapshot_clone.php" title="<?=gtext('Reload page');?>"><span><?=gtext("Clone");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_snapshot_auto.php"><span><?=gtext("Auto Snapshot");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_snapshot_info.php"><span><?=gtext("Information");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="disks_zfs_snapshot_clone.php" method="post">
				<?php if (!empty($errormsg)) print_error_box($errormsg);?>
				<?php if (!empty($savemsg)) print_info_box($savemsg);?>
				<?php if (updatenotify_exists("zfsclone")) print_config_change_box();?>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<?php html_titleline2(gtext('Overview'), 4);?>
					<tr>
						<td width="30%" class="listhdrlr"><?=gtext("Path");?></td>
						<td width="40%" class="listhdrr"><?=gtext("Origin");?></td>
						<td width="20%" class="listhdrr"><?=gtext("Creation");?></td>
						<td width="10%" class="list"></td>
					</tr>
					<?php foreach ($a_clone as $clonev):?>
					<?php $notificationmode = updatenotify_get_mode("zfsclone", serialize(['path' => $clonev['path']]));?>
					<tr>
						<td class="listlr"><?=htmlspecialchars($clonev['path']);?>&nbsp;</td>
						<td class="listr"><?=htmlspecialchars($clonev['origin']);?>&nbsp;</td>
						<td class="listr"><?=htmlspecialchars(get_datetime_locale($clonev['creation']));?>&nbsp;</td>
						<?php if (UPDATENOTIFY_MODE_DIRTY != $notificationmode):?>
						<td valign="middle" nowrap="nowrap" class="list">
							&nbsp; &nbsp; &nbsp;
							<a href="disks_zfs_snapshot_clone.php?act=del&amp;path=<?=urlencode($clonev['path']);?>" onclick="return confirm('<?=gtext("Do you really want to delete this clone?");?>')"><img src="images/delete.png" title="<?=gtext("Delete clone");?>" border="0" alt="<?=gtext("Delete clone");?>" /></a>
						</td>
						<?php else:?>
						<td valign="middle" nowrap="nowrap" class="list">
							<img src="images/delete.png" border="0" alt="" />
						</td>
						<?php endif;?>
					</tr>
					<?php endforeach;?>
				</table>
				<?php include 'formend.inc';?>
			</form>
		</td>
	</tr>
</table>
<?php include 'fend.inc';?>
