<?php
// file: /app/views/helpers/error.php
 
class ErrorHelper extends Helper {
 
  function messageFor($target) {
      list($model, $field) = explode('/', $target);
 
      if (isset($this->validationErrors[$model][$field])) {
          return '<span class="form_error_message">'.$this->validationErrors[$model][$field].'</span>';
      } else {
          return null;
      }
  }
  function customError($var) {
  	return '<span class="form_error_message">'.$var.'</span>';
  }
  
  function allMessagesFor($model) {
    $html =& new HtmlHelper;
    
    if (isset($this->validationErrors[$model])) {
      $list = '';
      foreach (array_keys($this->validationErrors[$model]) as $field) {
        $list .= $html->contentTag('li', $this->validationErrors[$model][$field]);
      }
      return $html->contentTag('div', 
        $html->contentTag('h4', 'The following errors need to be corrected: ') . 
        $html->contentTag('ul', $list), array('class'=>'error_messages'));
    }
  }
}
 
?>