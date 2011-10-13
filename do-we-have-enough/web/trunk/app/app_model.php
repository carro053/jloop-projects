<?PHP
// file: /app/app_model.php
 
define('VALID_WORD', '/^\\w+$/');
define('VALID_UNIQUE', 'isUnique');
define('VALID_AGE', 'isAge');
define('VALID_PARENT', 'isParent');
define('VALID_LENGTH_WITHIN', 'isLengthWithin');
define('VALID_VALUE_WITHIN', 'isValueWithin');
define('VALID_CONFIRMED', 'isConfirmed');
define('VALID_BOOL_POSITIVE', 'isBoolPositive');
define('VALID_NO_SPACES', 'isNoSpaces');
 
class AppModel extends Model {


	
	///////////VALIDATION FUNCTIONS
 
  // If you need to disable validation for particular columns, you may populate this variable like so:
  // $this->User->disabledValidate = array('email', 'password'); // disables validation on email and password columns
  // $this->User->disabledValidate = array(
  //   'email',
  //   'password' => array('confirmed', 'required')
  // ); // disables validation on email column, and password['confirmed'] && password['required']
  var $disabledValidate;
  
  function loadValidation() {
    // placeholder for overloading
  }
     
  function invalidFields($data = array()) {
    $this->loadValidation();
    
    if (is_array($this->disabledValidate)) {
      foreach($this->disabledValidate as $field => $params) {
        if (is_string($field) && is_array($params)) {
          foreach($params as $param) {
            if (is_string($param)) {
              $this->validate[$field][$param] = false;
            }
          }
        } else if (is_int($field) && is_string($params)) {
          $this->validate[$params] = false;
        }
      }
    }
    
    //debug($this->validate);
    
    if (!isset($this->validate) || !empty($this->validationErrors)) {
      if (!isset($this->validate)) {
        return true;
      } else {
        return $this->validationErrors;
      }
    }
 
    if (isset($this->data)) {
      $data = array_merge($data, $this->data);
    }
 
    $errors = array();
    $this->set($data);
 
    foreach ($data as $table => $field) {
      foreach ($this->validate as $field_name => $validators) {
        if ($validators) {      
          foreach($validators as $validator) {
            if (isset($validator['method'])) {
              if (method_exists($this, $validator['method'])) {
                $parameters = (isset($validator['parameters'])) ? $validator['parameters'] : array();
                $parameters['var'] = $field_name;
                if (isset($data[$table][$field_name]) &&
                  !call_user_func_array(array(&$this, $validator['method']),array($parameters))) {
                  if (!isset($errors[$field_name])) {
                    $errors[$field_name] = isset($validator['message']) ? $validator['message'] : 1;
                  }
                }
              } else {
                if (isset($data[$table][$field_name]) &&
                  !preg_match($validator['method'], $data[$table][$field_name])) {
                  if (!isset($errors[$field_name])) {
                    $errors[$field_name] = isset($validator['message']) ? $validator['message'] : 1;
                  }
                }
              }
            }
          }
        }
      }
    }
    $this->validationErrors = $errors;
    return $errors;
  }
  
  // validation methods
    
  function isUnique($params) {
    $val = $this->data[$this->name][$params['var']];
    $db = $this->name . '.' . $params['var'];
    $id = $this->name . '.id';
	if(!isset($this->data[$this->name]['id'])) $this->data[$this->name]['id'] = null;
    if($this->data[$this->name]['id'] == null ) {
      return(!$this->hasAny(array($db => $val ) ));
    } else {
      return(!$this->hasAny(array($db => $val, $id => '!= '.$this->data[$this->name]['id'] ) ) );
    }
  }
 
  function isLengthWithin($params) {
    $val = $this->data[$this->name][$params['var']];
    $length = strlen($val);
 
    if (array_key_exists('min', $params) && array_key_exists('max', $params)) {
      return $length >= $params['min'] && $length <= $params['max'];
    } else if (array_key_exists('min', $params)) {
      return $length >= $params['min'];
    } else if (array_key_exists('max', $params)) {
      return $length <= $params['max'];
    }
  }

  function isValueWithin($params) {
    $val = $this->data[$this->name][$params['var']];
    return $params['min'] < $val && $params['max'] > $val;
  }
 
  function isConfirmed($params) {
    $val = $this->data[$this->name][$params['var']];
    $val_confirmation = array_key_exists('confirm_var', $params) ?
      $this->data[$this->name][$params['confirm_var']] :
      $this->data[$this->name][$params['var'].'_confirmation'];
    return $val == $val_confirmation;
  }
  
  function isBoolPositive($params) {
  	$val = $this->data[$this->name][$params['var']];
	return $val == 1;
  }
  function isNoSpaces($params) {
    $val = $this->data[$this->name][$params['var']];
	if (strpos($val, " ") != false) return false;
	else return true;
  }
  function isAge($params) {
    $val = $this->data[$this->name][$params['var']];
	if (!$this->validateBirth($val)) return false;
	else return true;
  }
}
 
?>