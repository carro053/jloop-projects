<?php
App::uses('AppHelper', 'View');

class EditableHelper extends AppHelper {

	public function contact($field, $data, $options = array()) {
		$source = '';
		if(!empty($options)) {
			$source = ' data-source="[{"value":"1","text":"yes"},{"value":"0","text":"no"}]"';
			
		}
		return '<span class="editable" data-type="text" data-pk="'.$data['id'].'" data-name="'.$field.'" data-url="/Contacts/updateField" data-title="Enter '.$field.'"'.$source.'>'.$data[$field].'</span>';
	}
	
}