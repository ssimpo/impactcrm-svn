<?php
require_once('globals.php');

/**
 *	Unit Test for the SAVI class.
 *
 *	@author Stephen Simpson <me@simpo.org>
 *	@version 0.1.1
 *	@license http://www.gnu.org/licenses/lgpl.html
 *	@package UnitTests.Impact
 *	@extends ImpactPHPUnit
 */
class Test_Savi extends ImpactPHPUnit {

    protected function setUp() {
        $this->init();
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
		
		$result = "HELLO\nWORLD";
		$this->assertMethodReturn($result,"HELLO\r\nWORLD");
		$this->assertMethodReturn($result,"HELLO\rWORLD");
		$this->assertMethodReturn($result,"HELLO\n\rWORLD");
		$this->assertMethodReturn($result,"HELLO\x1EWORLD");
		$this->assertMethodReturn($result,"HELLO\x15WORLD");
		
		$result = "HELLO\n\nWORLD";
		$this->assertMethodReturn($result,"HELLO\r\rWORLD");
		$this->assertMethodReturn($result,"HELLO\r\n\r\nWORLD");
	}
	
	public function test_parse_ical_lines() {
		// STUB
	}
	
	public function test_fix_line() {
		
		$result = "HELLO WORLD";
		$this->assertMethodReturn($result,"\tHELLO WORLD");
		$this->assertMethodReturn($result,"\t\t\tHELLO WORLD");
		
	}
	
	public function test_line_parser() {
		$method = self::get_method('_line_parser');
		
		$this->assertMethodReturn(
			array(
				'tag' => 'DTSTART',
				'attributes' => array(),
				'content' => '19700329T010000',
				'rawtextcontent' => '19700329T010000'
			),
			'DTSTART:19700329T010000'
		);
		
		$this->assertMethodReturn(
			array(
				'tag' => 'X-WR-CALNAME',
				'attributes' => array(),
				'content' => 'The Christian Centre, Middlesbrough',
				'rawtextcontent' => 'The Christian Centre, Middlesbrough'
			),
			'X-WR-CALNAME:The Christian Centre\, Middlesbrough'
		);
		
		$this->assertMethodReturn(
			array(
				'tag' => 'RRULE',
				'attributes' => array(),
				'content' => array('FREQ'=>'YEARLY','BYMONTH'=>'3','BYDAY'=>'-1SU'),
				'rawtextcontent' => 'FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU'
			),
			'RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU'
		);
		
		$this->assertMethodReturn(
			array(
				'tag' => 'DTSTART',
				'attributes' => array('TZID'=>'Europe/London'),
				'content' => '20110417T103000',
				'rawtextcontent' => '20110417T103000'
			),
			'DTSTART;TZID=Europe/London:20110417T103000'
		);
		
		$this->assertMethodReturn(
			array(
				'tag' => 'DTSTART',
				'attributes' => array('TZID'=>'Europe/London'),
				'content' => '20110417T103000',
				'rawtextcontent' => '20110417T103000'
			),
			'DTSTART;TZID="Europe/London":20110417T103000'
		);
		
		$this->assertMethodReturn(
			array(
				'tag' => 'CATEGORIES',
				'attributes' => array(),
				'content' => array('Cat1','Cat2','Cat3'),
				'rawtextcontent' => 'Cat1,Cat2,Cat3'
			),
			'CATEGORIES:Cat1,Cat2,Cat3'
		);
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
            mb_check_encoding($this->instance->utf8_encode($test),'ISO-8859-1')
        );
    }
    
    public function test_utf8_encode() {
        $test = 'ĦÉLŁO WÖЯLÐ';
        $test = mb_convert_encoding($test,'ISO-8859-1');
        
        $this->assertTrue(
            mb_check_encoding($this->instance->utf8_encode($test),'UTF8')
        );
    }
    
    public function test_ical_get_error_code() {
        $this->assertEquals(
            -1,$this->instance->ical_get_error_code()
        );
    }
    
    public function test_ical_get_error_string() {
        $this->assertEquals(
            'No error',$this->instance->ical_get_error_string(-1)
        );
    }
    
    public function test_ical_get_current_line_number() {
        $this->assertEquals(
            -1,$this->instance->ical_get_current_line_number()
        );
    }
    
    public function test_get_current_byte_index() {
        // STUB
    }
    
    public function test_get_current_column_number() {
        // STUB
    }
	
	public function test_delimiting() {
		$delimiter = self::get_method('_delimit_replace');
		$undelimiter = self::get_method('_delimit_unreplace');
		
		$text = 'HELLO\: \;WORLD\.';
		$newtext = $delimiter->invokeArgs($this->instance, array($text));
		$this->assertNotEquals($text,$newtext);
		
		$this->assertEquals(
			'HELLO: ;WORLD\.',
			$undelimiter->invokeArgs($this->instance,array($newtext))
		);
		
		$text = 'HELLO: ;WORLD.';
		$this->assertEquals(
			$text,
			$delimiter->invokeArgs($this->instance, array($text))
		);
	}
}