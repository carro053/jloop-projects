<?php
App::uses('AppHelper', 'View');

class EditableHelper extends AppHelper {

	public function editable($field) {
		return '<a href="#" class="editable" data-type="text" data-pk="1" data-url="/Contacts/updateField" data-title="Enter '.$field.'">'.$field.'</a>';
	}
	
}