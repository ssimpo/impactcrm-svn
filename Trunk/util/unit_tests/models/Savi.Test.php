<?php
/**
 *	Unit Test for the SAVI class.
 *
 *	@author Stephen Simpson <me@simpo.org>
 *	@version 0.1.1
 *	@license http://www.gnu.org/licenses/lgpl.html
 *	@package UnitTests.Impact
 *	@extends PHPUnit_Framework_TestCase
 */
class Test_Savi extends PHPUnit_Framework_TestCase {
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
        
        $this->parser = new Savi;
    }
    
    private function __autoload($className) {
        $classFileName = str_replace('_',DIRECTORY_SEPARATOR,$className).'.php';
        require_once ROOT_BACK.MODELS_DIRECTORY.DIRECTORY_SEPARATOR.$classFileName;
    }
    
    protected static function get_method($name) {
        $class = new ReflectionClass('Savi');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
    
    public function test_ical_parse() {
        // STUB
    }
    
    public function test_ical_set_element_handler() {
        // STUB
    }
    
    public function test_ical_set_character_data_handler() {
        // STUB
    }
    
    public function test_utf8_decode() {
        // STUB
    }
    
    public function test_utf8_encode() {
        // STUB
    }
    
    public function test_ical_get_error_code() {
        // STUB
    }
    
    public function test_ical_get_error_string() {
        // STUB
    }
    
    public function test_get_current_line_number() {
        // STUB
    }
    
    public function test_get_current_byte_index() {
        // STUB
    }
    
    public function test_get_current_column_number() {
        // STUB
    } 
}