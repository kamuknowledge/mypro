<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	Bootstrap.php 
* Owner		:	RAM's 
* Purpose	:	This class is used for initializing modules, plugins and configuaration settings
* Date		:	08/05/2012


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/


require('functions.php');
 ini_set("display_errors",true);
 error_reporting(E_ERROR | E_WARNING | E_PARSE);
 //require_once __DIR__ . '/../Loader/StandardAutoloader.php';
 
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{	
	/**
	* Set the default application timezone
	*
	* @return void
	*/
    protected function _initTimezone()
    {
        date_default_timezone_set('Europe/Berlin');
    }
	
	
	/*
	 * Initializing Resources
	 */	
	public function _initResource() {		
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        Zend_Registry::set('config', $config);
		//self::getDbpassword();
    }
    
    
    protected function _initPlaceholders()
    {        
		$view = new Zend_View();
		$view->headTitle('Getlinc ')->setSeparator(' - ');	       
    }
    
    
	/*
	 * Initializing session timeout
	 */
	public function _initSessionCheck() {           
		Zend_Session::setOptions(array('name'=>"MyPortal"));
		$config = Zend_Registry::get('config');
		$sessionTimeOut = $config->user->timeout->session * 60;
		$SessionHandler=new Zend_Session_SaveHandler_DbFunctions($sessionTimeOut);
	}
     
	
	
  
    
    
	/*
	 * Initializing plugins requiredsezsz
	 */   
    public function _initLoadFiles() {
        # All plugins added        
        $plugins = glob(__DIR__ . "/plugins/*.php");
        foreach ($plugins as $plugin) {
            require_once $plugin;
        }
    }
    
	
	
	/*
     * Initializing Router
     */
	public function _initRoute(){
		$frontController  = Zend_Controller_Front::getInstance();
		
		$route = new Zend_Controller_Router_Route('getlinc/aboutus/',array('controller' => 'cms','module' => 'default' ,'action' => 'index','id' =>1));
		$frontController->getRouter()->addRoute('aboutus',$route);
		
		$route = new Zend_Controller_Router_Route('getlinc/contactus/',array('controller' => 'cms','module' => 'default' ,'action' => 'index','id' =>2));
		$frontController->getRouter()->addRoute('contactus',$route);
		
		$route = new Zend_Controller_Router_Route('getlinc/terms-and-conditions/',array('controller' => 'cms','module' => 'default' ,'action' => 'index','id' =>3));
		$frontController->getRouter()->addRoute('terms-and-conditions',$route);
		
		$route = new Zend_Controller_Router_Route('getlinc/privacy-policy/',array('controller' => 'cms','module' => 'default' ,'action' => 'index','id' =>4));
		$frontController->getRouter()->addRoute('privacy-policy',$route);
		
		$route = new Zend_Controller_Router_Route('getlinc/cancellations-and-returns/',array('controller' => 'cms','module' => 'default' ,'action' => 'index','id' =>5));
		$frontController->getRouter()->addRoute('cancellations-and-returns',$route);
		
		$route = new Zend_Controller_Router_Route('getlinc/shipping/',array('controller' => 'cms','module' => 'default' ,'action' => 'index','id' =>6));
		$frontController->getRouter()->addRoute('shipping',$route);
		
		$route = new Zend_Controller_Router_Route('getlinc/payments/',array('controller' => 'cms','module' => 'default' ,'action' => 'index','id' =>7));
		$frontController->getRouter()->addRoute('payments',$route);
		
		$route = new Zend_Controller_Router_Route('getlinc/ordering-and-tracking/',array('controller' => 'cms','module' => 'default' ,'action' => 'index','id' =>8));
		$frontController->getRouter()->addRoute('ordering-and-tracking',$route);
		
		$route = new Zend_Controller_Router_Route('profile/:id/:username/',array('controller' => 'cms','module' => 'default' ,'action' => 'index'),array('id' => '\d+', 'username' => '\d+'));
		$frontController->getRouter()->addRoute('ordering-and-tracking',$route);
			
    }

    
    
      
    
    /*
     * Initializing language file i.e, Constants
     */
    public function _initFuncName() {        
    	$langs = glob(__DIR__ . "/lang/*.php");
     	foreach ($langs as $lang) {
            require_once $lang;
        }
    }
    
    
    
    /*
     * Initializing modules
     */   
	public function _initModuleLayout() {   
           
        $front = Zend_Controller_Front::getInstance();
        
        //Fetching modules
        $modules = Application_Model_DataBaseOperations::getModules();
        
        $controllerDirectory = array();
        //$dir = __DIR__;
		$dir = APPLICATION_PATH;
        //echo $dir;exit;
        foreach($modules as $record) {
        	$controllerDirectory[$record['modulename']] =  $dir ."\modules\/".$record['modulename']."\controllers";                
        }
        
        //print_r($controllerDirectory);exit;
        $front->setControllerDirectory($controllerDirectory);       
        
        $router = $front->getRouter();
        $request = $front->getRequest();
        //$front->registerPlugin(new Temp_Plugin_ModuleLayout()); 
		        
		$MySession = new Zend_Session_Namespace('MyPortal');
		if(isset($MySession->loggedIn) && $MySession->loggedIn) {
			//Initializing ACL Helper			
			/*$helper= new My_Controller_Helper_Acl();
			$helper->setRoles(); 
			$helper->setResources();
			$helper->setPrivilages();
			$helper->setAcl();
			$front->registerPlugin(new My_Controller_Plugin_Acl());*/
		}
        
	}


	/*
	 * Initializing plugins requiredsezsz
	 */   
    public function _initDb() {
        $config = Zend_Registry::get('config');
		$params = array(
		'host'     => $config->db->params->host,
		'username' => $config->db->params->username,
		'password' => $config->db->params->password,
		'dbname'   => $config->db->params->dbname,
		'profiler' => TRUE  // turn on profiler
		// set to false to disable (disabled by default)
		);
		$db = Zend_Db::factory('PDO_MYSQL', $params);		
		$db->getConnection();		
		if (!Zend_Registry::isRegistered('db')) {
			Zend_Registry::set('db', $db);    
		}		
		//print_r(Zend_Registry::get('db'));
    }
	


	
	
}

