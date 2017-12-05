<?php
/*
	wui2.php

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
require_once 'config.inc';
require_once 'array.inc';

class HTMLBaseControl2 {
	var $_ctrlname = '';
	var $_title = '';
	var $_description = '';
	var $_value;
	var $_required = false;
	var $_readonly = false;
	var $_altpadding = false;
	var $_classtag = 'celltag';
	var $_classdata = 'celldata';
	var $_classaddonrequired = 'req';
	var $_classaddonpadalt = 'alt';
	//	constructor
	public function __construct($ctrlname,$title,$value,$description = '') {
		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetDescription($description);
		$this->SetValue($value);
	}
	//	get/set methods
	function SetAltPadding($bool) {
		$this->_altpadding = $bool;
	}
	function GetAltPadding() {
		return $this->_altpadding;
	}
	function SetClassAddonRequired($cssclass) {
		$this->_classaddonrequired = $cssclass;
	}
	function GetClassAddonRequired() {
		return $this->_classaddonrequired;
	}
	function SetClassAddonPadAlt($cssclass) {
		$this->_classaddonpadalt = $cssclass;
	}
	function GetClassAddonPadAlt() {
		return $this->_classaddonpadalt;
	}
	function SetClassData($cssclass) {
		$this->_classdata = $cssclass;
	}
	function GetClassData() {
		return $this->_classdata;
	}
	function SetClassTag($cssclass) {
		$this ->_classtag = $cssclass;
	}
	function GetClassTag() {
		return $this->_classtag;
	}
	function SetCtrlName($name) {
		$this->_ctrlname = $name;
	}
	function GetCtrlName() {
		return $this->_ctrlname;
	}
	function SetDescription($description) {
		$this->_description = $description;
	}
	function GetDescription() {
		return $this->_description;
	}
	function SetReadOnly($bool) {
		$this->_readonly = $bool;
	}
	function GetReadOnly() {
		return $this->_readonly;
	}
	function SetRequired($bool) {
		$this->_required = $bool;
	}
	function GetRequired() {
		return $this->_required;
	}
	function SetTitle($title) {
		$this->_title = $title;
	}
	function GetTitle() {
		return $this->_title;
	}
	function SetValue($value) {
		$this->_value = $value;
	}
	function GetValue() {
		return $this->_value;
	}
	//	support methods
	function GetClassOfTag() {
		$class = $this->GetClassTag();
		if(true === $this->GetRequired()):
			$class .= $this->GetClassAddonRequired();
		endif;
		if(true === $this->GetAltPadding()):
			$class .= $this->GetClassAddonPadAlt();
		endif;
		return $class;
	}
	function GetClassOfData() {
		$class = $this->GetClassData();
		if(true === $this->GetRequired()):
			$class .= $this->GetClassAddonRequired();
		endif;
		if(true === $this->GetAltPadding()):
			$class .= $this->GetClassAddonPadAlt();
		endif;
		return $class;
	}
	function GetDescriptionOutput() {
		//	description:
		//	string
		//	[string, ...]
		//	[ [string], ...]
		//	[ [string,no_br], ...]
		//	[ [string,color], ...]
		//	[ [string,color,no_br], ...]
		$description = $this->GetDescription();
		$description_output = '';
		$suppressbr = true;
		if(!empty($description)): // string or array
			if(is_string($description)):
				$description_output = $description;
			elseif(is_array($description)):
				foreach($description as $description_row):
					if(is_string($description_row)):
						if($suppressbr):
							$description_output .= $description_row;
							$suppressbr = false;
						else:
							$description_output .= ('<br />' . $description_row);
						endif;
					elseif(is_array($description_row)):
						switch(count($description_row)):
							case 1:
								if(is_string($description_row[0])):
									if($suppressbr):
										$suppressbr = false;
									else:
										$description_output .= '<br />';
									endif;
									$description_output .= $description_row[0];
								endif;
								break;
							case 2:
								if(is_string($description_row[0])):
									$color = NULL;
									if(is_string($description_row[1])):
										$color = $description_row[1];
									endif;
									if(is_bool($description_row[1])):
										$suppressbr = $description_row[1];
									endif;
									if($suppressbr):
										$suppressbr = false;
									else:
										$description_output .= '<br />';
									endif;
									if(is_null($color)):
										$description_output .= $description_row[0];
									else:
										$description_output .= sprintf('<span style="color:%2$s">%1$s</span>',$description_row[0],$color);
									endif;
								endif;
								break;
							case 3: // allow not to break
								if(is_string($description_row[0])):
									$color = NULL;
									if(is_string($description_row[1])):
										$color = $description_row[1];
									endif;
									if(is_bool($description_row[2])):
										$suppressbr = $description_row[2];
									endif;
									if($suppressbr):
										$suppressbr = false;
									else:
										$description_output .= '<br />';
									endif;
									if(is_null($color)):
										$description_output .= $description_row[0];
									else:
										$description_output .= sprintf('<span style="color:%2$s">%1$s</span>',$description_row[0],$color);
									endif;
								endif;
								break;
						endswitch;
					endif;
				endforeach;
			endif;
		endif;
		return $description_output;
	}
	function Compose(DOMNode &$anchor = NULL) {
		//	create root DOM if anchor not provided
		if(is_null($anchor)):
			$anchor = new co_DOMDocument();
		endif;
		$description = $this->GetDescriptionOutput();
		//	compose
		$attributes = ['id' => sprintf('%s_tr',$this->GetCtrlName())];
		$tr = $anchor->addElement('tr',$attributes);
		$attributes = ['class' => $this->GetClassOfTag()];
//		if($this->GetReadOnly()):
			$tr->addElement('td',$attributes,$this->GetTitle());
//		else:
//			$tdtag = $tr->addElement('td',$attributes);
//			$attributes = ['for' => $ctrlname];
//			$tdtag->addElement('label',$attributes,$this->GetTitle());
//		endif;
		$attributes = ['class' => $this->GetClassOfData()];
		$tddata = $tr->addElement('td',$attributes);
		$this->ComposeInner($tddata);
		if(!empty($description)):
			$attributes = ['class' => 'formfldadditionalinfo'];
			$tddata->addElement('div',$attributes,$description);
		endif;
		return $anchor;
	}
	function ComposeInner(&$anchor) {
	}
}
class HTMLBaseControlJS2 extends HTMLBaseControl2 {
	var $_onclick = '';
	function SetJSonClick($code) {
		$this->_onclick = $code;
	}
	function GetJSonClick() {
		return $this->_onclick;
	}
}
class HTMLEditBox2 extends HTMLBaseControl2 {
	var $_size = 40;
	var $_maxlength = 0;
	var $_placeholder = '';
	var $_classinputtext = 'formfld';
	var $_classinputtextro = 'formfldro';

	//	constructor
	function __construct($ctrlname,$title,$value,$description,$size) {
		parent::__construct($ctrlname,$title,$value,$description);
		$this->SetSize($size);
	}
	//	get/set methods
	function SetClassInputText($param) {
		$this->_classinputtext = $param;
	}
	function GetClassInputText() {
		return $this->_classinputtext;
	}
	function SetClassInputTextRO($param) {
		$this->_classinputtextro = $param;
	}
	function GetClassInputTextRO() {
		return $this->_classinputtextro;
	}
	function SetMaxLength($maxlength) {
		$this->_maxlength = $maxlength;
	}
	function GetMaxLength() {
		return $this->_maxlength;
	}
	function SetPlaceholder(string $placeholder = '') {
		$this->_placeholder = $placeholder;
	}
	function GetPlaceholder() {
		return $this->_placeholder;
	}
	function SetSize($size) {
		$this->_size = $size;
	}
	function GetSize() {
		return $this->_size;
	}
	//	support methods
	function GetAttributes(array &$attributes = []) {
		if(true === $this->GetReadOnly()):
			$attributes['readonly'] = 'readonly';
		endif;
		$tagval = $this->GetPlaceholder();
		if(preg_match('/\S/',$tagval)):
			$attributes['placeholder'] = $tagval;
		endif;
		$tagval = $this->GetMaxLength();
		if($tagval > 0):
			$attributes['maxlength'] = $tagval;
		endif;
		return $attributes;
	}
	function GetClassOfInputText() {
		if(true === $this->GetReadOnly()):
			return $this->GetClassInputTextRO();
		else:
			return $this->GetClassInputText();
		endif;
	}
	function ComposeInner(&$anchor) {
		$attributes = [
			'type' => 'text',
			'id' => $this->GetCtrlName(),
			'name' => $this->GetCtrlName(),
			'class' => $this->GetClassOfInputText(),
			'size' => $this->GetSize(),
			'value' => $this->GetValue()
		];
		$this->GetAttributes($attributes);
		$anchor->addElement('input',$attributes);
	}
}
class HTMLPasswordBox2 extends HTMLEditBox2 {
	var $_classinputpassword = 'formfld';
	//	get/set methods
	function SetClassInputPassword($cssclass) {
		$this->_classinputpassword = $cssclass;
	}
	function GetClassInputPassword() {
		return $this->_classinputpassword;
	}
	//	support methods
	function GetClassOfInputPassword() {
		return $this->GetClassInputPassword();
	}
	function ComposeInner(&$anchor) {
		$ctrlname = $this->GetCtrlName();
		$attributes = [
			'type' => 'password',
			'id' => $ctrlname,
			'name' => $ctrlname,
			'class' => $this->GetClassOfInputPassword(),
			'size' => $this->GetSize(),
			'value'=> $this->GetValue()
		];
		$this->GetAttributes($attributes);
		$anchor->addElement('input',$attributes);
	}
}
class HTMLPasswordConfBox2 extends HTMLEditBox2 {
	var $_ctrlnameconf = '';
	var $_valueconf = '';
	var $_classinputpassword = 'formfld';
	var $_placeholderconfirm = '';
	//	constructor
	function __construct($ctrlname,$ctrlnameconf,$title,$value,$valueconf,$description,$size) {
		parent::__construct($ctrlname,$title,$value,$description,$size);
		$this->SetCtrlNameConf($ctrlnameconf);
		$this->SetValueConf($valueconf);
	}
	//	get/set methods
	function SetClassInputPassword($cssclass) {
		$this->_classinputpassword = $cssclass;
	}
	function GetClassInputPassword() {
		return $this->_classinputpassword;
	}
	function SetCtrlNameConf($name) {
		$this->_ctrlnameconf = $name;
	}
	function GetCtrlNameConf() {
		return $this->_ctrlnameconf;
	}
	function SetPlaceholderConfirm(string $placeholder = '') {
		$this->_placeholderconfirm = $placeholder;
	}
	function GetPlaceholderConfirm() {
		return $this->_placeholderconfirm;
	}
	function SetValueConf($value) {
		$this->_valueconf = $value;
	}
	function GetValueConf() {
		return $this->_valueconf;
	}
	//	support methods
	function GetAttributesConfirm(array &$attributes = []) {
		$attributes = $this->GetAttributes($attributes);
		$tagval = $this->GetPlaceholderConfirm();
		if(preg_match('/\S/',$tagval)):
			$attributes['placeholder'] = $tagval;
		endif;
		return $attributes;
	}
	function GetClassOfInputPassword() {
		return $this->GetClassInputPassword();
	}
	function ComposeInner(&$anchor) {
		$ctrlname = $this->GetCtrlName();
		$ctrlnameconf = $this->GetCtrlNameConf();
		$attributes = [
			'type' => 'password',
			'id' => $ctrlname,
			'name' => $ctrlname,
			'class' => $this->GetClassOfInputPassword(),
			'size' => $this->GetSize(),
			'value'=> $this->GetValue()
		];
		$this->GetAttributes($attributes);
		$o_div1 = $anchor->addElement('div');
		$o_div1->addElement('input',$attributes);
		$attributes = [
			'type' => 'password',
			'id' => $ctrlnameconf,
			'name' => $ctrlnameconf,
			'class' => $this->GetClassOfInputPassword(),
			'size' => $this->GetSize(),
			'value' => $this->GetValueConf()
		];
		$this->GetAttributesConfirm($attributes);
		$o_div2 = $anchor->addElement('div');
		$o_div2->addElement('input',$attributes);
	}
}
class HTMLTextArea2 extends HTMLEditBox2 {
	var $_columns = 40;
	var $_rows = 5;
	var $_wrap = true;
	var $_classtextarea = 'formpre';
	var $_classtextarearo = 'formprero';
	//	constructor
	function __construct($ctrlname,$title,$value,$description,$columns,$rows) {
		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetValue($value);
		$this->SetDescription($description);
		$this->SetColumns($columns);
		$this->SetRows($rows);
	}
	//	get/set methods
	function SetClasstextarea($cssclass) {
		$this->_classtextarea = $cssclass;
	}
	function GetClassTextarea() {
		return $this->_classtextarea;
	}
	function SetClasstextareaRO($cssclass) {
		$this->_classtextarearo = $cssclass;
	}
	function GetClassTextareaRO() {
		return $this->_classtextarearo;
	}
	function SetColumns($columns) {
		$this->_columns = $columns;
	}
	function GetColumns() {
		return $this->_columns;
	}
	function SetRows($rows) {
		$this->_rows = $rows;
	}
	function GetRows() {
		return $this->_rows;
	}
	function SetWrap($bool) {
		$this->_wrap = $bool;
	}
	function GetWrap() {
		return $this->_wrap;
	}
	//	support methods
	function GetAttributes(array &$attributes = []) {
		parent::GetAttributes($attributes);
		if(false === $this->GetWrap()):
			$attributes['wrap'] = 'soft';
		else:
			$attributes['wrap'] = 'hard';
		endif;
		return $attributes;
	}
	function GetClassOfTextarea() {
		return ($this->GetReadOnly() ? $this->GetClassTextareaRO() : $this->GetClassTextarea());
	}
	function ComposeInner(&$anchor) {
		$ctrlname = $this->GetCtrlName();
		$attributes = [
			'id' => $ctrlname,
			'name' => $ctrlname,
			'class' => $this->GetClassOfTextarea(),
			'cols' => $this->GetColumns(),
			'rows' => $this->GetRows()
		];
		$this->GetAttributes($attributes);
		$anchor->addElement('textarea',$attributes,htmlspecialchars($this->GetValue(),ENT_QUOTES));
	}
}
class HTMLFileChooser2 extends HTMLEditBox2 {
	var $_path = '';
	function __construct($ctrlname,$title,$value,$description,$size = 60) {
		parent::__construct($ctrlname,$title,$value,$description,$size);
	}
	function SetPath($path) {
		$this->_path = $path;
	}
	function GetPath() {
		return $this->_path;
	}
	function ComposeInner(&$anchor) {
		//	helper variables
		$ctrlname = $this->GetCtrlName();
		$size = $this->GetSize();
		//	input element
		$attributes = [
			'type' => 'text',
			'id' => $ctrlname,
			'name' => $ctrlname,
			'class' => 'formfld',
			'value' => $this->GetValue(),
			'size' => $size
		];
		$this->GetAttributes($attributes);
		$anchor->addElement('input',$attributes);
		//	file chooser
		$js = sprintf('%1$sifield = form.%1$s;',$ctrlname)
			. 'filechooser = window.open("filechooser.php?p="+'
			. sprintf('encodeURIComponent(%sifield.value)+',$ctrlname)
			. sprintf('"&sd=%s",',$this->GetPath())
			. '"filechooser",'
			. '"scrollbars=yes,toolbar=no,menubar=no,statusbar=no,width=550,height=300");'
			. sprintf('filechooser.ifield = %sifield;',$ctrlname)
			. sprintf('window.ifield = %sifield;',$ctrlname);
		$attributes = [
			'type' => 'button',
			'id' => $ctrlname . 'browsebtn',
			'name' => $ctrlname . 'browsebtn',
			'class' => 'formbtn',
			'value' => $value,
			'size' => $size,
			'onclick' => $js,
			'value' => '...'
		];
		$this->GetAttributes($attributes);
		$anchor->addElement('input',$attributes);
	}
}
class HTMLIPAddressBox2 extends HTMLEditBox2 {
	var $_ctrlnamenetmask = '';
	var $_valuenetmask = '';
	//	constructor
	function __construct($ctrlname,$ctrlnamenetmask,$title,$value,$valuenetmask,$description) {
		$this->SetCtrlName($ctrlname);
		$this->SetCtrlNameNetmask($ctrlnamenetmask);
		$this->SetTitle($title);
		$this->SetValue($value);
		$this->SetValueNetmask($valuenetmask);
		$this->SetDescription($description);
	}
	//	get/set Methods
	function SetCtrlNameNetmask($name) {
		$this->_ctrlnamenetmask = $name;
	}
	function GetCtrlNameNetmask() {
		return $this->_ctrlnamenetmask;
	}
	function SetValueNetmask($value) {
		$this->_valuenetmask = $value;
	}
	function GetValueNetmask() {
		return $this->_valuenetmask;
	}
}
class HTMLIPv4AddressBox2 extends HTMLIPAddressBox2 {
	//	constructor
	function __construct($ctrlname,$ctrlnamenetmask,$title,$value,$valuenetmask,$description) {
		parent::__construct($ctrlname,$ctrlnamenetmask,$title,$value,$valuenetmask,$description);
		$this->SetSize(20);
	}
	//	support methods
	function ComposeInner(&$anchor) {
		$ctrlname = $this->GetCtrlName();
		$ctrlnamenetmask = $this->GetCtrlNameNetmask();
		$valuenetmask = $this->GetValueNetmask();
		$attributes = [
			'type' => 'text',
			'id' => $ctrlname,
			'name' => $ctrlname,
			'class' => 'formfld',
			'value' => $this->GetValue(),
			'size' => $this->GetSize()
		];
		$anchor->addElement('input',$attributes);
		$slash = $anchor->ownerDocument->createTextNode(' / ');
		$anchor->appendChild($slash);
		$attributes = ['id' => $ctrlnamenetmask,'name' => $ctrlnamenetmask,'class' => 'formfld'];
		$o_select = $anchor->addElement('select',$attributes);
		foreach(range(1,32) as $netmask):
			$attributes = ['value' => $netmask];
			if($netmask == $valuenetmask):
				$attributes['selected'] = 'selected';
			endif;
			$o_select->addElement('option',$attributes,$netmask);
		endforeach;
	}
}
class HTMLIPv6AddressBox2 extends HTMLIPAddressBox2 {
	//	constructor
	function __construct($ctrlname,$ctrlnamenetmask,$title,$value,$valuenetmask,$description) {
		parent::__construct($ctrlname,$ctrlnamenetmask,$title,$value,$valuenetmask,$description);
		$this->SetSize(60);
	}
	//	support methods
	function ComposeInner(&$anchor) {
		$ctrlname = $this->GetCtrlName();
		$ctrlnamenetmask = $this->GetCtrlNameNetmask();
		$attributes = [
			'type' => 'text',
			'id' => $ctrlname,
			'name' => $ctrlname,
			'class' => 'formfld',
			'value' => $this->GetValue(),
			'size' => $this->GetSize()
		];
		$anchor->addElement('input',$attributes);
		$slash = $anchor->ownerDocument->createTextNode(' / ');
		$anchor->appendChild($slash);
		$attributes = [
			'type' => 'text',
			'id' => $ctrlnamenetmask,
			'name' => $ctrlnamenetmask,
			'class' => 'formfld',
			'value' => $this->GetValueNetmask(),
			'size' => 2
		];
		$anchor->addElement('input',$attributes);
	}
}
class HTMLCheckBox2 extends HTMLBaseControlJS2 {
	var $_caption = '';
	var $_classcheckbox = 'celldatacheckbox';
	var $_classcheckboxro = 'celldatacheckbox';
	//	constructor
	function __construct($ctrlname,$title,$value,$caption,$description = '') {
		parent::__construct($ctrlname,$title,$value,$description);
		$this->SetCaption($caption);
	}
	//	get/set methods
	function SetChecked($bool) {
		$this->SetValue($bool);
	}
	function IsChecked() {
		return $this->GetValue();
	}
	function SetCaption($caption) {
		$this->_caption = $caption;
	}
	function GetCaption() {
		return $this->_caption;
	}
	function SetClassCheckbox($cssclass) {
		$this->_classcheckbox = $cssclass;
	}
	function GetClassCheckbox() {
		return $this->_classcheckbox;
	}
	function SetClassCheckboxRO($cssclass) {
		$this->_classcheckboxro = $cssclass;
	}
	function GetClassCheckboxRO() {
		return $this->_classcheckboxro;
	}
	//	support methods
	function GetAttributes(array &$attributes = []) {
		if(true === $this->IsChecked()):
			$attributes['checked'] = 'checked';
		endif;
		if(true === $this->GetReadOnly()):
			$attributes['disabled'] = 'disabled';
		endif;
		$onclick = $this->GetJSonClick();
		if(!empty($onclick)):
			$attributes['onclick'] = $onclick;
		endif;
		return $attributes;
	}
	function GetClassOfCheckbox() {
		return ($this->GetReadOnly() ? $this->GetClassCheckboxRO() : $this->GetClassCheckbox());
	}
	function ComposeInner(&$anchor) {
		//	helper variables
		$ctrlname = $this->GetCtrlName();
		//	compose
		$div = $anchor->addElement('div',['class' => $this->GetClassOfCheckbox()]);
		$attributes = ['type' => 'checkbox','id' => $ctrlname,'name' => $ctrlname,'value' => 'yes'];
		$this->GetAttributes($attributes);
		$div->addElement('input',$attributes);
		$div->addElement('label',['for' => $ctrlname],$this->GetCaption());
	}
}
class HTMLSelectControl2 extends HTMLBaseControlJS2 {
	var $_ctrlclass = '';
	var $_options = [];
	//	constructor
	function __construct($ctrlclass,$ctrlname,$title,$value,$options,$description) {
		parent::__construct($ctrlname,$title,$value,$description);
		$this->SetCtrlClass($ctrlclass);
		$this->SetOptions($options);
	}
	//	get/set methods
	function SetCtrlClass($ctrlclass) {
		$this->_ctrlclass = $ctrlclass;
	}
	function GetCtrlClass() {
		return $this->_ctrlclass;
	}
	function SetOptions(array $options = []) {
		$this->_options = $options;
	}
	function GetOptions() {
		return $this->_options;
	}
	function GetAttributes(array &$attributes = []) {
		if(true === $this->GetReadOnly()):
			$attributes['disabled'] = 'disabled';
		endif;
		$onclick = $this->GetJSonClick();
		if(!empty($onclick)):
			$attributes['onclick'] = $onclick;
		endif;
		return $attributes;
	}
	function ComposeInner(&$anchor) {
		$ctrlname = $this->GetCtrlName();
		$value = $this->GetValue();
		$options = $this->GetOptions();
		$attributes = [
			'id' => $ctrlname,
			'name' => $ctrlname,
			'class' => $this->GetCtrlClass()
		];
		$this->GetAttributes($attributes);
		$select = $anchor->addElement('select',$attributes);
		foreach($options as $option_tag => $option_val):
			$attributes = ['value' => $option_tag];
			if($value == $option_tag):
				$attributes['selected'] = 'selected';
			endif;
			$select->addElement('option',$attributes,$option_val);
		endforeach;
	}
}
class HTMLMultiSelectControl2 extends HTMLSelectControl2 {
	var $_size = 10;
	//	constructor
	function __construct($ctrlclass,$ctrlname,$title,$value,$options,$description) {
		parent::__construct($ctrlclass,$ctrlname,$title,$value,$options,$description);
	}
	//	get/set methods
	function GetSize() {
		return $this->_size;
	}
	function SetSize($size) {
		$this->_size = $size;
	}
	//	support methods
	function ComposeInner(&$anchor) {
		$ctrlname = $this->GetCtrlName();
		$value = $this->GetValue();
		$options = $this->GetOptions();
		$attributes = [
			'id' => $ctrlname,
			'name' => sprintf('%s[]',$ctrlname),
			'class' => $this->GetCtrlClass(),
			'multiple' => 'multiple',
			'size' => $this->GetSize()
		];
		$this->GetAttributes($attributes);
		$select = $anchor->addElement('select',$attributes);
		foreach($options as $option_tag => $option_val):
			$attributes = ['value' => $option_tag];
			if(is_array($value) && in_array($option_tag,$value)):
				$attributes['selected'] = 'selected';
			endif;
			$select->addElement('option',$attributes,$option_val);
		endforeach;
	}
}
class HTMLComboBox2 extends HTMLSelectControl2 {
	//	constructor
	function __construct($ctrlname,$title,$value,$options,$description) {
		parent::__construct('formfld',$ctrlname,$title,$value,$options,$description);
	}
}
class HTMLRadioBox2 extends HTMLComboBox2 {
	function ComposeInner(&$anchor) {
		$ctrlname = $this->GetCtrlName();
		$value = $this->GetValue();
		$options = $this->GetOptions();
		$table = $anchor->addElement('table',['class' => 'area_data_selection']);
		$colgroup = $table->addElement('colgroup');
		$colgroup->addElement('col',['style' => 'width:5%']);
		$colgroup->addElement('col',['style' => 'width:95%']);
		$thead = $table->addElement('thead');
		$tr = $thead->addElement('tr');
		$tr->addElement('th',['class' => 'lhelc']);
		$tr->addElement('th',['class' => 'lhebl'],$this->GetTitle());
		$tbody = $table->addElement('tbody');
		foreach($options as $option_tag => $option_val):
			//	create a unique identifier for each row.
			//	use label tag for text column to allow enabling the radio button by clicking on the text
			$uuid = sprintf('radio_%s',uuid());
			$tr = $tbody->addElement('tr');
			$tdl = $tr->addElement('td',['class' => 'lcelc']);
			$attributes = [
				'name' => $ctrlname,
				'value' => $option_tag,
				'type' => 'radio',
				'id' => $uuid
			];
			if($value === $option_tag):
				$attributes['checked'] = 'checked';
			endif;
			$tdl->addElement('input',$attributes);
			$tdr = $tr->addElement('td',['class' => 'lcebl']);
			$tdr->addElement('label',['for' => $uuid,'style' => 'white-space:pre-wrap;'],$option_val);
		endforeach;
	}
}
class HTMLMountComboBox2 extends HTMLComboBox2 {
	//	constructor
	function __construct($ctrlname,$title,$value,$description) {
		global $config;
		//	generate options.
		$a_mounts = &array_make_branch($config,'mounts','mount');
		array_sort_key($a_mounts,'devicespecialfile');
		$options = [];
		$options[''] = gtext('Must choose one');
		foreach($a_mounts as $r_mount):
			$options[$r_mount['uuid']] = $r_mount['sharename'];
		endforeach;
		parent::__construct($ctrlname,$title,$value,$options,$description);
	}
}
class HTMLTimeZoneComboBox2 extends HTMLComboBox2 {
	function __construct($ctrlname,$title,$value,$description) {
		//	get time zone data.
		function is_timezone($elt) {
			return !preg_match("/\/$/",$elt);
		}
		exec('/usr/bin/tar -tf /usr/share/zoneinfo.txz',$timezonelist);
		$timezonelist = array_filter($timezonelist,'is_timezone');
		sort($timezonelist);
		//	generate options.
		$options = [];
		foreach($timezonelist as $tzv):
			if(!empty($tzv)):
				$tzv = substr($tzv,2); // Remove leading './'
				$options[$tzv] = $tzv;
			endif;
		endforeach;
		parent::__construct($ctrlname,$title,$value,$options,$description);
	}
}
class HTMLLanguageComboBox2 extends HTMLComboBox2 {
	function __construct($ctrlname,$title,$value,$description) {
		global $g_languages;
		//	generate options.
		$options = [];
		foreach($g_languages as $key => $val):
			if('auto' == $key):
				$options[$key] = gtext('Autodetect');
			else:
				$options[$key] = locale_get_display_name($key,$key);
			endif;
		endforeach;
		parent::__construct($ctrlname,$title,$value,$options,$description);
	}
}
class HTMLInterfaceComboBox2 extends HTMLComboBox2 {
	function __construct($ctrlname,$title,$value,$description) {
		global $config;
		//	generate options.
		$options = ['lan' => 'LAN'];
		for($i = 1;isset($config['interfaces']['opt' . $i]);$i++):
			if(isset($config['interfaces']['opt' . $i]['enable'])):
				$options['opt' . $i] = $config['interfaces']['opt' . $i]['descr'];
			endif;
		endfor;
		parent::__construct($ctrlname,$title,$value,$options,$description);
	}
}
class HTMLListBox2 extends HTMLMultiSelectControl2 {
	function __construct($ctrlname,$title,$value,$options,$description) {
		parent::__construct('formselect',$ctrlname,$title,$value,$options,$description);
	}
}
class HTMLCheckboxBox2 extends HTMLListBox2 {
	function ComposeInner(&$anchor) {
		$ctrlname = $this->GetCtrlName();
		$value = $this->GetValue();
		$options = $this->GetOptions();
		$table = $anchor->addElement('table',['class' => 'area_data_selection']);
		$colgroup = $table->addElement('colgroup');
		$colgroup->addElement('col',['style' => 'width:5%']);
		$colgroup->addElement('col',['style' => 'width:95%']);
		$thead = $table->addElement('thead');
		$tr = $thead->addElement('tr');
		$tr->addElement('th',['class' => 'lhelc']);
		$tr->addElement('th',['class' => 'lhebl'],$this->GetTitle());
		$tbody = $table->addElement('tbody');
		foreach($options as $option_tag => $option_val):
			//	create a unique identifier for each row.
			//	use label tag for text column to allow toggling the checkbox button by clicking on the text
			$uuid = sprintf('checkbox_%s',uuid());
			$tr = $tbody->addElement('tr');
			$tdl = $tr->addElement('td',['class' => 'lcelc']);
			$attributes = [
				'name' => sprintf('%s[]',$ctrlname),
				'value' => $option_tag,
				'type' => 'checkbox',
				'id' => $uuid
			];
			if(is_array($value) && in_array($option_tag,$value)):
				$attributes['checked'] = 'checked';
			endif;
			$tdl->addElement('input',$attributes);
			$tdr = $tr->addElement('td',['class' => 'lcebl']);
			$tdr->addElement('label',['for' => $uuid,'style' => 'white-space:pre-wrap;'],$option_val);
		endforeach;
	}
}
class HTMLSeparator2 extends HTMLBaseControl2 {
	var $_colspan = 2;
	var $_classseparator = 'gap';
	// constructor
	function __construct() {
	}
	//	get/set methods
	function SetClassSeparator($cssclass) {
		$this->_classseparator = $cssclass;
	}
	function GetClassSeparator() {
		return $this->_classseparator;
	}
	function SetColSpan($colspan) {
		$this->_colspan = $colspan;
	}
	function GetColSpan() {
		return $this->_colspan;
	}
	//	support methods
	function GetClassOfSeparator() {
		return $this->GetClassSeparator();
	}
	function Compose(DOMNode &$anchor = NULL) {
		//	create root DOM if anchor not provided
		if(is_null($anchor)):
			$anchor = new co_DOMDocument();
		endif;
		//	helping variables
		$ctrlname = $this->GetCtrlName();
		//	compose
		$attributes = [];
		if(preg_match('/\S/',$ctrlname)):
			$attributes['id'] = $ctrlname;
		endif;
		$o_tr = $anchor->addElement('tr',$attributes);
		$attributes = ['class' => $this->GetClassOfSeparator(),'colspan' => $this->GetColSpan()];
		$o_tr->addElement('td',$attributes);
		return $anchor;
	}
}
class HTMLTitleLine2 extends HTMLBaseControl2 {
	var $_colspan = 2;
	var $_classtopic = 'lhetop';
	//	get/set methods
	function SetClassTopic($cssclass) {
		$this->_classtopic = $cssclass;
	}
	function GetClassTopic() {
		return $this->_classtopic;
	}
	function SetColSpan($colspan) {
		$this->_colspan = $colspan;
	}
	function GetColSpan() {
		return $this->_colspan;
	}
	//	support methods
	function GetClassOfTopic() {
		return $this->GetClassTopic();
	}
	function Compose(DOMNode &$anchor = NULL) {
		//	create root DOM if anchor not provided
		if(is_null($anchor)):
			$anchor = new co_DOMDocument();
		endif;
		//	helping variables
		$ctrlname = $this->GetCtrlName();
		//	compose
		$attributes = [];
		if(preg_match('/\S/',$ctrlname)):
			$attributes['id'] = $ctrlname;
		endif;
		$tr = $anchor->addElement('tr',$attributes);
		$attributes = ['class' => $this->GetClassOfTopic(),'colspan' => $this->GetColSpan()];
		$th = $tr->addElement('th',$attributes,$this->GetTitle());
		return $anchor;
	}
}
class HTMLTitleLineCheckBox2 extends HTMLCheckBox2 {
	var $_colspan = 2;
	var $_classtopic = 'lhetop';
	//	constructor
	function __construct($ctrlname,$title,$value,$caption) {
		parent::__construct($ctrlname,$title,$value,$caption);
	}
	//	get/set methods
	function SetClassTopic($cssclass) {
		$this->_classtopic = $cssclass;
	}
	function GetClassTopic() {
		return $this->_classtopic;
	}
	function SetColSpan($colspan) {
		$this->_colspan = $colspan;
	}
	function GetColSpan() {
		return $this->_colspan;
	}
	//	support methods
	function GetClassOfTopic() {
		return $this->GetClassTopic();
	}
	function Compose(DOMNode &$anchor = NULL) {
		//	create root DOM if anchor not provided
		if(is_null($anchor)):
			$anchor = new co_DOMDocument();
		endif;
		//	helping variables
		$ctrlname = $this->GetCtrlName();
		//	compose
		$attributes = ['id' => sprintf('%s_tr',$ctrlname)];
		$tr = $anchor->addElement('tr',$attributes);
		$attributes = ['class' => $this->GetClassOfTopic(),'colspan' => $this->GetColSpan()];
		$th = $tr->addElement('th',$attributes);
		$attributes = ['style' => 'float:left'];
		$spanleft = $th->addElement('span',$attributes,$this->GetTitle());
		$attributes = ['style' => 'float:right'];
		$spanright = $th->addElement('span',$attributes);
		$label = $spanright->addElement('label');
		$attributes = ['type' => 'checkbox','id' => $ctrlname,'name' => $ctrlname,'class' => 'formfld cblot','value' => 'yes'];
		$this->getAttributes($attributes);
		$label->addElement('input',$attributes);
		$attributes = ['class' => 'cblot'];
		$label->addElement('span',$attributes,$this->GetCaption());
		return $anchor;
	}
}
class HTMLText2 extends HTMLBaseControl2 {
	//	constructor
	function __construct($ctrlname,$title,$text) {
		$this->SetCtrlName($ctrlname);
		$this->SetReadOnly(true);
		$this->SetRequired(false);
		$this->SetTitle($title);
		$this->SetValue($text);
	}
	//	support methods
	function ComposeInner(&$anchor) {
		//	compose
		$anchor->addElement('span',[],$this->GetValue());
	}
}
class HTMLTextInfo2 extends HTMLBaseControl2 {
	function __construct($ctrlname,$title,$text) {
		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetValue($text);
	}
	function Compose(DOMNode &$anchor = NULL) {
		//	create root DOM if anchor not provided
		if(is_null($anchor)):
			$anchor = new co_DOMDocument();
		endif;
		//	helping variables
		$ctrlname = $this->GetCtrlName();
		//	compose
		$attributes = ['id' => sprintf('%s_tr',$ctrlname)];
		$tr = $anchor->addElement('tr',$attributes);
		$attributes = ['class' => $this->GetClassOfTag()];
		$tdtag = $tr->addElement('td',$attributes,$this->GetTitle());
		$attributes = ['class' => $this->GetClassOfData()];
		$tddata = $tr->addElement('td',$attributes);
		$attributes = ['id' => $ctrlname];
		$tddata->addElement('span',$attributes,$this->getValue());
		return $anchor;
	}
}
class HTMLRemark2 extends HTMLBaseControl2 {
	function __construct($ctrlname,$title,$text) {
		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetValue($text);
	}
	function Compose(DOMNode &$anchor = NULL) {
		//	create root DOM if anchor not provided
		if(is_null($anchor)):
			$anchor = new co_DOMDocument();
		endif;
		//	helping variables
		$ctrlname = $this->GetCtrlName();
		//	compose
		$attributes = ['id' => $ctrlname];
		$div1 = $anchor->addElement('div',$attributes);
		$attributes = ['class' => 'red'];
		$div1->addElement('strong',$attributes,$this->GetTitle());
		$attributes = [];
		$div2 = $anchor->addElement('div',$attributes,$this->GetValue());
		return $anchor;
	}
}
class HTMLFolderBox2 extends HTMLBaseControl2 {
	var $_path = '';

	function __construct($ctrlname,$title,$value,$description = '') {
		parent::__construct($ctrlname,$title,$value,$description);
	}
	function GetPath() {
		return $this->_path;
	}
	function SetPath($path) {
		$this->_path = $path;
	}
	function ComposeInner(&$anchor) {
		//	helping variables
		$ctrlname = $this->GetCtrlName();
		$ctrlnamedata = $ctrlname . 'data';
		$value = $this->GetValue();
		//	control code for folders
		$t = [];
		$t[] = sprintf('function onchange_%s() {',$ctrlname);
		$t[] = "\t" . sprintf('document.getElementById("%s").value = document.getElementById("%s").value;',$ctrlnamedata,$ctrlname);
		$t[] = '}';
		$t[] = sprintf('function onclick_add_%s() {',$ctrlname);
		$t[] = "\t" . sprintf('var value = document.getElementById("%s").value;',$ctrlnamedata);
		$t[] = "\t" . 'if (value != "") {';
		$t[] = "\t\t" . 'var found = false;';
		$t[] = "\t\t" . sprintf('var element = document.getElementById("%s");',$ctrlname);
		$t[] = "\t\t" . 'for (var i = 0; i < element.length; i++) {';
		$t[] = "\t\t\t" . 'if (element.options[i].text == value) {';
		$t[] = "\t\t\t\t" . 'found = true;';
		$t[] = "\t\t\t\t" . 'break;';
		$t[] = "\t\t\t" . '}';
		$t[] = "\t\t" . '}';
		$t[] = "\t\t" . 'if (found != true) {';
		$t[] = "\t\t\t" . 'element.options[element.length] = new Option(value, value, false, true);';
		$t[] = "\t\t\t" . sprintf('document.getElementById("%s").value = "";',$ctrlnamedata);
		$t[] = "\t\t" . '}';
		$t[] = "\t" . '}';
		$t[] = '}';
		$t[] = sprintf('function onclick_delete_%s() {',$ctrlname);
		$t[] = "\t" . sprintf('var element = document.getElementById("%s");',$ctrlname);
		$t[] = "\t" . 'if (element.value != "") {';
		$t[] = "\t\t" . sprintf('var msg = confirm("%s");',gtext('Do you really want to remove the selected item from the list?'));
		$t[] = "\t\t" . 'if (msg == true) {';
		$t[] = "\t\t\t" . 'element.options[element.selectedIndex] = null;';
		$t[] = "\t\t\t" . sprintf('document.getElementById("%s").value = "";',$ctrlnamedata);
		$t[] = "\t\t" . '}';
		$t[] = "\t" . '} else {';
		$t[] = "\t\t" . sprintf('alert("%s");',gtext('Select item to remove from the list'));
		$t[] = "\t" . '}';
		$t[] = '}';
		$t[] = sprintf('function onclick_change_%s() {',$ctrlname);
		$t[] = "\t" . sprintf('var element = document.getElementById("%s");',$ctrlname);
		$t[] = "\t" . 'if (element.value != "") {';
		$t[] = "\t\t" . sprintf('var value = document.getElementById("%s").value;',$ctrlnamedata);
		$t[] = "\t\t" . 'element.options[element.selectedIndex].text = value;';
		$t[] = "\t\t" . 'element.options[element.selectedIndex].value = value;';
		$t[] = "\t" . '}';
		$t[] = '}';
		$t[] = sprintf('function onsubmit_%s() {',$ctrlname);
		$t[] = "\t" . sprintf('var element = document.getElementById("%s");',$ctrlname);
		$t[] = "\t" . 'for (var i = 0; i < element.length; i++) {';
		$t[] = "\t\t" . 'if (element.options[i].value != "")';
		$t[] = "\t\t\t" . 'element.options[i].selected = true;';
		$t[] = "\t" . '}';
		$t[] = '}';
		$anchor->addJavaScript(implode(PHP_EOL,$t));
		//	section 1: select + delete
		$div1 = $anchor->addElement('div');
		//	selected folder
		$attributes = [
			'id' => $ctrlname,
			'name' => sprintf('%s[]',$ctrlname),
			'class' => 'formfld',
			'multiple' => 'multiple',
			'size' => '4',
			'style' => 'width:350px',
			'onchange' => sprintf('onchange_%s()',$ctrlname)
		];
		$select = $div1->addElement('select',$attributes);
		foreach ($value as $value_key => $value_val):
			$attributes = ['value' => $value_val];
			$select->addElement('option',$attributes,$value_val);
		endforeach;
		//	delete button
		$attributes = [
			'type' => 'button',
			'id' => sprintf('%sdeletebtn',$ctrlname),
			'name' => sprintf('%sdeletebtn',$ctrlname),
			'class' => 'formbtn',
			'value' => gtext('Delete'),
			'onclick' => sprintf('onclick_delete_%s()',$ctrlname)
		];
		$div1->addElement('input',$attributes);
		//	section 2: choose, add + change
		$div2 = $anchor->addElement('div');
		//	path input field
		$attributes = [
			'type' => 'text',
			'id' => sprintf('%sdata',$ctrlname),
			'name' => sprintf('%sdata',$ctrlname),
			'class' => 'formfld',
			'value' => '',
			'size' => 60
		];
		$div2->addElement('input',$attributes);
		//	choose button
		$js = sprintf('ifield = form.%s;',$ctrlnamedata)
			. ' filechooser = window.open("filechooser.php'
			. '?p="+encodeURIComponent(ifield.value)+"'
			. sprintf('&sd=%s",',$this->GetPath())
			. ' "filechooser",'
			. ' "scrollbars=yes,toolbar=no,menubar=no,statusbar=no,width=550,height=300");'
			. ' filechooser.ifield = ifield;'
			. ' window.ifield = ifield;';
		$attributes = [
			'type' => 'button',
			'id' => sprintf('%sbrowsebtn',$ctrlname),
			'name' => sprintf('%sbrowsebtn',$ctrlname),
			'class' => 'formbtn',
			'value' => '...',
			'onclick' => $js
		];
		$div2->addElement('input',$attributes);
		//	add button
		$attributes = [
			'type' => 'button',
			'id' => sprintf('%saddbtn',$ctrlname),
			'name' => sprintf('%saddbtn',$ctrlname),
			'class' => 'formbtn',
			'value' => gtext('Add'),
			'onclick' => sprintf('onclick_add_%s()',$ctrlname)
		];
		$div2->addElement('input',$attributes);
		//	change button
		$attributes = [
			'type' => 'button',
			'id' => sprintf('%schangebtn',$ctrlname),
			'name' => sprintf('%schangebtn',$ctrlname),
			'class' => 'formbtn',
			'value' => gtext('Change'),
			'onclick' => sprintf('onclick_change_%s()',$ctrlname)
		];
		$div2->addElement('input',$attributes);
	}
}
class HTMLFolderBox12 extends HTMLFolderBox2 {
	function ComposeInner(&$anchor) {
		//	helping variables
		$ctrlname = $this->GetCtrlName();
		$ctrlnamedata = $ctrlname . 'data';
		$ctrlnamefiletype = $ctrlname . 'filetype';
		$value = $this->GetValue();
		//	control code for folders
		$t = [];
		$t[] = 'function onchange_' . $ctrlname . '() {';
		$t[] = "\t" . 'var value1 = document.getElementById("' . $ctrlname . '");';
		$t[] = "\t" . 'if (value1.value.charAt(0) != "/") {';
		$t[] = "\t" . 'document.getElementById("' . $ctrlnamedata . '").value = value1.value.substring(2,(value1.value.length));';
		$t[] = "\t" . 'document.getElementById("' . $ctrlnamefiletype . '").value = value1.value.charAt(0);';
		$t[] = "\t\t" . '}else{';
		$t[] = "\t" . 'document.getElementById("' . $ctrlnamedata . '").value = document.getElementById("' . $ctrlname . '").value;';
		$t[] = "\t" . 'document.getElementById("' . $ctrlnamefiletype . '").value = "";';
		$t[] = "\t" . '}';
		$t[] = '}';
		$t[] = 'function onclick_add_' . $ctrlname . '() {';
		$t[] = "\t" . 'var value1 = document.getElementById("' . $ctrlnamedata . '").value;';
		$t[] = "\t" . 'var valuetype = document.getElementById("' . $ctrlnamefiletype . '").value;';
		$t[] = "\t" . 'if (valuetype != "") {';
		$t[] = "\t\t" . 'var valuetype = valuetype + ",";';
		$t[] = "\t" . '}';
		$t[] = "\t" . 'var value = valuetype +  value1;';
		$t[] = "\t" . 'if (value != "") {';
		$t[] = "\t\t" . 'var found = false;';
		$t[] = "\t\t" . 'var element = document.getElementById("' . $ctrlname . '");';
		$t[] = "\t\t" . 'for (var i = 0; i < element.length; i++) {';
		$t[] = "\t\t\t" . 'if (element.options[i].text == value) {';
		$t[] = "\t\t\t\t" . 'found = true;';
		$t[] = "\t\t\t\t" . 'break;';
		$t[] = "\t\t\t" . '}';
		$t[] = "\t\t" . '}';
		$t[] = "\t\t" . 'if (found != true) {';
		$t[] = "\t\t\t" . 'element.options[element.length] = new Option(value, value, false, true);';
		$t[] = "\t\t\t" . 'document.getElementById("' . $ctrlnamedata . '").value = "";';
		$t[] = "\t\t" . '}';
		$t[] = "\t" . '}';
		$t[] = '}';
		$t[] = 'function onclick_delete_' . $ctrlname . '() {';
		$t[] = "\t" . 'var element = document.getElementById("' . $ctrlname . '");';
		$t[] = "\t" . 'if (element.value != "") {';
		$t[] = "\t\t" . 'var msg = confirm("' . gtext('Do you really want to remove the selected item from the list?') . '");';
		$t[] = "\t\t" . 'if (msg == true) {';
		$t[] = "\t\t\t" . 'element.options[element.selectedIndex] = null;';
		$t[] = "\t\t\t" . 'document.getElementById("' . $ctrlnamedata . '").value = "";';
		$t[] = "\t\t" . '  }';
		$t[] = "\t" . '} else {';
		$t[] = "\t\t" . 'alert("' . gtext('Select item to remove from the list') . '");';
		$t[] = "\t" . '}';
		$t[] = '}';
		$t[] = 'function onclick_change_' . $ctrlname . '() {';
		$t[] = "\t" . 'var element = document.getElementById("' . $ctrlname . '");';
		$t[] = "\t" . 'if (element.value != "") {';
		$t[] = "\t\t" . 'var value1 = document.getElementById("' . $ctrlnamedata . '").value;';
		$t[] = "\t" . 'var valuetype = document.getElementById("' . $ctrlnamefiletype . '").value;';
		$t[] = "\t" . 'if (valuetype != "") {';
		$t[] = "\t\t" . 'var valuetype = valuetype + ",";';
		$t[] = "\t" . '}';
		$t[] = "\t" . 'var value = valuetype +  value1;';
		$t[] = "\t\t" . 'element.options[element.selectedIndex].text = value;';
		$t[] = "\t\t" . 'element.options[element.selectedIndex].value = value;';
		$t[] = "\t" . '}';
		$t[] = '}';
		$t[] = 'function onsubmit_' . $ctrlname . '() {';
		$t[] = "\t" . 'var element = document.getElementById("' . $ctrlname . '");';
		$t[] = "\t" . 'for (var i = 0; i < element.length; i++) {';
		$t[] = "\t\t" . 'if (element.options[i].value != "")';
		$t[] = "\t\t\t" . 'element.options[i].selected = true;';
		$t[] = "\t" . '}';
		$t[] = '}';
		$anchor->addJavaScript(implode(PHP_EOL,$t));
		//	section 1: select + delete
		$div1 = $anchor->addElement('div');
		//	selected folder
		$attributes = ['id' => $ctrlname,'name' => sprintf('%s[]',$ctrlname),'class' => 'formfld','multiple' => 'multiple','style' => 'width:350px','onchange' => sprintf('onchange_%s()',$ctrlname)];
		$select = $div1->addElement('select',$attributes);
		foreach ($value as $value_key => $value_val):
			$attributes = ['value' => $value_val];
			$select->addElement('option',$attributes,$value_val);
		endforeach;
		//	delete button
		$attributes = [
			'type' => 'button',
			'id' => sprintf('%sdeletebtn',$ctrlname),
			'name' => sprintf('%sdeletebtn',$ctrlname),
			'class' => 'formbtn',
			'value' => gtext('Delete'),
			'onclick' => sprintf('onclick_delete_%s()',$ctrlname)
		];
		$div1->addElement('input',$attributes);
		//	section 2: choose, add + change
		$div2 = $anchor->addElement('div');
		//	media type
		$attributes = ['id' => sprintf('%sfiletype',$ctrlname),'name' => sprintf('%sfiletype',$ctrlname),'class' => 'formfld'];
		$select = $div2->addElement('select',$attributes);
		$attributes = ['value' => '','selected' => 'selected'];
		$select->addElement('option',$attributes,gtext('All'));
		$attributes = ['value' => 'A'];
		$select->addElement('option',$attributes,gtext('Audio'));
		$attributes = ['value' => 'V'];
		$select->addElement('option',$attributes,gtext('Video'));
		$attributes = ['value' => 'P'];
		$select->addElement('option',$attributes,gtext('Pictures'));
		//	path input field
		$attributes = [
			'type' => 'text',
			'id' => sprintf('%sdata',$ctrlname),
			'name' => sprintf('%sdata',$ctrlname),
			'class' => 'formfld',
			'value' => '',
			'size' => 60
		];
		$div2->addElement('input',$attributes);
		//	choose button
		$js = sprintf('ifield = form.%s;',$ctrlnamedata)
			. ' filechooser = window.open("filechooser.php'
			. '?p="+encodeURIComponent(ifield.value)+"'
			. sprintf('&sd=%s",',$this->GetPath())
			. ' "filechooser",'
			. ' "scrollbars=yes,toolbar=no,menubar=no,statusbar=no,width=550,height=300");'
			. ' filechooser.ifield = ifield;'
			. ' window.ifield = ifield;';
		$attributes = [
			'type' => 'button',
			'id' => sprintf('%sbrowsebtn',$ctrlname),
			'name' => sprintf('%sbrowsebtn',$ctrlname),
			'class' => 'formbtn',
			'value' => '...',
			'onclick' => $js
		];
		$div2->addElement('input',$attributes);
		//	add button
		$attributes = [
			'type' => 'button',
			'id' => sprintf('%saddbtn',$ctrlname),
			'name' => sprintf('%saddbtn',$ctrlname),
			'class' => 'formbtn',
			'value' => gtext('Add'),
			'onclick' => sprintf('onclick_add_%s()',$ctrlname)
		];
		$div2->addElement('input',$attributes);
		//	change button
		$attributes = [
			'type' => 'button',
			'id' => sprintf('%schangebtn',$ctrlname),
			'name' => sprintf('%schangebtn',$ctrlname),
			'class' => 'formbtn',
			'value' => gtext('Change'),
			'onclick' => sprintf('onclick_change_%s()',$ctrlname)
		];
		$div2->addElement('input',$attributes);
	}
}
trait co_DOMTools {
/**
 *	Appends a child node to an element and returns $subnode
 *	@param string $name
 *	@param array $attributes
 *	@param string $value
 *	@param string $namespaceURI
 *	@return DOMNode $subnode
 */
	public function addElement(string $name,array $attributes = [],string $value = NULL,string $namespaceURI = NULL) {
		$subnode = $this->appendChild(new co_DOMElement($name,NULL,$namespaceURI));
		$subnode->addAttributes($attributes);
		if(preg_match('/\S/',$value)):
			$saved_setting = libxml_use_internal_errors(true); // user cares about exceptions
			$document = $subnode->ownerDocument ?? $this;
			$innerhtml = $document->createDocumentFragment(); // create fragment from $value
			if($innerhtml->appendXML($value)):
				$subnode->appendChild($innerhtml);
			endif;
			libxml_clear_errors();
			libxml_use_internal_errors($saved_setting);
		endif;
		return $subnode;
	}
