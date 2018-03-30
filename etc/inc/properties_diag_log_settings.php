<?php
/*
	properties_diag_log_settings.php

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
require_once 'properties.php';

class properties_diag_log_settings extends co_property_container {
	protected $x_daemon;
	protected $x_disablecomp;
	protected $x_disablesecure;
	protected $x_enable;
	protected $x_ftp;
	protected $x_ipaddr;
	protected $x_nentries;
	protected $x_port;
	protected $x_resolve;
	protected $x_reverse;
	protected $x_rsyncd;
	protected $x_smartd;
	protected $x_sshd;
	protected $x_system;
	
	public function get_daemon() {
		return $this->x_daemon ?? $this->init_daemon();
	}
	public function init_daemon() {
		$property = $this->x_daemon = new property_bool($this);
		$caption = gtext('Send daemon event messages.');
		$description = '';
		$property->
			set_name('daemon')->
			set_title(gtext('Daemon Events'));
		$property->
			set_id('daemon')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function get_disablecomp() {
		return $this->x_disablecomp ?? $this->init_disablecomp();
	}
	public function init_disablecomp() {
		$property = $this->x_disablecomp = new property_bool($this);
		$caption = gtext('Disable the compression of repeating lines.');
		$description = '';
		$property->
			set_name('disablecomp')->
			set_title(gtext('Compression'));
		$property->
			set_id('disablecomp')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function get_disablesecure() {
		return $this->x_disablesecure ?? $this->init_disablesecure();
	}
	public function init_disablesecure() {
		$property = $this->x_disablesecure = new property_bool($this);
		$caption = gtext('Accept remote syslog messages.');
		$description = '';
		$property->
			set_name('disablesecure')->
			set_title(gtext('Remote Syslog Messages'));
		$property->
			set_id('disablesecure')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function get_enable() {
		return $this->x_enable ?? $this->init_enable();
	}
	public function init_enable() {
		$property = $this->x_enable = new property_bool($this);
		$caption = gtext('Enable');
		$description = '';
		$property->
			set_name('enable')->
			set_title(gtext('Enable Server'));
		$property->
			set_id('enable')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function get_ftp() {
		return $this->x_ftp ?? $this->init_ftp();
	}
	public function init_ftp() {
		$property = $this->x_ftp = new property_bool($this);
		$caption = gtext('Send FTP event messages.');
		$description = '';
		$property->
			set_name('ftp')->
			set_title(gtext('FTP Events'));
		$property->
			set_id('ftp')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function get_ipaddr() {
		return $this->x_ipaddr ?? $this->init_ipaddr();
	}
	public function init_ipaddr() {
		$property = $this->x_ipaddr = new property_ipaddress($this);
		$description = gtext('IP address of the remote syslog server.');
		$property->
			set_name('ipaddr')->
			set_title(gtext('IP Address'));
		$property->
			set_id('ipaddr')->
			set_description($description)->
			set_defaultvalue('')->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('This is not a valid IP Address.')));
		return $property;
	}
	public function get_nentries() {
		return $this->x_nentries ?? $this->init_nentries();
	}
	public function init_nentries() {
		$property = $this->x_nentries = new property_int($this);
		$description = '';
		$property->
			set_name('nentries')->
			set_title(gtext('Show Log Entries'));
		$property->
			set_id('nentries')->
			set_description($description)->
			set_defaultvalue(50)->
			set_size(5)->
			set_maxlength(4)->
			set_min(5)->set_max(1000)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('Must be a number between 5 and 1000.')));
		return $property;
	}
	public function get_port() {
		return $this->x_port ?? $this->init_port();
	}
	public function init_port() {
		$property = $this->x_port = new property_int($this);
		$caption = gtext('Port of the remote syslog server. Leave blank to use the default port.');
		$description = gtext('Syslog sends UDP datagrams to port 514 on the specified remote syslog server. Be sure to set syslogd on the remote server to accept syslog messages from this server.');
		$property->
			set_name('port')->
			set_title(gtext('Port'));
		$property->
			set_id('port')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue('')->
			set_size(6)->
			set_maxlength(5)->
			set_min(1024)->set_max(49151)->
			set_placeholder(gtext('514'))->
			filter_use_default()->
			set_filter(FILTER_VALIDATE_INT,'514')->
			set_filter_flags(FILTER_REQUIRE_SCALAR,'514')->
			set_filter_options(['default' => NULL,'min_range' => 514,'max_range' => 514],'514')->
			set_filter(FILTER_VALIDATE_REGEXP,'empty')->
			set_filter_flags(FILTER_REQUIRE_SCALAR,'empty')->
			set_filter_options(['default' => NULL,'regexp' => '/^$/'],'empty')->
			set_filter(FILTER_UNSAFE_RAW,'scalar')->
			set_filter_flags(FILTER_REQUIRE_SCALAR,'scalar')->
			set_filter_options(['default' => ''],'scalar')->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('Port number must be 514 or a number between 1024 and 49151.')));
		return $property;
	}
	public function get_resolve() {
		return $this->x_resolve ?? $this->init_resolve();
	}
	public function init_resolve() {
		$property = $this->x_resolve = new property_bool($this);
		$caption = gtext('Resolve IP addresses to hostnames.');
		$description = [
			gtext('Hint'),
			[': ',true],
			[gtext('If this option is checked, IP addresses in the server logs are resolved to their hostnames where possible.'),true],
			[gtext('Warning'),'red'],
			[': ',true],
			[gtext('This can cause a huge delay in loading the log page!'),true]
		];
		$property->
			set_name('resolve')->
			set_title(gtext('Resolve IP'));
		$property->
			set_id('resolve')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function get_reverse() {
		return $this->x_reverse ?? $this->init_reverse();
	}
	public function init_reverse() {
		$property = $this->x_reverse = new property_bool($this);
		$caption = gtext('Show log entries in reverse order (newest entries on top).');
		$description = '';
		$property->
			set_name('reverse')->
			set_title(gtext('Log Order'));
		$property->
			set_id('reverse')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function get_rsyncd() {
		return $this->x_rsyncd ?? $this->init_rsyncd();
	}
	public function init_rsyncd() {
		$property = $this->x_rsyncd = new property_bool($this);
		$caption = gtext('Send RSYNC event messages.');
		$description = '';
		$property->
			set_name('rsyncd')->
			set_title(gtext('RSYNC Events'));
		$property->
			set_id('rsyncd')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function get_smartd() {
		return $this->x_smartd ?? $this->init_smartd();
	}
	public function init_smartd() {
		$property = $this->x_smartd = new property_bool($this);
		$caption = gtext('Send S.M.A.R.T. event messages.');
		$description = '';
		$property->
			set_name('smartd')->
			set_title(gtext('S.M.A.R.T. Events'));
		$property->
			set_id('smartd')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function get_sshd() {
		return $this->x_sshd ?? $this->init_sshd();
	}
	public function init_sshd() {
		$property = $this->x_sshd = new property_bool($this);
		$caption = gtext('Send SSH event messages.');
		$description = '';
		$property->
			set_name('sshd')->
			set_title(gtext('SSH Events'));
		$property->
			set_id('sshd')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
	public function get_system() {
		return $this->x_system ?? $this->init_system();
	}
	public function init_system() {
		$property = $this->x_system = new property_bool($this);
		$caption = gtext('Send system event messages.');
		$description = '';
		$property->
			set_name('system')->
			set_title(gtext('System Events'));
		$property->
			set_id('system')->
			set_caption($caption)->
			set_description($description)->
			set_defaultvalue(false)->
			filter_use_default()->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_message_error(sprintf('%s: %s',$property->get_title(),gtext('The value is invalid.')));
		return $property;
	}
}
