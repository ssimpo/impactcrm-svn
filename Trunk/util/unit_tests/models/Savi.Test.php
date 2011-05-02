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
		if (!defined('__DIR__')) {
			$iPos = strrpos(__FILE__, "/");
			define('__DIR__', substr(__FILE__, 0, $iPos) . '/');
		}
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
        $classFileName = str_replace('_',DS,$className).'.php';
        require_once ROOT_BACK.MODELS_DIRECTORY.DS.$classFileName;
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
	
	public function test_parse_file_handle() {
		//STUB
	}
	
	public function test_split_ical_lines() {
		// STUB
	}
	
	public function test_normalize_line_endings() {
		$method = self::get_method('_normalize_line_endings');
		$result = "HELLO\nWORLD";
		
		$this->assertEquals(
			$result,
			$method->invokeArgs($this->parser,array("HELLO\r\nWORLD"))
		);
		$this->assertEquals(
			$result,
			$method->invokeArgs($this->parser,array("HELLO\rWORLD"))
		);
		$this->assertEquals(
			$result,
			$method->invokeArgs($this->parser,array("HELLO\n\rWORLD"))
		);
		$this->assertEquals(
			$result,
			$method->invokeArgs($this->parser,array("HELLO\x1EWORLD"))
		);
		$this->assertEquals(
			$result,
			$method->invokeArgs($this->parser,array("HELLO\x15WORLD"))
		);
		
		$result = "HELLO\n\nWORLD";
		$this->assertEquals(
			$result,
			$method->invokeArgs($this->parser,array("HELLO\r\rWORLD"))
		);
		$this->assertEquals(
			$result,
			$method->invokeArgs($this->parser,array("HELLO\r\n\r\nWORLD"))
		);
	}
	
	public function test_parse_ical_lines() {
		// STUB
	}
    
    public function test_ical_set_element_handler() {
        // STUB
    }
    
    public function test_ical_set_character_data_handler() {
        // STUB
    }
    
    public function test_utf8_decode() {
        $test = 'ĦÉLŁO WÖЯLÐ';
        $test = mb_convert_encoding($test,'UTF8');
        
        $this->assertTrue(
            mb_check_encoding($this->parser->utf8_encode($test),'ISO-8859-1')
        );
    }
    
    public function test_utf8_encode() {
        $test = 'ĦÉLŁO WÖЯLÐ';
        $test = mb_convert_encoding($test,'ISO-8859-1');
        
        $this->assertTrue(
            mb_check_encoding($this->parser->utf8_encode($test),'UTF8')
        );
    }
    
    public function test_ical_get_error_code() {
        $this->assertEquals(
            -1,$this->parser->ical_get_error_code()
        );
    }
    
    public function test_ical_get_error_string() {
        $this->assertEquals(
            'No error',$this->parser->ical_get_error_string(-1)
        );
    }
    
    public function test_ical_get_current_line_number() {
        $this->assertEquals(
            -1,$this->parser->ical_get_current_line_number()
        );
    }
    
    public function test_get_current_byte_index() {
        // STUB
    }
    
    public function test_get_current_column_number() {
        // STUB
    }
	
	public function test_fix_line() {
		$method = self::get_method('_fix_line');
		$text = "\t\tHELLO WORLD";
		
		$this->assertEquals(
			"HELLO WORLD",
			$method->invokeArgs($this->parser,array($text))
		);
	}
	
	public function test_delimiting() {
		$delimiter = self::get_method('_delimit_replace');
		$undelimiter = self::get_method('_delimit_unreplace');
		
		$text = "HELLO: ;WORLD.";
		$newtext = $delimiter->invokeArgs($this->parser, array($text));
		$this->assertNotEquals($text,$newtext);
		
		$this->assertEquals(
			$text,
			$undelimiter->invokeArgs($this->parser,array($newtext))
		);
	}
}