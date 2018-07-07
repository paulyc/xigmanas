<?php
/*
	email.inc

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
require_once 'config.inc';
require_once 'util.inc';
require_once 'system.inc';

/**
 *	Checks if email settings are valid.
 * @global array $config
 * @return int Return 0 if successful, otherwise 1.
 */
function email_validate_settings() {
	global $config;
	$email = array_make_branch($config,'system','email');
	if(empty($email['server']) || empty($email['port']) || empty($email['security']) || empty($email['from'])):
		return 1;
	endif;
	if(isset($email['auth'])):
		if(empty($email['username']) || empty($email['password'])):
			return 1;
		endif;
	endif;
	return 0;
}
/**
 *	Send an email.
 *	@global array $config
 *	@param string $address The email adress
 *	@param string $subject The email subject
 *	@param string $message The message
 *	@param string $error Contains an error message if submission fails.
 *	@return int Return 0 if successful, otherwise 1.
 */
function email_send($address,$subject,$message,&$error) {
	global $config;
	$msgfilepath = '/tmp/message';
	//	Get date in RFC 2882 format.
	$rfcdate = date('r');
	//	Get the language configured.
	$preferences = [
		'input-charset' => 'UTF-8',
		'output-charset' => 'UTF-8',
		'line-length' => 76,
		'line-break-chars' => PHP_EOL
	];
	//	Substitute special characters in subject.
	$subject = str_replace('%d',trim(shell_exec('date')),$subject);
	$subject = str_replace('%h',system_get_hostname(),$subject); 
	$subject = iconv_mime_encode('Subject',$subject,$preferences);
	//	Convert adresses for msmtp.
	$address = strtr($address, [';' => ' ',',' => ' ']);
	//	Generate message content.
	$data = '';
	foreach(explode(' ',$address) as $addressv):
		$data .= 'To: ' . $addressv . PHP_EOL;
	endforeach;
	$data .= 'From: ' . $config['system']['email']['from'] . PHP_EOL;
	$data .= $subject . PHP_EOL;
	$data .= 'Date: ' . $rfcdate . PHP_EOL;
	$data .= 'MIME-Version: 1.0' . PHP_EOL;
	$data .= 'Content-Type: text/plain; charset=UTF-8' . PHP_EOL;
	$data .= 'Content-Transfer-Encoding: 8bit' . PHP_EOL;
	$data .= PHP_EOL;
	$data .= $message;
	$data .= PHP_EOL;
	//	Write message to file.
	if(false === file_put_contents($msgfilepath,$data)):
		write_log(sprintf("Error: Failed to create '%s'",$msgfilepath));
		return 1;
	endif;
	//	Send email via external SMTP client.
	$cmd = sprintf('/usr/local/bin/msmtp --file=/var/etc/msmtp.conf %s < %s',$address,$msgfilepath);
	mwexec2($cmd,$output,$retval);
	if(0 !== $retval):
		$error = $output;
	endif;
	//	Cleanup message.
	@unlink($msgfilepath);
	return $retval;
}