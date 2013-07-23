<?php 
class Wall_Bootstrap extends Zend_Application_Module_Bootstrap {

    protected function _initAutoload()
    {
      $autoloader = new Zend_Application_Module_Autoloader(array(
           'namespace' => 'Wall_',
           'basePath' => dirname(__FILE__)
      ));
      return $autoloader;
     }
} ?>