/**
 *	Appends a child node to an element and returns $this
 *	@param string $name
 *	@param array $attributes
 *	@param string $value
 *	@param string $namespaceURI
 *	@return DOMNode $this
 */
	public function insElement(string $name,array $attributes = [],string $value = NULL,string $namespaceURI = NULL) {
		$subnode = $this->appendChild(new co_DOMElement($name,NULL,$namespaceURI));
		$subnode->addAttributes($attributes);
		if(preg_match('/\S/',$value)):
			$saved_setting = libxml_use_internal_errors(true); // user cares about exceptions
			$document = $subnode->ownerDocument ?? $this;
			$innerhtml = $document->createDocumentFragment(); // create fragment from $value
			if($innerhtml->appendXML($value)):
				$subnode->appendChild($innerhtml);
			endif;
			libxml_clear_errors();
			libxml_use_internal_errors($saved_setting);
		endif;
		return $this;
	}
/**
 *	Inserts a child node on top of the children
 *	@param string $name
 *	@param array $attributes
 *	@param string $value
 *	@param string $namespaceURI
 *	@return DOMNode $subnode
 */
	public function prepend_element(string $name,array $attributes = [],string $value = NULL,string $namespaceURI = NULL) {
		if(is_null($this->firstChild)):
			$subnode = $this->insertBefore(new co_DOMElement($name,NULL,$namespaceURI));
		else:
			$subnode = $this->insertBefore(new co_DOMElement($name,NULL,$namespaceURI),$this->firstChild);
		endif;
		$subnode->addAttributes($attributes);
		if(preg_match('/\S/',$value)):
			$saved_setting = libxml_use_internal_errors(true); // user cares about exceptions
			$innerhtml = $subnode->ownerDocument->createDocumentFragment(); // create fragment from $value
			if($innerhtml->appendXML($value)):
				$subnode->appendChild($innerhtml);
			endif;
			libxml_clear_errors();
			libxml_use_internal_errors($saved_setting);
		endif;
		return $subnode;
	}
