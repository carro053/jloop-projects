<?php
App::uses('AppHelper', 'View');

class EditableHelper extends AppHelper {

	public function editable($field) {
		return '<span class="editable">'.$field.'</span>';
	}
	
}