<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	ProfileController.php 
* Module	:	Default Module
* Owner		:	RAM's 
* Purpose	:	This class is used for common Profile operations for all user types
* Date		:	08/05/2012


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

class ProfileController extends Zend_Controller_Action { 
	public $session;	// used for managing session with NAMESPACE portal
	public $error;		// used for managing session with NAMESPACE portalerror
	private $profile;		// used for creating an instance of model, Access is with in the class	
	private $profiledb;

	/**
     * Purpose: Initiates sessions with Namespace 'portal' and 'portalerror' 
     *
     * Access is public
     *
     * @param	
     * 
     * @return  
     */
	
	public function init() { 
		$this->profile = new Default_Model_Profile();
		$this->profiledb = new Default_Model_Profiledb();
		
		/* Check Login */
		if(!$this->profile->check_login()){ $this->_redirect('/');exit;}		
		
        $this->_helper->layout->setLayout('default/profile_layout');
		$this->view->headLink()->setStylesheet($this->view->baseUrl('public/default/css/jquery.Jcrop.css'));
		$this->view->headLink()->appendStylesheet($this->view->baseUrl('public/default/css/dev_profile.css'));
		//$this->view->headScript()->appendFile('http://malsup.github.com/jquery.form.js','text/javascript');
		$this->view->headScript()->appendFile($this->view->baseUrl('public/default').'/js/jquery.Jcrop.js','text/javascript');
		$this->view->headScript()->appendFile($this->view->baseUrl('public/default').'/js/jquery.form.js','text/javascript');
		//$this->setLayoutAction('store/layout');		
	}
	
    
	/**
     * Purpose: Index action
     *
     * Access is public
     *
     * @param	
     * 
     * @return  
     */
	
