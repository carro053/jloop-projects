<?php
/*
 * WebServices Controller 
 *
 * @author      RosSoft
 * @version     0.1
 * @license		MIT
 *
 */

class WebServicesController extends AppController
{
	var $api            =   null;
	var $complexTypes   =   null;
	var $_soap_server;
	var $name           =   'WebServices';	
	var $autoRender     =   false;
    var $ignoreFields   =   array();
    
	/** 
	 * converts sql-type to xsd-type
	 */	
	var $columnTypes=array(
		'varchar'=>'xsd:string',
		'tinyint'=>'xsd:integer',
        'integer'=>'xsd:integer',
        'string' => 'xsd:string',
		'text'=>'xsd:string',
		'date'=>'xsd:string',
		'smallint'=>'xsd:integer',
		'mediumint'=>'xsd:integer',
		'int'=>'xsd:integer',
		'bigint'=>'xsd:integer',
		'float'=>'xsd:float',
		'double'=>'xsd:float', //maybe exists xsd:double
		'decimal'=>'xsd:float',
		'datetime'=>'xsd:string',
		'timestamp'=>'xsd:integer',
		'time'=>'xsd:string',
		'year'=>'xsd:string',
		'char'=>'xsd:string',		
		'binary'=>'xsd:base64binary',
		'varbinary'=>'xsd:string',);					

	function beforeFilter()
	{
        // everything is handle by index
		if ($this->action!='index' /*&& $this->action!='wsdl'*/) 
		{       
			$this->action='index';
			$this->index();			
		}
        else
            // done for working with mod-rewrite (to avoid the WSDL web description to contain the webroot/index.php?wsdl
            // with this it will contain servers?wsdl (the ? wsdl will be handled by the above method)
		    $_SERVER['PHP_SELF'] = FULL_BASE_URL.$this->base.'/'.$this->name;

        // be sure to turn the debug < 2, the cakeDebug will screw up the xml
        
        if(Configure::read() >= 2)
            Configure::write(array('debug' => 1));            
	}
	
	function index()
	{
		//clean the output		
        if(ob_get_length() > 0)
           ob_end_clean();						
        
        // import nusoap
        
		App::import('Vendor', 'nusoap');

        // create a server instance
        
		$this->_soap_server=new soap_server();

		$wsdl="{$this->name}";
		$urn="urn:$wsdl";
        
        // this will be our endpoint
        $endpoint = FULL_BASE_URL.$this->base.'/'.$this->name;
        // create the WSDL
		$this->_soap_server->configureWSDL($wsdl, $urn, $endpoint);
        // register a service for each api
		foreach ($this->api as $name => $method)
		{			
			if (isset($method['output']))
			{
				if (is_array($method['output']))
				{
					$output=$this->_convertIOArray($method['output']);
				}
				else
				{
					$output=array('return'=>$method['output']);
					$output=$this->_convertIOArray($output);
				}
			}
			else
			{
				$output=array();
			}
			
			
			if (isset($method['input'])
                && is_array($method['input']) 
                && $method['input']!=array())
			{				
				$input=$this->_convertIOArray($method['input']);
			}
			else
			{
				$input=array();
			}
			
			$doc=(isset($method['doc']))? $method['doc'] : '';
			$this->_soap_server->register($name,array($this,$name),$input,$output,
                                          $urn, "$urn#$name",						  
                                          'rpc',		// style
                                          'encoded', // use
                                          $doc);	// documentation
    							
    		
		}

        // build the complexTypes structures

		$this->_buildComplexTypes();
		// get the raw data, if not there this will be redirect to "wsdl" method
		global $HTTP_RAW_POST_DATA;		    						
		$data = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
		$this->_soap_server->service($data);
		exit();		
	}

	/**
	 * Auxiliar method for building the complex types
	 * parsing the $complexTypes array
	 */	
	function _buildComplexTypes()
	{
		if ($this->complexTypes)
		{
			foreach ($this->complexTypes as $name=> $def)
			{
				if (isset($def['array']))
				{
					$item=$def['array'];
					if (strpos($item,'xsd:')===false)
					{
						$item="tns:$item";
					}
					$phpType='array';
					$compositor='';
					$restrictionBase='SOAP-ENC:Array';
					$elements=array();
					$attrs=array(array('ref'=>'SOAP-ENC:arrayType',
										'wsdl:arrayType'=>"{$item}[]"));
					$arrayType=$item;
				}
				else if (isset($def['struct']))
				{    					
					$phpType='struct';
					$compositor='all';
					$restrictionBase='';
					$elements=array();
					foreach ($def['struct'] as $n=>$v)
					{
					    // model in structure
					    if(is_array($v) && isset($v['model']))
					    {
					       $this->_explodeModel($v, $elements);
					    }
					    else
                           $elements[$n]=array('name'=>$n,'type'=>$v);    						
					}                    
					$attrs=array();
					$arrayType='';
				}
				else if (isset($def['model']))
				{    					
					$phpType='struct';
					$compositor='any';
					$restrictionBase='';
					$elements=array();
                    $this->_explodeModel($def, $elements);
					$attrs=array();
					$arrayType='';										    										
				}
				else
                    continue;

				$this->_soap_server->wsdl->addComplexType($name,'complexType',$phpType,$compositor,$restrictionBase,$elements,$attrs,$arrayType);    				
			}
		}		
	}
    
    // build the parameters "model" for complex type
    
	function _explodeModel(&$def, &$elements)
	{   
        $ldef = array_merge(array('ignoreFields' => $this->ignoreFields, 'onlyFields' => array()), $def);
        
        // get the model name
        
		$columns=$this->{$ldef['model']}->getColumnTypes();

        foreach ($columns as $n=>$v)
		{												
			$pos=strpos($v,"(");
			if($pos)
		    	$v=substr($v,0,$pos);

            $include = true;
						
            // filter the fields ?
            
			if(!empty($ldef['onlyFields']))
			{
			    if(!in_array($n,$ldef['onlyFields']))
					$include = false;
			}						 							
             
            // check if the fields is not in ignoreFields 

            if($include)
            {
                if(in_array($n,$ldef['ignoreFields']))
                    $include = false;
		    }
									
			if($include)
			    $elements[$n]=array('name'=>$n,'type'=>$this->columnTypes[$v]);						
		}		
	}
	
	/** 
	 * Returns a soap error with the $data variable printed in it
	 * This error will be returned to the client.
	 * In your method, do:
	 *    return $this->soapDebug($variableToDebug);
	 */
	function soapDebug($data)
	{
		return new soap_fault('Debug','',print_r($data,true),'');	
	}
	
	/**
	 * Converts the values an Input/Ouput array.
	 * Sets the namespace tns: for each of the elements
	 * that doesn't have any namespace. 
	 * @param array $array The input/output array
	 * @return array The converted array
	 */
	
	function _convertIOArray($array)
	{
		$ret=array();
		foreach ($array as $k=>$v)
		{
			if (! strpos($v,":"))
			{
				$v='tns:' . $v;
			}
			$ret[$k]=$v;
		}
		return $ret;					
	}	
}
?>