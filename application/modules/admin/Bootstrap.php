<?php 
class Admin_Bootstrap extends Zend_Application_Module_Bootstrap {

    protected function _initAutoload()
    {
      $autoloader = new Zend_Application_Module_Autoloader(array(
           'namespace' => 'Admin_',
           'basePath' => dirname(__FILE__)
      ));
      return $autoloader;
     }
} ?>