	public function indexAction() {
		try{			
			//echo "store/index/index";
			//exit; 
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	

	
	
	
	/**
     * Purpose: Index action
     *
     * Access is public
     *
     * @param	
     * 
     * @return  
     */
	
	public function viewAction() {
		try{
			//$this->_helper->layout->disableLayout();
			$UserDetails["profile"] = $this->profiledb->getProfileDetails();
			$UserDetails["address"] = $this->profiledb->getUserAddress();
			$UserDetails["education"] = $this->profiledb->getUserEducation();
			$UserDetails["skills"] = $this->profiledb->getUserSkills();
			$UserDetails["experiance"] = $this->profiledb->getUserExperiance();
			$UserDetails["timezones"] = $this->profiledb->getTimezones();
			//print_r($UserDetails);
			$this->view->UserDetails = $UserDetails;
			//echo "store/index/index";
			//exit; 
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	
	
	
	
	
		/**
     * Purpose: Index action
     *
     * Access is public
     *
     * @param	
     * 
     * @return  
     */
	
	public function editAction() {
		try{			
			//echo "store/index/index";
			//exit; exit;
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function editaboutusAction() {
		try{
			$params = $this->_getAllParams();	
			$this->_helper->layout->setLayout('default/empty_layout');
			$request = $this->getRequest();
			$Request_Values = $request->getPost();			
			if ($request->isPost()) {
				if(!$this->profile->edit_about_us($params)) {
					// return 1;
					// redirect to current url
					
				} else {
					echo 1;
					exit;
				}
			}else{			
				//$this->view->countrieslist = $this->merchantdb->getCountriesList();
				//return 0;
			}
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function addeditexperianceAction() {
		try{
			$params = $this->_getAllParams();
			$this->_helper->layout->setLayout('default/empty_layout');
			$this->view->IndustryList = $this->profiledb->getIndustry();
			$this->view->CountryList = $this->profiledb->getCountry();
			if(isset($params["input_type"]) && $params["input_type"]) {
				// Display form with error message
				$this->view->UserDetails = array(array("input_type"=>"error", "experience_id" => $params["experiance_idd"]));
			} else {
				// Normal form display functionality for the first time
				$request = $this->getRequest();
				$Request_Values = $request->getPost();
				if ($request->isPost()) {
					if(isset($Request_Values["type"]) && $Request_Values["type"] == "edit") {
						$id = $Request_Values["id"];
						if($id)
						{
							if($id != "new") {
								$this->view->UserDetails = $this->profiledb->getUserExperiance($id);
								$country = isset($this->view->UserDetails[0]["country_id"]) ? $this->view->UserDetails[0]["country_id"] : "";
								$this->view->StateList = $this->profiledb->getState($country);
							} else {
								$this->view->UserDetails = array(array("experience_id"=>"new", "company_name"=>"", "job_location"=>"", "country_id"=>"", "job_title"=>"", "industry_id"=>"", "state_id"=>"", "from_month"=>"", "from_year"=>"", "to_month"=>"", "to_year"=>"", "present_working"=>"", "company_description"=>"", ""=>"", ""=>""));
							}
						} else {
							echo 1;
							exit;
						}
					}
				}else{			
					//$this->view->countrieslist = $this->merchantdb->getCountriesList();
					//return 0;
				}
			}
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function vieweditexperianceAction() {
		try{
			$params = $this->_getAllParams();
			$request = $this->getRequest();
			$Request_Values = $request->getPost();
			if ($request->isPost()) {
				$this->_helper->layout->setLayout('default/empty_layout');
				if(isset($Request_Values["type"]) && $Request_Values["type"] == "edit") {
					$experiance_id = $Request_Values["experiance_id"];
					$edit_exp_id = $Request_Values["exp_id"];
					if($experiance_id)
					{
						$results = $this->profiledb->getUserExperiance($experiance_id);
						$this->view->UserDetails = $results[0];
					} else if($edit_exp_id) {
						if($this->profile->add_edit_experiance($params)) {
							/*$results = $this->profiledb->getUserExperiance($edit_exp_id);
							$this->view->UserDetails = $results[0];*/
							echo 1;exit;
						} else {
							// Load form with error messages
							$this->view->UserDetails = array("error"=>1, "exp_id"=>$edit_exp_id);
						}
					}
				} else if(isset($Request_Values["type"]) && $Request_Values["type"] == "new") {
					$exp_id = $this->profile->add_edit_experiance($params);
					if($exp_id) {
						/*$results = $this->profiledb->getUserExperiance($exp_id);
						$results[0]["dis_type"] = "new";
						$this->view->UserDetails = $results[0];*/
						echo 1;exit;
					} else {
						// Load form with error messages
						$this->view->UserDetails = array("error"=>1, "exp_id"=>"new");
					}
				}
			}else{
				echo 0;exit;
			}
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function getstatesAction() {
		$request = $this->getRequest();
		$Request_Values = $request->getPost();
		$country_arr = "";
		if ($request->isPost() && isset($Request_Values["country"])) {
			$country_db_arr = $this->profiledb->getState($Request_Values["country"]);
			foreach($country_db_arr as $value) {
				$country_arr .= "<option value='".$value["state_id"]."'>".$value["name"]."</option>";
			}
		}
		echo $country_arr;
		exit;
	}
	
	public function addediteducationAction() {
		try{
			$params = $this->_getAllParams();
			$this->_helper->layout->setLayout('default/empty_layout');
			//$this->view->IndustryList = $this->profiledb->getIndustry();
			//$this->view->CountryList = $this->profiledb->getCountry();
			if(isset($params["input_type"]) && $params["input_type"]) {
				// Display form with error message
				$this->view->UserDetails = array(array("input_type"=>"error", "education_id" => $params["education_idd"]));
			} else {
				// Normal form display functionality for the first time
				$request = $this->getRequest();
				$Request_Values = $request->getPost();
				if ($request->isPost()) {
					if(isset($Request_Values["type"]) && $Request_Values["type"] == "edit") {
						$id = $Request_Values["id"];
						if($id)
						{
							if($id != "new") {
								$this->view->UserDetails = $this->profiledb->getUserEducation($id);
								//$country = isset($this->view->UserDetails[0]["country_id"]) ? $this->view->UserDetails[0]["country_id"] : "";
								//$this->view->StateList = $this->profiledb->getState($country);
							} else {
								$this->view->UserDetails = array(array("education_id"=>"new", "company_name"=>"", "job_location"=>"", "country_id"=>"", "job_title"=>"", "industry_id"=>"", "state_id"=>"", "from_month"=>"", "from_year"=>"", "to_month"=>"", "to_year"=>"", "present_working"=>"", "company_description"=>"", ""=>"", ""=>""));
							}
							// return 1;
							// redirect to current url
							
						} else {
							echo 1;
							exit;
						}
					}
				}else{			
					//$this->view->countrieslist = $this->merchantdb->getCountriesList();
					//return 0;
				}
			}
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function viewediteducationAction() {
		try{
			$params = $this->_getAllParams();
			$request = $this->getRequest();
			$Request_Values = $request->getPost();
			if ($request->isPost()) {
				$this->_helper->layout->setLayout('default/empty_layout');
				if(isset($Request_Values["type"]) && $Request_Values["type"] == "edit") {
					$education_id = $Request_Values["education_id"];
					$edit_edu_id = $Request_Values["edu_id"];
					if($education_id)
					{
						$results = $this->profiledb->getUserEducation($education_id);
						$this->view->UserDetails = $results[0];
					} else if($edit_edu_id) {
						if($this->profile->add_edit_education($params)) {
							// $results = $this->profiledb->getUserEducation($edit_edu_id);
							// $this->view->UserDetails = $results[0];
							echo 1; exit;
						} else {
							// Load form with error messages
							$this->view->UserDetails = array("error"=>1, "edu_id"=>$edit_edu_id);
						}
					}
				} else if(isset($Request_Values["type"]) && $Request_Values["type"] == "new") {
					$edu_id = $this->profile->add_edit_education($params);
					if($edu_id) {
						// $results = $this->profiledb->getUserEducation($edu_id);
						// $results[0]["dis_type"] = "new";
						// $this->view->UserDetails = $results[0];
						echo 1; exit;
					} else {
						// Load form with error messages
						$this->view->UserDetails = array("error"=>1, "edu_id"=>"new");
					}
				}
			}else{
				echo 0;exit;
			}
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function addeditpersonalAction() {
		try{
			$params = $this->_getAllParams();	
			$this->_helper->layout->setLayout('default/empty_layout');
			$request = $this->getRequest();
			$Request_Values = $request->getPost();			
			if ($request->isPost()) {
				if(isset($Request_Values["type"])) {
					if($Request_Values["type"] == "editform") {
						$this->view->UserDetails = $this->profiledb->getProfileInfo();
						$this->view->UserDetails[] = array("display"=>"edit");
					} else if($Request_Values["type"] == "edit"){
						if(!$this->profile->edit_profile_info($params))
							$this->view->UserDetails[] = array("display"=>"edit");
						$this->view->UserDetails = $this->profiledb->getProfileInfo();
					} else {
						$this->view->UserDetails = $this->profiledb->getProfileInfo();
					}
				}
			}else{
				//$this->view->countrieslist = $this->merchantdb->getCountriesList();
				//return 0;
			}
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function addeditaddressAction() {
		try{
			$params = $this->_getAllParams();	
			$this->_helper->layout->setLayout('default/empty_layout');
			$request = $this->getRequest();
			$Request_Values = $request->getPost();
			$this->view->CountryList = $this->profiledb->getCountry();
			if ($request->isPost()) {
				if(isset($Request_Values["type"])) {
					if($Request_Values["type"] == "editform") {
						$this->view->UserDetails = $this->profiledb->getUserAddress();
						foreach($this->view->UserDetails as $value) {
							$country = isset($value["country_id"]) ? $value["country_id"] : "";
							if($value["address_type"] == "Home") {
								$this->view->HomeStateList = $this->profiledb->getState($country);
							} else if($value["address_type"] == "Office") {
								$office_country = isset($value["country_id"]) ? $value["country_id"] : "";
								$this->view->OfficeStateList = $this->profiledb->getState($country);
							}
						}
						$this->view->UserDetails[2] = array("display"=>"edit");
					} else if($Request_Values["type"] == "edit"){
						$error = 0;
						if(!$this->profile->edit_address_info($params))
							$error = 1;
						$this->view->UserDetails = $this->profiledb->getUserAddress();
						if($error)
							$this->view->UserDetails[2] = array("display"=>"edit");
						foreach($this->view->UserDetails as $value) {
							$country = isset($value["country_id"]) ? $value["country_id"] : "";
							if($value["address_type"] == "Home") {
								$this->view->HomeStateList = $this->profiledb->getState($country);
							} else if($value["address_type"] == "Office") {
								$office_country = isset($value["country_id"]) ? $value["country_id"] : "";
								$this->view->OfficeStateList = $this->profiledb->getState($country);
							}
						}
					} else {
						$this->view->UserDetails = $this->profiledb->getUserAddress();
						foreach($this->view->UserDetails as $value) {
							$country = isset($value["country_id"]) ? $value["country_id"] : "";
							if($value["address_type"] == "Home") {
								$this->view->HomeStateList = $this->profiledb->getState($country);
							} else if($value["address_type"] == "Office") {
								$office_country = isset($value["country_id"]) ? $value["country_id"] : "";
								$this->view->OfficeStateList = $this->profiledb->getState($country);
							}
						}
					}
				}
			}else{
				//$this->view->countrieslist = $this->merchantdb->getCountriesList();
				//return 0;
			}
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function editmembertitleAction() {
		try{
			$params = $this->_getAllParams();	
			$this->_helper->layout->setLayout('default/empty_layout');
			$request = $this->getRequest();
			$Request_Values = $request->getPost();			
			if ($request->isPost()) {
				if(!$this->profile->edit_member_title($params)) {
					$this->view->UserDetails = $this->profiledb->getTimezones();
				} else {
					echo 1;
					exit;
				}
			}else{			
				//$this->view->countrieslist = $this->merchantdb->getCountriesList();
				//return 0;
			}
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function deleteimageAction() {
		try{
			if ($this->getRequest()->isXmlHttpRequest()) {
				$this->profiledb->update_member_profile();
			}
			echo 1;exit;
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function uploadAction() {
		try{		
			$params = $this->_getAllParams();	
			$this->_helper->layout->setLayout('default/empty_layout');
			// Define a destination
			$targetFolder = $this->view->baseUrl().'/public/uploads'; // Relative to the root
			$request = $this->getRequest();
			$Request_Values = $request->getPost();
			if ($request->isPost()) {
				if(isset($Request_Values["input_type"])) {
					$targ_w = $targ_h = 200;
					$jpeg_quality = 90;
					$src = $_SERVER['DOCUMENT_ROOT'].$Request_Values["file_src"];
					if(file_exists( $src )){
						$src_dst = str_replace("/original/", "/crop_200_200/", $src);
						$img_r = imagecreatefromjpeg($src);
						$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
						imagecopyresampled($dst_r,$img_r,0,0,$Request_Values['x'],$Request_Values['y'],
						$targ_w,$targ_h,$Request_Values['w'],$Request_Values['h']);
						if(imagejpeg($dst_r,$src_dst,$jpeg_quality) && $Request_Values["file_name"]) {
							$this->profiledb->update_member_profile($Request_Values["file_name"]);
						}
					}
					exit;
				} else {
					if (!empty($_FILES)) {
						// Validate the file type
						$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
						$fileParts = pathinfo($_FILES['avatar']['name']);
						if (in_array($fileParts['extension'],$fileTypes)) {
							$image = new SimpleImage();
							$file_name = "photo_".md5(date("Y-m-d H:i:s")).'.jpg';
							$size = getImageSize($_FILES['avatar']['tmp_name']);
							$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
							$targetPath = rtrim($targetPath,'/') . '/user_images/original/'.$file_name;
							$max_width = 500;
							$max_height = 500;
							$width = $size[0];
							$height = $size[1];
							if ($size[0] > $max_width) {
								$image->load($_FILES['avatar']['tmp_name']);
								$image->resizeToWidth($max_width);
								$image->save($targetPath);
								$width = $max_width;
							} else {
								move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath);
							}
							$size = getImageSize($targetPath);
							if ($size[1] > $max_height) {
								$image->load($targetPath);
								$image->resizeToHeight($max_height);
								$image->save($targetPath);
								$height = $max_height;
							}
							$this->view->UserDetails = array("file_name"=>$file_name,"width"=>$image->getWidth(),"height"=>$image->getHeight());
						} else {
							echo '0';
							exit;
						}
					}
				}
			}
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	public function reloadexperianceviewAction() {
		try {
			$this->_helper->layout->setLayout('default/empty_layout');
			$UserDetails["experiance"] = $this->profiledb->getUserExperiance();
			$this->view->UserDetails = $UserDetails;
		} catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		} 
	}
	public function deleteexperianceAction() {
		try{
			if ($this->getRequest()->isXmlHttpRequest()) {
				$request = $this->getRequest();
				$Request_Values = $request->getPost();
				if ($request->isPost()) {
					$this->profiledb->delete_user_experiance($Request_Values["id"]);
					echo 1;
				}
			}
			exit;
		} catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		} 
	}
	public function deleteeducationAction() {
		try{
			if ($this->getRequest()->isXmlHttpRequest()) {
				$request = $this->getRequest();
				$Request_Values = $request->getPost();
				if ($request->isPost()) {
					$this->profiledb->delete_user_education($Request_Values["id"]);
					echo 1;
				}
			}
			exit;
		} catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		} 
	}
	public function reloadeducationviewAction() {
		try {
			$this->_helper->layout->setLayout('default/empty_layout');
			$UserDetails["education"] = $this->profiledb->getUserEducation();
			$this->view->UserDetails = $UserDetails;
		} catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		} 
	}
	public function addeditskillsetAction() {
		try {
			if ($this->getRequest()->isXmlHttpRequest()) {
				$this->_helper->layout->setLayout('default/empty_layout');
				$request = $this->getRequest();
				$Request_Values = $request->getPost();
				if ($request->isPost()) {
					$type = $Request_Values["type"];
					$id = $Request_Values["id"];
					$var = 0;
					if($type == "new") {
						$var = $this->profiledb->createSkillSet($Request_Values);
					} else if($type == "edit") {
						$var = $this->profiledb->editSkillSet($id, $Request_Values);
					} else if($type == "delete") {
						$var = $this->profiledb->deleteSkillSet($id, $Request_Values);
					}
					if($var) echo $var; else echo 0;
				}
			}
			exit;
		} catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		} 
	}
}

class SimpleImage {

   var $image;
   var $image_type;

   function load($filename) {

      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {

         $this->image = imagecreatefromjpeg($filename);
      } elseif( $this->image_type == IMAGETYPE_GIF ) {

         $this->image = imagecreatefromgif($filename);
      } elseif( $this->image_type == IMAGETYPE_PNG ) {

         $this->image = imagecreatefrompng($filename);
      }
   }
   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {

      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,$filename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {

         imagegif($this->image,$filename);
      } elseif( $image_type == IMAGETYPE_PNG ) {

         imagepng($this->image,$filename);
      }
      if( $permissions != null) {

         chmod($filename,$permissions);
      }
   }
   function output($image_type=IMAGETYPE_JPEG) {

      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {

         imagegif($this->image);
      } elseif( $image_type == IMAGETYPE_PNG ) {

         imagepng($this->image);
      }
   }
   function getWidth() {

      return imagesx($this->image);
   }
   function getHeight() {

      return imagesy($this->image);
   }
   function resizeToHeight($height) {

      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }

   function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }

   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100;
      $this->resize($width,$height);
   }

   function resize($width,$height) {
      $new_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;
   }      

}
?>