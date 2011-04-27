<?php
/**
 *	  Unit Test for the I class.
 *
 *	@author Stephen Simpson <me@simpo.org>
 *	@version 0.1.1
 *	@license http://www.gnu.org/licenses/lgpl.html
 *	@package UnitTests.Impact
 *	@extends PHPUnit_Framework_TestCase
 */
class Test_Templater extends PHPUnit_Framework_TestCase {
    private $templater = null;
    
    protected function setUp() {
        if (!defined('DS')) {
            define('DS',DIRECTORY_SEPARATOR);
        }
        if (!defined('MODELS_DIRECTORY')) {
            define('MODELS_DIRECTORY','models');
        }
        if (!defined('ROOT_BACK')) {
            define('ROOT_BACK',__DIR__.DS.'..'.DS.'..'.DS.'..'.DS);
        }
        spl_autoload_register('self::__autoload');
        
        $this->templater = new Templater;
    }
    
    private function __autoload($className) {
        $classFileName = str_replace('_',DIRECTORY_SEPARATOR,$className).'.php';
        require_once ROOT_BACK.MODELS_DIRECTORY.DIRECTORY_SEPARATOR.$classFileName;
    }
    
    protected static function get_method($name) {
        $class = new ReflectionClass('Templater');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
    
    public function test_parse() {
        // STUB
    }
    
    public function test_get_xml() {
        $method = self::get_method('_get_xml');
        
        // STUB
    }
    
    public function test_loop() {
        $method = self::get_method('_loop');
        
        // STUB
    }
    
    public function test_block() {
        $method = self::get_method('_block');
        
        // STUB
    }
    
    public function test_template() {
        $method = self::get_method('_template');
        
        // STUB
    }
    
    public function test_data() {
        $method = self::get_method('_data');
        
        // STUB
    }
    
    public function test_include() {
        $method = self::get_method('_include');
        
        // STUB
    }
    
    public function test_feature() {
        $method = self::get_method('_feature');
        
        // STUB
    }
    
    public function test_feature_loader() {
        $method = self::get_method('_feature_loader');
        
        // STUB
    }
    
    public function test_plugin() {
        $method = self::get_method('_plugin');
        
        // STUB
    }
    
    public function test_notblank() {
        $method = self::get_method('_notblank');
        
        // STUB
    }
    
    public function test_acl() {
        $method = self::get_method('_acl');
        
        // STUB
    }
    
    public function test_ical() {
        $method = self::get_method('_ical');
        
        // STUB
    }
    
    public function test_variable() {
        $method = self::get_method('_variable');
        
        // STUB
    }
    
    public function test_test_formatter() {
        $method = self::get_method('_test_formatter');
        
        // STUB
    }
    
    public function test_get_attributes() {
        $method = self::get_method('_get_attributes');
        
        // STUB
    }
    
    public function test_date_reformat() {
        $method = self::get_method('_date_reformat');
        
        // STUB
    }
    
    public function test_contains() {
        $method = self::get_method('_contains');
        
        // STUB
    }
    
}