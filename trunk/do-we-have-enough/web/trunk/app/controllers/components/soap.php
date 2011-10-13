<?php
/*
 * SOAP component 
 *
 * @author      RosSoft
 * @version     0.1
 * @license		MIT
 *
 */

class SoapComponent extends Object
{
	var $controller  = null;
    var $error       = null;
    var $dbgstr      = null;
    var $rawRequest  = null;
    var $rawResponse = null;  
    var $debugLevel  = 9;
    var $decodeUTF8  = false;
    
	function startup(&$controller)
	{
		$this->controller=& $controller;									
	}
	
	function client($url, $func, $param=array(''), $useProxy = false, $timeout = 30)
	{		
		App::import('Vendor', 'nusoap');

		//you have to rename all the instances of soapclient to soap_client in the file nusoap.php (PHP5 compat)		
        if($useProxy)
            $url .= '/wsdl';
            
		$client = new soap_client($url, $useProxy, false, false, false, false, 0, $timeout);
        $client->setGlobalDebugLevel($this->debugLevel);
        $client->decodeUTF8($this->decodeUTF8);
        
        // we use the WSDL and make a proxy
        if($useProxy)
        {
            $proxy = $client->getProxy();
            $proxy->decodeUTF8($this->decodeUTF8);

            $response = $proxy->{$func}($param);

            if($proxy->fault || $client->fault)
                $this->error = $response;
            else
                $this->error = $proxy->getError();
        
            if($this->debugLevel > 0)
                $this->dbgstr = $proxy->debug_str;
          
            if($this->debugLevel > 8)
                $this->rawRequest = $proxy->request;

            if($this->debugLevel > 8)
                $this->rawResponse = $proxy->response;
        }
        else
        {    
            $response = $client->call($func, $param, "");

            if($client->fault)
                $this->error = $response;
            else
                $this->error = $client->getError();
                  
            if($this->debugLevel > 0)
                $this->dbgstr = $client->debug_str;
          
            if($this->debugLevel > 8)
                $this->rawRequest = $client->request;

            if($this->debugLevel > 8)
                $this->rawResponse = $client->response;
        }
        
        if($this->error)
           return null;
        
		return $response;
	}   
}
?>