/**
 *	Appends a JavaScript node to the DOM
 *	@param string $text
 *	@return DOMNode $this
 */
	public function addJavaScript(string $text = '') {
		$node = $this->addElement('script');
		if(false !== $node):
			$opening = $node->ownerDocument->createTextNode(PHP_EOL . '//![CDATA[' . PHP_EOL);
			if(false !== $opening):
				$node->appendChild($opening);
				$cdata = $node->ownerDocument->createCDATASection($text);
				if(false !== $cdata):
					$node->appendChild($cdata);
				endif;
				$ending = $node->ownerDocument->createTextNode(PHP_EOL . '//]]>' . PHP_EOL);
				if(false !== $ending):
					$node->appendChild($ending);
				endif;
			endif;
		endif;
		return $this;
	}
	//	tags
	public function addA(array $attributes = [],string $value = NULL) {
		return $this->addElement('a',$attributes,$value);
	}
	public function insA(array $attributes = [],string $value = NULL) {
		return $this->insElement('a',$attributes,$value);
	}
	public function insCOL(array $attributes = []) {
		return $this->insElement('col',$attributes);
	}
	public function addDIV(array $attributes = [],string $value = NULL) {
		return $this->addElement('div',$attributes,$value);
	}
	public function insDIV(array $attributes = [],string $value = NULL) {
		return $this->insElement('div',$attributes,$value);
	}
	public function addFORM(array $attributes = [],string $value = NULL) {
		return $this->addElement('form',$attributes,$value);
	}
	public function insIMG(array $attributes = []) {
		return $this->insElement('img',$attributes);
	}
	public function insINPUT(array $attributes = [],string $value = NULL) {
		return $this->insElement('input',$attributes,$value);
	}
	public function addLI(array $attributes = [],string $value = NULL) {
		return $this->addElement('li',$attributes,$value);
	}
	public function addP(array $attributes = [],string $value = NULL) {
		return $this->addElement('p',$attributes,$value);
	}
	public function addSPAN(array $attributes = [],string $value = NULL) {
		return $this->addElement('span',$attributes,$value);
	}
	public function insSPAN(array $attributes = [],string $value = NULL) {
		return $this->insElement('span',$attributes,$value);
	}
	public function addUL(array $attributes = [],string $value = NULL) {
		return $this->addElement('ul',$attributes,$value);
	}
	//	table tags
	public function addTABLE(array $attributes = [],string $value = NULL) {
		return $this->addElement('table',$attributes,$value);
	}
	public function addCOLGROUP(array $attributes = [],string $value = NULL) {
		return $this->addElement('colgroup',$attributes,$value);
	}
	public function addTHEAD(array $attributes = [],string $value = NULL) {
		return $this->addElement('thead',$attributes,$value);
	}
	public function addTBODY(array $attributes = [],string $value = NULL) {
		return $this->addElement('tbody',$attributes,$value);
	}
	public function addTFOOT(array $attributes = [],string $value = NULL) {
		return $this->addElement('tfoot',$attributes,$value);
	}
	public function addTR(array $attributes = [],string $value = NULL) {
		return $this->addElement('tr',$attributes,$value);
	}
	public function addTD(array $attributes = [],string $value = NULL) {
		return $this->addElement('td',$attributes,$value);
	}
	public function insTD(array $attributes = [],string $value = NULL) {
		return $this->insElement('td',$attributes,$value);
	}
	public function addTDwC(string $class,string $value = NULL) {
		return $this->addElement('td',['class' => $class],$value);
	}
	public function insTDwC(string $class,string $value = NULL) {
		return $this->insElement('td',['class' => $class],$value);
	}
	public function addTH(array $attributes = [],string $value = NULL) {
		return $this->addElement('th',$attributes,$value);
	}
	public function insTH(array $attributes = [],string $value = NULL) {
		return $this->insElement('th',$attributes,$value);
	}
	public function addTHwC(string $class,string $value = NULL) {
		return $this->addElement('th',['class' => $class],$value);
	}
	public function insTHwC(string $class,string $value = NULL) {
		return $this->insElement('th',['class' => $class],$value);
	}
	//	tab menu fragments and macros
