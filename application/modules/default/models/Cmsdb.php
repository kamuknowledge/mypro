<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	Cmsdb.php 
* Module	:	CMS Management Module
* Owner		:	RAM's 
* Purpose	:	This class is used for CMS management related database operations
* Date		:	08/05/2012


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

class Default_Model_Cmsdb extends Application_Model_DataBaseOperations {
	
	public $session;
	private $error;
	public $viewobj;
	
	
	/**
     * Purpose: Constructor sets sessions for MyClientPortal and MyClientPortalerror
     * Access is limited to class and extended classes
     *
     * @param   
     * @return  
     */
	public function __construct(){
		$this->session = new Zend_Session_Namespace('MyClientPortal');
		$this->error = new Zend_Session_Namespace('MyClientPortalerror');
	}
	
	

	
	/**
     * Purpose: Fetching list
     *
     * Access is limited to class and extended classes
     *
     * @param   int		$id, Id
     * @param	int		$istart Start value
     * @param	int		$ilimit Limit value for fetching result set
     * @param	varchar	$cond Search condition
     * @return  object	Returns status message.	
     */
	
	public function getChatList(){
		try {	
			//parent::SetDatabaseConnection();
			//$query = "SELECT category_id, parent_category_id, category_name FROM store_categories where statusid=1";
			//exit;			
			//return Application_Model_Db::getResult($query);
			
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	
}
?>