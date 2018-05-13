<?php
/*
	status_graph_cpu_freq.php

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

array_make_branch($config,'rrdgraphs');

$rrd_cpu_freq = true;
$refresh = 300;
if(isset($config['rrdgraphs']['refresh_time'])):
	if(!empty($config['rrdgraphs']['refresh_time'])):
		$refresh = $config['rrdgraphs']['refresh_time'];
	endif;
endif;
mwexec('/usr/local/share/rrdgraphs/rrd-graph.sh frequency',true);
$pgtitle = [gtext('Status'),gtext('Monitoring'),gtext('CPU Frequency')];
?>
<?php
include 'fbegin.inc';
?>
<meta http-equiv="refresh" content="<?=$refresh?>">
<table id="area_navigator"><tbody>
	<tr><td class="tabnavtbl"><ul id="tabnav">
<?php
		include 'status_graph_tabs.inc';
?>
	</ul></td></tr>
</tbody></table>
<table id="area_data"><tbody><tr><td id="area_data_frame">
	<table class="area_data_settings">
		<colgroup>
			<col style="width:100%">
		</colgroup>
		<thead>
<?php
			html_titleline(gtext('CPU Frequency'),1);
?>
		</thead>
		<tbody>
			<tr><td><?=sprintf(gtext('Graph updates every %d seconds.'),$refresh);?></td></tr>
			<tr><td>
				<div align="center" style="min-width:840px;">
					<br>
					<img src="/images/rrd/rrd-cpu_freq_daily.png?rand=<?=time()?>" alt="RRDGraphs Daily CPU frequency Graph">
					<br><br>
					<img src="/images/rrd/rrd-cpu_freq_weekly.png?rand=<?=time()?>" alt="RRDGraphs Weekly CPU frequency Graph">
					<br><br>
					<img src="/images/rrd/rrd-cpu_freq_monthly.png?rand=<?=time()?>" alt="RRDGraphs Monthly CPU frequency Graph">
					<br><br>
					<img src="/images/rrd/rrd-cpu_freq_yearly.png?rand=<?=time()?>" alt="RRDGraphs Yearly CPU frequency Graph">
				</div>
			</td></tr>
		</tbody>
	</table>
</td></tr></tbody></table>
<?php
include 'fend.inc';
?>