/**
 *
 *	@return DOMNode $subnode
 */
	public function add_area_tabnav() {
		$table_attributes = ['id' => 'area_navigator'];
		$document = $this->ownerDocument ?? $this;
		$target = $document->getElementById('g4h');
		if(isset($target)):
			$append_mode = true; // last element of header section
			$div_attributes = [
				'id' => 'area_tabnav',
				'style' => 'padding: 0em 2em;'
			];
			if($append_mode):
				$subnode = $target->
					addDIV($div_attributes)->
						addTABLE($table_attributes)->
							addTBODY();
			else:
				$subnode = $target->
					prepend_element('div',$div_attributes)->
						addTABLE($table_attributes)->
							addTBODY();
			endif;
		else: // workaround for unconverted pages because of padding
			$target = $this;
			$subnode = $target->
				addTABLE($table_attributes)->
					addTBODY();
		endif;
		return $subnode;
	}
/**
 *	Creates tags for upper navigation menu
 *	@return DOMNode $subnode
 */
	public function add_tabnav_upper() {
		$subnode = $this->
			addTR()->
				addTDwC('tabnavtbl')->
					addUL(['id' => 'tabnav']);
		return $subnode;
	}
/**
 *	Creates tags for lower navigation menu
 *	@return DOMNode $subnode
 */
	public function add_tabnav_lower() {
		$subnode = $this->
			addTR()->
				addTDwC('tabnavtbl')->
					addUL(['id' => 'tabnav2']);
		return $subnode;
	}
