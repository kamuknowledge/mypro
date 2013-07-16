<?php 
class Usermanagement_Bootstrap extends Zend_Application_Module_Bootstrap {

    protected function _initAutoload()
    {
      $autoloader = new Zend_Application_Module_Autoloader(array(
           'namespace' => 'Usermanagement_',
           'basePath' => dirname(__FILE__)
      ));
      return $autoloader;
     }
} ?>