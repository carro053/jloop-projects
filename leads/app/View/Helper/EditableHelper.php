<?php
App::uses('AppHelper', 'View');

class EditableHelper extends AppHelper {

	public function contact($field, $data) {
		return '<span class="editable" data-type="text" data-pk="'.$data['id'].'" data-name="'.$field.'" data-url="/Contacts/updateField" data-title="Enter '.$field.'">'.$data[$field].'</span>';
	}
	
}