/**
 *	Adds a menu item to the navigation menu
 *	@param string $href Link to script
 *	@param string $value Name of the menu item
 *	@param string $title Title of the menu item
 *	@param bool $active Flag to indicate an active menu item
 *	@return object $this
 */
	public function ins_tabnav_record(string $href = '',string $value = '',string $title = '',bool $active = false) {
		$attributes = [];
		if(preg_match('/\S/',$href)):
			$attributes['href'] = $href;
		endif;
		if(preg_match('/\S/',$title)):
			$attributes['title'] = $title;
		endif;
		$this->
			addLI(['class' => $active ? 'tabact' : 'tabinact'])->
				addA($attributes)->
					addSPAN([],$value);
		return $this;
	}
	/**
	 *
	 *	@return DOMNode $subnode
	 */
	public function add_area_data() {
		$subnode = $this->
			addTABLE(['id' => 'area_data'])->
				addTBODY()->
					addTR()->
						addTD(['id' => 'area_data_frame']);
		return $subnode;
	}
	public function ins_input_errors(array $input_errors = []) {
		foreach($input_errors as $input_error):
			if(is_string($input_error)):
				if(preg_match('/\S/',$input_error)):
					$messages[] = $input_error;
				endif;
			endif;
		endforeach;
		if(!empty($messages)):
			$ul = $this->
				addDIV(['id' => 'errorbox'])->
					addTABLE(['border' => '0','cellspacing' => '0','cellpadding' => '1','width' => '100%'])->
						addTR()->
							push()->addTD(['class' => 'icon','align' => 'center','valign' => 'center'])->
								insIMG(['src' => 'images/error_box.png','alt' => ''])->
							pop()->addTDwC('message')->
								addDIV([],sprintf('%s:',gtext('The following errors were detected'),':'))->
									addUL();
			foreach($messages as $message):
				$ul->addLI([],$message);
			endforeach;
		endif;
		return $this;
	}
	public function ins_error_box(string $message = '') {
		if(preg_match('/\S/',$message)):
			$this->
				addDIV(['id' => 'errorbox'])->
					addTABLE(['style' => 'width:100%;border-spacing:0;'])->
						addTR()->
							push()->addTDwC('icon')->insIMG(['src' => '/images/error_box.png','alt' => ''])->
							pop()->addTDwC('message',$message);
		endif;
		return $this;
	}
	public function ins_info_box(string $message = '') {
		if(preg_match('/\S/',$message)):
			$this->
				addDIV(['id' => 'infobox'])->
					addTABLE(['style' => 'width:100%;border-spacing:0;'])->
						addTR()->
							push()->addTDwC('icon')->insIMG(['src' => '/images/info_box.png','alt' => ''])->
							pop()->addTDwC('message',$message);
		endif;
		return $this;
	}
	public function ins_warning_box(string $message = '') {
		if(preg_match('/\S/',$message)):
			$this->
				addDIV(['id' => 'warningbox'])->
					addTABLE(['style' => 'width:100%;border-spacing:0;'])->
						addTR()->
							push()->addTDwC('icon')->insIMG(['src' => '/images/warn_box.png','alt' => ''])->
							pop()->addTDwC('message',$message);
		endif;
		return $this;
	}
	public function ins_config_has_changed_box() {
		$gt_info = sprintf(
			'%s<br />%s<br /><b><a href="diag_log.php">%s</a></b>',
			gtext('The configuration has been changed.'),
			gtext('You must apply the changes in order for them to take effect.'),
			gtext('If this message persists take a look at the system log for more information.')
		);
		$this->
			addDIV(['id' => 'applybox'])->
				ins_info_box($gt_info)->
				ins_button_apply();
		return $this;
	}
	//	data settings table macros
	public function add_table_data_settings() {
		$subnode = $this->addTABLE(['class' => 'area_data_settings']);
		return $subnode;
	}
	public function ins_colgroup_data_settings() {
		$this->ins_colgroup_with_classes(['area_data_settings_col_tag','area_data_settings_col_data']);
		return $this;
	}
	public function add_table_data_selection() {
		$subnode = $this->addTABLE(['class' => 'area_data_selection']);
		return $subnode;
	}
	public function ins_colgroup_with_classes(array $data = []) {
		$colgroup = $this->addCOLGROUP();
		foreach($data as $value):
			$colgroup->insCOL(['class' => htmlspecialchars($value)]);
		endforeach;
		return $this;
	}
	public function ins_colgroup_with_styles(string $tag,array $data = []) {
		$colgroup = $this->addCOLGROUP();
		$tag = htmlspecialchars($tag);
		foreach($data as $value):
			$colgroup->insCOL(['style' => sprintf('%s:%s;',$tag,htmlspecialchars($value))]);
		endforeach;
		return $this;
	}
	//	title macros
	public function ins_titleline(string $title = NULL,int $colspan = 0,string $id = NULL) {
		$tr_attributes = [];
		$th_attributes = [];
		if(!is_null($id) && preg_match('/\S/',$id)):
			$tr_attributes['id'] = sprintf('%s_tr',$id);
		endif;
		$th_attributes['class'] = 'lhetop';
		if($this->option_exists('tablesort')):
			$tr_attributes['class'] = 'tablesorter-ignoreRow';
		endif;
		if($colspan > 0):
			$th_attributes['colspan'] = $colspan;
		endif;
		$spanleft_attributes = ['style' => 'float:left'];
		$this->addTR($tr_attributes)->addTH($th_attributes)->addSPAN($spanleft_attributes,$title);
		return $this;
	}
	public function ins_titleline_with_checkbox(properties $p,$value,bool $is_required = false,bool $is_readonly = false,string $title = '',int $colspan = 0) {
		$tr_attributes = [];
		$th_attributes = [];
		$tr_attributes['id'] = sprintf('%s_tr',$p->get_id());
		$th_attributes['class'] = 'lhetop';
		if($this->option_exists('tablesort')):
			$tr_attributes['class'] = 'tablesorter-ignoreRow';
		endif;
		if($colspan > 0):
			$th_attributes['colspan'] = $colspan;
		endif;
		$spanleft_attributes = ['style' => 'float:left'];
		$spanright_attributes = ['style' => 'float:right'];
		$input_attributes = [
			'type' => 'checkbox',
			'id' => $p->get_id(),
			'name' => $p->get_name(),
			'class' => 'formfld cblot',
			'value' => 'yes',
			'class' => 'oneemhigh'
		];
		if(isset($value) && $value):
			$input_attributes['checked'] = 'checked';
		endif;
		if($is_readonly):
			$input_attributes['disabled'] = 'disabled';
			$is_required = false;
		endif;
		if($is_required):
			$input_attributes['required'] = 'required';
		endif;
		$span_attributes = ['class' => 'cblot'];
		$this->
			addTR($tr_attributes)->
				addTH($th_attributes)->
					insSPAN($spanleft_attributes,$title)->
					addSPAN($spanright_attributes)->
						addElement('label')->
							insElement('input',$input_attributes)->
							addSPAN($span_attributes,$p->get_caption());
		return $this;
	}
	public function ins_description(properties $p) {
		//	description can be:
		//	string
		//	[string, ...]
		//	[ [string], ...]
		//	[ [string,no_br], ...]
		//	[ [string,color], ...]
		//	[ [string,color,no_br], ...]
		$description = $p->get_description();
		if(isset($description)):
			$description_output = '';
			$suppressbr = true;
			if(!empty($description)): // string or array
				if(is_string($description)):
					$description_output = $description;
				elseif(is_array($description)):
					foreach($description as $description_row):
						if(is_string($description_row)):
							if($suppressbr):
								$description_output .= $description_row;
								$suppressbr = false;
							else:
								$description_output .= ('<br />' . $description_row);
							endif;
						elseif(is_array($description_row)):
							switch(count($description_row)):
								case 1:
									if(is_string($description_row[0])):
										if($suppressbr):
											$suppressbr = false;
										else:
											$description_output .= '<br />';
										endif;
										$description_output .= $description_row[0];
									endif;
									break;
								case 2:
									if(is_string($description_row[0])):
										$color = NULL;
										if(is_string($description_row[1])):
											$color = $description_row[1];
										endif;
										if(is_bool($description_row[1])):
											$suppressbr = $description_row[1];
										endif;
										if($suppressbr):
											$suppressbr = false;
										else:
											$description_output .= '<br />';
										endif;
										if(is_null($color)):
											$description_output .= $description_row[0];
										else:
											$description_output .= sprintf('<span style="color:%2$s">%1$s</span>',$description_row[0],$color);
										endif;
									endif;
									break;
								case 3: // allow not to break
									if(is_string($description_row[0])):
										$color = NULL;
										if(is_string($description_row[1])):
											$color = $description_row[1];
										endif;
										if(is_bool($description_row[2])):
											$suppressbr = $description_row[2];
										endif;
										if($suppressbr):
											$suppressbr = false;
										else:
											$description_output .= '<br />';
										endif;
										if(is_null($color)):
											$description_output .= $description_row[0];
										else:
											$description_output .= sprintf('<span style="color:%2$s">%1$s</span>',$description_row[0],$color);
										endif;
									endif;
									break;
							endswitch;
						endif;
					endforeach;
				endif;
			endif;
			if(!empty($description_output)):
				$this->addDIV(['class' => 'formfldadditionalinfo'],$description_output);
			endif;
		endif;
		return $this;
	}
	public function ins_checkbox(properties $p,$value,bool $is_required = false,bool $is_readonly = false) {
		$input_attributes = [
			'type' => 'checkbox',
			'id' => $p->get_id(),
			'name' => $p->get_name(),
			'value' => 'yes',
			'class' => 'oneemhigh'
		];
		if(isset($value) && $value):
			$input_attributes['checked'] = 'checked';
		endif;
		if($is_readonly):
			$class_checkbox = 'celldatacheckbox';
			$input_attributes['disabled'] = 'disabled';
			$is_required = false;
		else:
			$class_checkbox = 'celldatacheckbox';
		endif;
		if($is_required):
			$class_checkbox = 'celldatacheckbox';
			$input_attributes['required'] = 'required';
		endif;
		$div = $this->addDIV(['class' => $class_checkbox]);
		$div->addElement('input',$input_attributes);
		$div->addElement('label',['for' => $p->get_id()],$p->get_caption());
		return $this;
	}
	public function ins_input_text(properties $p,$value,bool $is_required = false,bool $is_readonly = false) {
		$id = $p->get_id();
		$caption = $p->get_caption();
		$input_attributes = [
			'type' => 'text',
			'id' => $id,
			'name' => $p->get_name(),
			'value' => $value
		];
		if($is_readonly):
			$input_attributes['class'] = 'formfldro';
			$input_attributes['disabled'] = 'disabled';
			$is_required = false;
			$maxlength = 0;
			$placeholder = NULL;
		else:
			$input_attributes['class'] = 'formfld';
			$maxlength = $p->get_maxlength();
			$placeholder = $p->get_placeholder();
		endif;
		if($is_required):
			$input_attributes['class'] = 'formfld';
			$input_attributes['required'] = 'required';
		endif;
		if(isset($placeholder)):
			$input_attributes['placeholder'] = $placeholder;
		endif;
		$size = $p->get_size();
		if($size > 0):
			$input_attributes['size'] = $size;
		endif;
		if($maxlength > 0):
			$input_attributes['maxlength'] = $maxlength;
		endif;
		$div = $this->addDIV();
		$div->addElement('input',$input_attributes);
		if(isset($caption)):
			if($is_readonly):
				$div->addElement('span',['style' => 'margin-left: 0.7em;'],$caption);
			else:
				$div->addElement('label',['style' => 'margin-left: 0.7em;','for' => $id],$caption);
			endif;
		endif;
		return $this;
	}
	public function ins_checkbox_grid(properties $p,$value,bool $is_required = false,bool $is_readonly = false) {
		$input_attributes = [
			'name' => sprintf('%s[]',$p->get_name()),
			'type' => 'checkbox',
			'class' => 'oneemhigh'
		];
		if($is_readonly):
			$input_attributes['disabled'] = 'disabled';
			$is_required = false;
		endif;
		if($is_required):
			$input_attributes['required'] = 'required';
		endif;
		$table = $this->add_table_data_selection();
		$table->ins_colgroup_with_styles('width',['5%','95%']);
		$table->
			addTHEAD()->
				addTR()->
					insTHwC('lhelc')->
					insTHwC('lhebl',$p->get_title());
		$tbody = $table->addTBODY();
		foreach($p->get_options() as $option_tag => $option_val):
			//	create a unique identifier for each row and use label tag for text
			//	column to allow toggling the checkbox button by clicking on the text.
			$input_attributes['value'] = $option_tag;
			$input_attributes['id'] = sprintf('checkbox_%s',uuid());
			if(is_array($value) && in_array($option_tag,$value)):
				$input_attributes['checked'] = 'checked';
			elseif(array_key_exists('checked',$input_attributes)):
				unset($input_attributes['checked']);
			endif;
			$tr = $tbody->addTR();
			$tr->addTDwC('lcelc')->addElement('input',$input_attributes);
			$tr->addTDwC('lcebl')->addElement('label',['for' => $input_attributes['id'],'style' => 'white-space:pre-wrap;'],$option_val);
		endforeach;
	}

	public function ins_filechooser($p,$value,bool $is_required = false,bool $is_readonly = false) {
		$id = $p->get_id();
		$name = $p->get_name();
		$input_attributes = [
			'type' => 'text',
			'id' => $id,
			'name' => $name,
			'value' => $value
		];
		if($is_readonly):
			$input_attributes['class'] = 'formfldro';
			$input_attributes['disabled'] = 'disabled';
			$is_required = false;
			$maxlength = 0;
			$placeholder = NULL;
		else:
			$input_attributes['class'] = 'formfld';
			$maxlength = $p->get_maxlength();
			$placeholder = $p->get_placeholder();
		endif;
		if($is_required):
			$input_attributes['class'] = 'formfld';
			$input_attributes['required'] = 'required';
		endif;
		if(isset($placeholder)):
			$input_attributes['placeholder'] = $placeholder;
		endif;
		$size = $p->get_size();
		if($size > 0):
			$input_attributes['size'] = $size;
		endif;
		if($maxlength > 0):
			$input_attributes['maxlength'] = $maxlength;
		endif;
		$div = $this->addDIV();
		$div->addElement('input',$input_attributes);
//	file chooser start
		if(!$is_readonly):
			$var = 'ifield';
			$idifield = sprintf('%1$s%2$s',$id,$var);
			$js = <<<EOJ
{$idifield} = form.{$id};
filechooser = window.open("filechooser.php?p="+encodeURIComponent({$idifield}.value)+"&sd={$value}","filechooser","scrollbars=yes,toolbar=no,menubar=no,statusbar=no,width=550,height=300");
filechooser.{$var} = {$idifield};
window.{$var} = {$idifield};
EOJ;
			$button_attributes = [
				'type' => 'button',
				'id' => $id . 'browsebtn',
				'name' => $name . 'browsebtn',
				'class' => 'formbtn',
				'onclick' => $js,
				'value' => '...'
			];
			$div->addElement('input',$button_attributes);
		endif;
//	file chooser end
		$caption = $p->get_caption();
		if(isset($caption)):
			if($is_readonly):
				$div->addElement('span',['style' => 'margin-left: 0.7em;'],$caption);
			else:
				$div->addElement('label',['style' => 'margin-left: 0.7em;','for' => $p->get_id()],$p->get_caption());
			endif;
		endif;
		return $this;
	}
	public function ins_radio_grid(properties $p,$value,bool $is_required = false,bool $is_readonly = false) {
		$table = $this->add_table_data_selection();
		$table->ins_colgroup_with_styles('width',['5%','95%']);
		$table->
			addTHEAD()->
				addTR()->
					insTHwC('lhelc')->
					insTHwC('lhebl',$p->get_title());
		$tbody = $table->addTBODY();
		$input_attributes = [
			'name' => $p->get_name(),
			'type' => 'radio',
			'class' => 'oneemhigh'
		];
		if($is_readonly):
			$input_attributes['disabled'] = 'disabled';
			$is_required = false;
		endif;
		if($is_required):
			$input_attributes['required'] = 'required';
		endif;
		foreach($p->get_options() as $option_tag => $option_val):
			//	create a unique identifier for each row.
			$uuid = uuid();
			//	use label tag for text column to allow enabling the radio button by clicking on the text
			$input_attributes['value'] = $option_tag;
			$input_attributes['id'] = sprintf('radio_%s',$uuid);
			if($value === $option_tag):
				$input_attributes['checked'] = 'checked';
			elseif(array_key_exists('checked',$input_attributes)):
				unset($input_attributes['checked']);
			endif;
			$tr = $tbody->addTR();
			$tr->addTDwC('lcelc')->addElement('input',$input_attributes);
			$tr->addTDwC('lcebl')->addElement('label',['for' => $uuid,'style' => 'white-space:pre-wrap;'],$option_val);
		endforeach;
		return $this;
	}
	public function ins_select(properties $p,$value,bool $is_required = false,bool $is_readonly = false) {
		$caption = $p->get_caption();
		$select_attributes = [
			'id' => $p->get_id(),
			'name' => $p->get_name(),
			'class' => 'formfld'
		];
		if($is_readonly):
			$select_attributes['disabled'] = 'disabled';
			$is_required = false;
		endif;
		if($is_required):
			$select_attributes['required'] = 'required';
		endif;
		$select = $this->addElement('select',$select_attributes);
		foreach($p->get_options() as $option_tag => $option_val):
			$option_attributes = ['value' => $option_tag];
			if($value == $option_tag):
				$option_attributes['selected'] = 'selected';
			endif;
			$select->addElement('option',$option_attributes,$option_val);
		endforeach;
		if(isset($caption)):
			$this->addElement('span',['style' => 'margin-left: 0.7em;'],$caption);
		endif;
		return $this;
	}
	public function ins_separator(int $colspan = 0,string $id = NULL) {
		$tr_attributes = [];
		if(isset($id) && preg_match('/\S/',$id)):
			$tr_attributes['id'] = sprintf('%s_tr',$id);
		endif;
		$td_attributes = [
			'class' => 'gap'
		];
		if($colspan > 0):
			$td_attributes['colspan'] = $colspan;
		endif;
		$this->addTR($tr_attributes)->addTD($td_attributes);
		return $this;
	}
	public function ins_textinfo(string $id = NULL,string $value = NULL) {
		if(isset($value)):
			$span_attributes  = [];
			if(isset($id)):
				$span_attributes = ['id' => $id];
			endif;
			$this->addElement('span',$span_attributes,$value);
		endif;
		return $this;
	}
	//	elements requiring sphere
	public function ins_cbm_checkbox_toggle($sphere) {
		$element = 'input';
		$cbm_toggle_id = $sphere->get_cbm_checkbox_id_toggle();
		$input_attributes = [
			'type' => 'checkbox',
			'name' => $cbm_toggle_id,
			'id' => $cbm_toggle_id,
			'title' => gtext('Invert Selection'),
			'class' => 'oneemhigh'
		];
		$this->addElement($element,$input_attributes);
		return $this;
	}
	public function ins_cbm_checkbox($sphere,bool $disabled = false) {
		$element = 'input';
		$identifier = $sphere->get_row_identifier_value();
		$input_attributes = [
			'type' => 'checkbox',
			'name' => $sphere->cbm_name . '[]',
			'value' => $identifier,
			'id' => $identifier,
			'class' => 'oneemhigh'
		];
		if($disabled):
			$input_attributes['disabled'] = 'disabled';
		endif;
		$this->addElement($element,$input_attributes);
		return $this;
	}
	public function add_toolbox_area() {
		$subnode = $this->
			addTDwC('lcebld')->
				addTABLE(['class' => 'area_data_selection_toolbox'])->
					ins_colgroup_with_styles('width',['33%','34%','33%'])->
					addTBODY()->
						addTR();
		return $subnode;
	}
	public function ins_toolbox($sphere,bool $notprotected = true,bool $notdirty = true) {
		global $g_img;
/*
 *	<td>
 *		<a href="scriptname_edit.php?submit=edit&uuid=12345678-1234-1234-1234-1234567890AB"><img="images/edit.png" title="Edit Record" alt="Edit Record" class="spin"/></a>
 *		or
 *		<img src="images/delete.png" title="Record is marked for deletion" alt="Record is marked for deletion"/>
 *		or
 *		<img src="images/locked.png" title="Record is protected" alt="Record is protected"/>
 *	</td>
 */
		if($notdirty && $notprotected): // record is editable
			$link = sprintf('%s?submit=edit&%s=%s',$sphere->modify->scriptname(),$sphere->row_identifier(),$sphere->get_row_identifier_value());
			$this->addTD()->
				addElement('a',['href' => $link])->
					addElement('img', ['src' => $g_img['mod'],'title' => $sphere->sym_mod(),'alt' => $sphere->sym_mod(),'class' => 'spin','class' => 'oneemhigh']);
		elseif($notprotected): //record is dirty
			$this->addTD()->
				addElement('img',['src' => $g_img['del'],'title' => $sphere->sym_del(),'alt' => $sphere->sym_del(),'class' => 'oneemhigh']);
		else: // record is protected
			$this->addTD()->
				addElement('img',['src' => $g_img['loc'],'title' => $sphere->sym_loc(),'alt' => $sphere->sym_loc(),'class' => 'oneemhigh']);
		endif;
		return $this;
	}
	public function ins_footerwa($sphere,int $colspan = 2) {
		global $g_img;
/*
 *	<tfoot>
 *		<tr>
 *			<th class="lcenl" colspan="1"></th>
 *			<th class="lceadd">
 *				<a href="scriptname_edit.php?submit=add">
 *					<img src="images/add.png" title="Add Record" alt="Add Record" class="spin"/>
 *				</a>
 *			</th>
 *		</tr>
 *	</tfoot>
 */
		$link = sprintf('%s?submit=add',$sphere->modify->scriptname());
		$tr = $this->addTFOOT()->addTR();
		if($colspan > 1):
			$tr->addTH(['class' => 'lcenl','colspan' => $colspan - 1]);
		endif;
		$tr->
			addTHwC('lceadd')->
				addA(['href' => $link])->
					addElement('img',['src' => $g_img['add'],'title' => $sphere->sym_add(),'alt' => $sphere->sym_add(),'class' => 'spin oneemhigh']);
		return $this;
	}
	public function ins_no_records_found(int $colspan = 0) {
		$td_attributes = ['class' => 'lcebl'];
		if($colspan > 0):
			$td_attributes['colspan'] = $colspan;
		endif;
		$this->addTR()->addTD($td_attributes,gtext('No records found.'));
		return $this;
	}
	public function ins_cbm_button_delete($sphere) {
		$this->ins_button_submit($sphere->get_cbm_button_val_delete(),$sphere->cbm_delete(),[],$sphere->get_cbm_button_id_delete());
		return $this;
	}
	public function ins_cbm_button_enadis($sphere) {
		if($sphere->enadis()):
			if($sphere->toggle()):
				$this->ins_button_submit($sphere->get_cbm_button_val_toggle(),$sphere->cbm_toggle(),[],$sphere->get_cbm_button_id_toggle());
			else:
				$this->ins_button_submit($sphere->get_cbm_button_val_enable(),$sphere->cbm_enable(),[],$sphere->get_cbm_button_id_enable());
				$this->ins_button_submit($sphere->get_cbm_button_val_disable(),$sphere->cbm_disable(),[],$sphere->get_cbm_button_id_disable());
			endif;
		endif;
		return $this;
	}
	//	c2 blocks
	public function c2_row(properties $p,bool $is_required = false,bool $is_readonly = false,bool $tagaslabel = false) {
		if($is_readonly):
			$class_tag = 'celltag';
			$class_data = 'celldata';
			$is_required = false;
		else:
			$class_tag = 'celltag';
			$class_data = 'celldata';
		endif;
		if($is_required):
			$class_tag = 'celltagreq';
			$class_data = 'celldatareq';
		endif;
		$tr = $this->addTR(['id' => sprintf('%s_tr',$p->get_id())]);
		if($tagaslabel):
			$tr->addTDwC($class_tag)->addElement('label',['for' => $p->get_id()],$p->get_title());
		else:
			$tr->addTDwC($class_tag,$p->get_title());
		endif;
		$subnode = $tr->addTDwC($class_data);
		return $subnode;
	}
	public function c2_checkbox(properties $p,$value,bool $is_required = false,bool $is_readonly = false) {
		$this->c2_row($p,$is_required,$is_readonly,true)->ins_checkbox($p,$value,$is_required,$is_readonly)->ins_description($p);
		return $this;
	}
	public function c2_checkbox_grid(properties $p,$value,bool $is_required = false,bool $is_readonly = false) {
		$this->c2_row($p,$is_required,$is_readonly,true)->ins_checkbox_grid($p,$value,$is_required,$is_readonly)->ins_description($p);
		return $this;
	}
	public function c2_filechooser(properties $p,$value,bool $is_required = false,bool $is_readonly = false) {
		$this->c2_row($p,$is_required,$is_readonly,true)->ins_filechooser($p,$value,$is_required,$is_readonly)->ins_description($p);
		return $this;
	}
	public function c2_input_text(properties $p,$value,bool $is_required = false,bool $is_readonly = false) {
		$this->
			c2_row($p,$is_required,$is_readonly,true)->
				ins_input_text($p,$value,$is_required,$is_readonly)->
				ins_description($p);
		return $this;
	}
	public function c2_radio_grid(properties $p,$value,bool $is_required = false,bool $is_readonly = false) {
		$this->c2_row($p,$is_required,$is_readonly,true)->ins_radio_grid($p,$value,$is_required,$is_readonly)->ins_description($p);
		return $this;
	}
	public function c2_select(properties $p,$value,bool $is_required = false,bool $is_readonly = false) {
		$this->c2_row($p,$is_required,$is_readonly,true)->ins_select($p,$value,$is_required,$is_readonly)->ins_description($p);
		return $this;
	}
	public function c2_separator() {
		$this->ins_separator(2);
		return $this;
	}
	public function c2_textinfo(string $id,string $title,$value) {
		$tr_attributes = [];
		if(isset($id)):
			$tr_attributes['id'] = sprintf('%s_tr',$id);
		endif;
		$tr = $this->addTR($tr_attributes);
		$tr->addTDwC('celltag',$title);
		$tr->addTDwC('celldata')->ins_textinfo($id,$value);
		return $this;
	}
	public function c2_titleline(string $title = '') {
		$this->ins_titleline($title,2);
		return $this;
	}
	public function c2_titleline_with_checkbox(properties $p,$value,bool $is_required = false,bool $is_readonly = false,string $title = '') {
		$this->ins_titleline_with_checkbox($p,$value,$is_required,$is_readonly,$title,2);
		return $this;
	}
	//	submit area macros
	public function add_area_buttons(bool $use_config_setting = true) {
		global $config;

		$div_attributes = ['id' => 'submit'];
		if($use_config_setting):
			$referrer = 'adddivsubmittodataframe';
			$root = $this->ownerDocument ?? $this;
			if(isset($config['system'][$referrer]) && (is_bool($config['system'][$referrer]) ? $config['system'][$referrer] : true)):
				$target = $root->getElementById('area_data_frame') ?? $this;
				$subnode = $target->addDIV($div_attributes);
			else:
				$target = $root->getElementById('g4f') ?? $this;
				$div_attributes['style'] = 'padding: 0em 2em;';
				$subnode = $target->prepend_element('div',$div_attributes);
			endif;
		else:
			$subnode = $this->addDIV($div_attributes);
		endif;
		return $subnode;
	}
	public function ins_button_submit(string $value = NULL,string $content = NULL,$attributes = [],string $id = NULL) {
		$element      = 'button';
		$class_button = 'formbtn';
		$value        = $value ?? 'cancel';
		$id           = $id ?? sprintf('%1$s_%2$s',$element,$value);
		$content      = $content ?? gtext('Cancel');
		$button_attributes   = [
			'name' => 'submit',
			'type' => 'submit',
			'class' => $class_button,
			'value' => $value,
			'id' => $id
		];
		foreach($attributes as $key => $value):
			switch($key):
				case 'class+':
					$button_attributes['class'] += ' ' . $value;
					break;
				default:
					$button_attributes[$key] = $value;
					break;
			endswitch;
		endforeach;
		$this->addElement($element,$button_attributes,$content);
		return $this;
	}
	public function ins_button_add() {
		$this->ins_button_submit('save',gtext('Add'));
		return $this;
	}
	public function ins_button_apply() {
		$this->ins_button_submit('apply',gtext('Apply Changes'));
		return $this;
	}
	public function ins_button_cancel() {
		$this->ins_button_submit('cancel',gtext('Cancel'),['formnovalidate' => 'formnovalidate']);
		return $this;
	}
	public function ins_button_edit() {
		$this->ins_button_submit('edit',gtext('Edit'));
		return $this;
	}
	public function ins_button_save() {
		$this->ins_button_submit('save',gtext('Apply'));
		return $this;
	}
	//	remark area macros
	public function add_area_remarks() {
		$subnode = $this->addDIV(['id' => 'remarks']);
		return $subnode;
	}
	public function ins_remark($ctrlname,$title,$text) {
		$ctrl = new HTMLRemark2($ctrlname,$title,$text);
		$ctrl->Compose($this);
		return $this;
	}
	public function ins_authtoken() {
		$input_attributes = [
			'name' => 'authtoken',
			'type' => 'hidden',
			'value' => Session::getAuthToken()
		];
		$this->addElement('input',$input_attributes);
		return $this;
	}
	public function clc_page_title(array $page_title = []) {
		$output = implode(htmlspecialchars(' > '),$page_title);
		return $output;
	}
	public function clc_html_page_title(array $page_title = []) {
		$output = htmlspecialchars(system_get_hostname());
		if(!empty($page_title)):
			$output .= htmlspecialchars(' > ');
			$output .= $this->clc_page_title($page_title);
		endif;
		return $output;
	}
	public function ins_head(array $page_title = []) {
		$is_login = $this->option_exists('login');
		$is_tablesort = $this->option_exists('tablesort');
		$is_datechooser = $this->option_exists('datechooser');
		$head = $this->addElement('head',['id' => 'head']);
		$head->
			insElement('meta',['charset' => system_get_language_codeset()])->
			insElement('meta',['name' => 'format-detection','content' => 'telephone=no'])->
			insElement('meta',['name' => 'viewport','content' => 'width=device-width, initial-scale=1.0'])->
			insElement('title',[],$this->clc_html_page_title($page_title))->
			insElement('link',['href' => '/css/gui.css','rel' => 'stylesheet','type' => 'text/css'])->
			insElement('link',['href' => '/css/navbar.css','rel' => 'stylesheet','type' => 'text/css'])->
			insElement('link',['href' => '/css/tabs.css','rel' => 'stylesheet','type' => 'text/css']);
		if($is_login):
			$head->
				insElement('link',['href' => '/css/login.css','rel' => 'stylesheet','type' => 'text/css']);
		endif;
		$head->
			insElement('script',['type' => 'text/javascript','src' => '/js/jquery.min.js'])->
			insElement('script',['type' => 'text/javascript','src' => '/js/gui.js'])->
			insElement('script',['type' => 'text/javascript','src' => '/js/spinner.js'])->
			insElement('script',['type' => 'text/javascript','src' => '/js/spin.min.js']);
		if($is_tablesort):
			$head->
				insElement('script',['type' => 'text/javascript','src' => '/js/jquery.tablesorter.min.js']);
//				insElement('script',['type' => 'text/javascript','src' => '/js/jquery.tablesorter.widgets.min.js']);
		endif;
		if($is_datechooser):
			$head->
				insElement('link',['href' => 'js/datechooser.css','rel' => 'stylesheet','type' => 'text/css'])->
				insElement('script',['type' => 'text/javascript','src' => 'js/datechooser.js']);
		endif;
		return $this;
	}
	/**
	 *	Creates the body element of the page with all basic subnodes.
	 *
	 *	@param array $page_title
	 *	@param string $action_url If $action_url empty no form element will be created.
	 *	@return DOMNode $this
	 */
	public function ins_body(array $page_title = [],string $action_url = NULL) {
		$is_login = $this->option_exists('login');
		$is_multipart = $this->option_exists('multipart');
		$is_tablesort = $this->option_exists('tablesort');
		$is_form = (isset($action_url) && preg_match('/^\S+$/',$action_url));
		$is_spinonsubmit = $is_form && !$this->option_exists('nospinonsubmit');
		$is_tabnav = !($is_login || $this->option_exists('notabnav'));
		$jdata = <<<'EOJ'
$(window).on("load", function() {
	$(".spin").click(function() { spinner(); });

EOJ;
		if($is_spinonsubmit):
			$jdata .= <<<'EOJ'
	$("#iform").submit(function() { spinner(); });

EOJ;
		endif;
		if($is_tabnav):
			$jdata .= <<<'EOJ'
	$("#tabnav").on('click', function() { spinner(); });
	$("#tabnav2").on('click', function() { spinner(); });

EOJ;
		endif;
		$jdata .= <<<'EOJ'
});
EOJ;
		if($is_tablesort):
			$jdata .= <<<'EOJ'
$(document).ready(function() { 
	$(".area_data_selection").tablesorter({
		emptyTo: 'none'
	});
}); 
EOJ;
		endif;
		$body = $this->addElement('body',['id' => 'main']);
		if($is_form):
			$form_attributes = [
				'action' => $action_url,
				'method' => 'post',
				'id' => 'iform',
				'name' => 'iform'
			];
			if($is_multipart):
				$form_attributes['enctype'] = 'multipart/form-data';
			endif;
			$flexcontainer = $body->addFORM($form_attributes)->addDIV(['id' => 'pagebodyflex']);
		else:
			$flexcontainer = $body->addDIV(['id' => 'pagebodyflex']);
		endif;
		$flexcontainer->addDIV(['id' => 'spinner_main']);
		$flexcontainer->addDIV(['id' => 'spinner_overlay','style' => 'display: none; background-color: white; position: fixed; left:0; top:0; height:100%; width:100%; opacity: 0.25;']);
		if(!$is_login && $is_form):
			$flexcontainer->addDIV(['id' => 'formextension'])->ins_authtoken();
		endif;
		if($is_login):
			$flexcontainer->ins_header_login();
		else:
			$flexcontainer->ins_header_logo();
			$flexcontainer->ins_header($page_title);
		endif;
		$flexcontainer->ins_main();
		$flexcontainer->ins_footer();
		$flexcontainer->addJavascript($jdata);
		return $this;
	}
	public function ins_header_logo() {
		if(!$_SESSION['g']['shrinkpageheader']):
			$a_attributes = [
				'title' => sprintf('www.%s',get_product_url()),
				'href' => sprintf('https://www.%s',get_product_url()),
				'target' => '_blank'
			];
			$img_attributes = [
				'src' => '/images/header_logo.png',
				'alt' => 'logo'
			];
			$this->addElement('header',['id' => 'g4l'])->
				addDIV(['id' => 'header'])->
					push()->
					addDIV(['id' => 'headerrlogo'])->
						addDIV(['class' => 'hostname'])->
							addSPAN([],system_get_hostname())->
					pop()->
					addDIV(['id' => 'headerlogo'])->
						addA($a_attributes)->
							insIMG($img_attributes);
		endif;
		return $this;
	}
	public function ins_header_login() {
		$header = $this->
			addElement('header',['id' => 'g4h'])->
				addDIV(['id' => 'gapheader']);
		return $this;
	}
	public function ins_header(array $page_title = []) {
		$header = $this->addElement('header',['id' => 'g4h']);
		$header->addDIV(['id' => 'area_navhdr'],make_headermenu());
		$header->addDIV(['id' => 'gapheader']);
		if(!empty($page_title)):
			$header->addP(['class' => 'pgtitle','style' => 'padding:0em 2em;' ],$this->clc_page_title($page_title));
		endif;
		return $this;
	}
	public function ins_main() {
		$this->
			addElement('main',['id' => 'g4m'])->
				addDIV(['id' => 'pagecontent']);
		return $this;
	}
	/**
	 *
	 * @global array $g
	 * @return $this
	 */
	public function ins_footer() {
		global $g;

		$g4fx = $this->
			addElement('footer',['id' => 'g4f'])->
				insDIV(['id' => 'gapfooter'])->
				addDIV(['id' => 'pagefooter'])->
					add_table_data_settings()->
						ins_colgroup_with_styles('width',['10%','80%','10%'])->
						addTBODY()->
							addTR();
		$g4fl = $g4fx->addTDwC('g4fl');
		if(Session::isAdmin()):
			if(file_exists(sprintf('%s/sysreboot.reqd',$g['varrun_path']))):
				$img_attributes = [
					'src' => '/images/notify_reboot.png',
					'title' => gtext('A reboot is required'),
					'alt' => gtext('Reboot Required'),
					'class' => 'oneemhigh'
				];
				$g4fl->
					addA(['class' => 'g4fi','href' => '/reboot.php'])->
						insIMG($img_attributes);
			endif;
		endif;
		$g4fx->addTDwC('g4fc',htmlspecialchars(get_product_copyright()));
		$g4fx->addTDwC('g4fr');
		return $this;
	}
}
class co_DOMElement extends \DOMElement implements ci_DOM {
	use co_DOMTools;

