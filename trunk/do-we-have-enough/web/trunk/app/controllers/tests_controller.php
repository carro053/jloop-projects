<?php
class TestsController extends AppController
{
    var $name       = 'Tests';    // required for PHP4 installs
    var $uses       = null;  // no table
    var $components = array('soap');

    // simpletest no proxy

    function testsoap()
    {        
    	ini_set('display_errors', 1); 
		ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache 


		$client = new SoapClient(null,	//WSDL mode is not possible because production servers needs authentication to get wsdl.
											array(
										   'location'=>"https://auth.ws.realogyfg.com/AuthenticationService/AuthenticationMgmt.svc",
										   'uri'=>"http://rfg.realogy.com/Btt/AuthenticationManagement/Services/2009/05",
										   'login'=> "WSSIR800041",
                                           'password'=> "9PByn4Je"));
  
		$client->__setCookie("ObSSOCookie", "loggedoutcontinue");		//mandatory cookies which shall be sent with credentials to authenticate.
		$client->__setCookie("OBBasicAuth", "fromDialog");	
		$retval = $client->__soapCall("Authenticate", array(),
		array('soapaction'=>"http://rfg.realogy.com/Btt/AuthenticationManagement/Services/2009/05/AuthenticationManagementServiceContract/Authenticate"));
	
		print ($retval->Token);			//Token is a cookie which shall be sent with requests to other services (cookie with authention token).
		exit();
    }

    // simpletest with proxy

    function simpleproxy()
    {          
        $url    =   Configure::read('SoapServer');
        $func   =   'simpletest';
         
        $snd  = 15;
        $data = $this->soap->client($url, $func, 15, true);
        $this->_renderResult($data);   
    }

    // simpletest Failure with proxy

    function simpleproxyfail()
    {          
        $url    =   Configure::read('SoapServer');
        $func   =   'simpletest';
         
        $snd  = array('appid' => 15);
        // this will failt, because the simple text ecpect just a simple xsd::integer, the proxy will
        // send an array instead of a single value, this would work if the simpletest was defined with a structure
        
        $data = $this->soap->client($url, $func, $snd, true);
        $this->_renderResult($data);   
    }
    
    // send an array of interger to make the sum
    
    function sumtest()
    {
        $url    =   Configure::read('SoapServer');
        $func   =   'sumtest';
         
        $snd  = array('int1' => 15, 'int2' => 10);
        $data = $this->soap->client($url, $func, $snd, false);
        $this->_renderResult($data);   
        $this->set('data',$data);
    }

    // send an array of interger to make the sum, server expects 2 values => error
    
    function sumtesterror()
    {
        $url    =   Configure::read('SoapServer');
        $func   =   'sumtest';
         
        $snd  = array('int1' => 15);
        $data = $this->soap->client($url, $func, $snd, false);
        $this->_renderResult($data);   
        $this->set('data',$data);
    }
    
    // sumall no proxy, sum all given values
    function sumall()
    {
        $url    =   Configure::read('SoapServer');
        $func   =   'sumall';
         
        $snd  = array(15, 50, 10);
        $data = $this->soap->client($url, $func, $snd, false);
        $this->_renderResult($data);   
        $this->set('data',$data);
    }    

    //  double all values sent to server
    function doubleall()
    {
        $url    =   Configure::read('SoapServer');
        $func   =   'doubleall';
         
        $snd  = array(15, 50, 10);
        $data = $this->soap->client($url, $func, $snd, false);
        $this->_renderResult($data);   
        $this->set('data',$data);
    }    
    // send  a structure with proxy 
    
    function sumteststruct()
    {
        $url    =   Configure::read('SoapServer');
        $func   =   'sumteststruct';
         
        $snd  = array('int1' => 15, 'int2' => 10);
        $data = $this->soap->client($url, $func, $snd, true);
        $this->_renderResult($data);   
        $this->set('data',$data);
    }
    // send a structure + a single value
    
    function sumteststructsingle()
    {
        $url    =   Configure::read('SoapServer');
        $func   =   'sumteststructsingle';
         
        $snd  = array(array('int1' => 15, 'int2' => 10), 10);
        $data = $this->soap->client($url, $func, $snd, false);
        $this->_renderResult($data);   
        $this->set('data',$data);
    }

    // send a structure + values
    
    function sumtestlevelstruct()
    {
        $url    =   Configure::read('SoapServer');
        $func   =   'sumtestlevelstruct';
         
        $snd  = array(array('int1' => 15, 'int2' => 10), 10, 30, 40);
        $data = $this->soap->client($url, $func, $snd, false);
        $this->_renderResult($data);   
        $this->set('data',$data);
    }

    // get a Person record    

    function getpersonbyid($method)
    {
        $url    =   Configure::read('SoapServer');
        $func   =   $method;
                
        $snd  = array('pid' => 1);
        $data = $this->soap->client($url, $func, $snd, false);
        $this->_renderResult($data);   
        $this->set('data',$data);
    }
    
    // get all person test
    
    function getallperson($method)
    {
        $url    =   Configure::read('SoapServer');
        $func   =   $method;
                
        $snd  = array(1);
        $data = $this->soap->client($url, $func, $snd, false);
        $this->_renderResult($data);   
        $this->set('data',$data);
    }
    
    // render test results
    
    function _renderResult($data)
    {
        if($data == null)
        {
            $this->set('error',     true);
            $this->set('dbgStr',    $this->soap->dbgstr);
            $this->set('dbgRequest',    $this->soap->rawRequest);
            $this->set('dbgResponse',   $this->soap->rawResponse);
            $this->set('error',         $this->soap->error);
            $this->render('soaperror');
            return;
        }      
        $this->set('data',$data);
        $this->render('testsoap');
    }
}
?>