	public function addAttributes($attributes = []) {
		foreach($attributes as $key => $value):
			$this->setAttribute($key,$value);
		endforeach;
		return $this;
	}
	public function option_exists(string $option) {
		return $this->ownerDocument->option_exists($option);
	}
	public function push() {
		$this->ownerDocument->push($this);
		return $this;
	}
	public function pop() {
		return $this->ownerDocument->pop();
	}
	public function last() {
		return $this->ownerDocument->last();
	}
}
class co_DOMDocument extends \DOMDocument implements ci_DOM {
	use co_DOMTools;

	protected $stack = [];
	protected $options = [];
	
	public function __construct(string $version = '1.0',string $encoding = 'UTF-8') {
		parent::__construct($version,$encoding);
		$this->preserveWhiteSpace = false;
		$this->formatOutput = true;
		$this->registerNodeClass('DOMElement','co_DOMElement');
	}
	public function set_options(string ...$options) {
		foreach($options as $value):
			$this->options[$value] = $value;
		endforeach;
		return $this;
	}
	public function option_exists(string $option) {
		return array_key_exists($option,$this->options);
	}
	public function push($element) {
		array_push($this->stack,$element);
		return $element;
	}
	public function pop() {
		return array_pop($this->stack);
	}
	public function last() {
		$element = end($this->stack);
		reset($this->stack);
		return $element;
	}
	public function render() {
		echo $this->saveHTML();
		return $this;
	}
	public function get_html() {
		return $this->saveHTML();
	}
}
interface ci_DOM {
}
/**
 * 
 * @param array $page_title
 * @param string $action_url
 * @param string ...$options
 * @return \co_DOMDocument
 */
/*
 *	login
 *	datechooser
 *	multipart
 *	nospinonsubmit
 *	notabnav
 *	tablesort
 */
function new_page(array $page_title = [],string $action_url = NULL,string ...$options) {
	$document = new co_DOMDocument();
	$document->
		loadHTML('<!DOCTYPE html>',LIBXML_HTML_NOIMPLIED);
	$document->
		set_options(...$options)->
		addElement('html',['lang' => system_get_language_code()])->
			ins_head($page_title)->
			ins_body($page_title,$action_url);
	return $document;
}
?>
