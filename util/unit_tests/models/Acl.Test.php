<?php
require_once('globals.php');

/**
 *	Unit Test for the Acl class.
 *
 *	@author Stephen Simpson <me@simpo.org>
 *	@version 0.1.1
 *	@license http://www.gnu.org/licenses/lgpl.html
 *	@package UnitTests.Impact
 *	@extends ImpactPHPUnit
 */
class Test_Acl extends ImpactPHPUnit {
	
	protected function setUp() {
        $this->init();
		$application = Application::instance();
	}
	
	public function test_load_roles() {
		
		$this->instance->load_roles('[WEB][ADMIN][DEV]');
		$this->assertMethodReturnFalse(array('[WEB2]'),'allowed');
		
		$this->instance->load_roles('[WEB2]');
		$this->assertMethodReturnTrue(array('[WEB2]'),'allowed');
	}
	
	public function test_allowed() {
		
		$this->instance->load_roles('[WEB][ADMIN][DEV]');
		$this->assertMethodReturnTrue(array('[WEB][FB:USER:93][DEVELOPER]','[WEB2]'));
		$this->assertMethodReturnFalse(array('[WEB],[FB:USER:93],[DEVELOPER]','[DEV]'));
		$this->assertMethodReturnTrue(array('[WEB]'));
		$this->assertMethodReturnFalse(array('','[WEB]'));
		$this->assertMethodReturnFalse(array('[DEV],[ADMIN]','[WEB]'));
		$this->assertMethodReturnFalse(array('[WEB2]','[WEB][ADMIN][WEB3]'));
	}
	
	public function test_test_role() {
		$this->instance->load_roles('[WEB][ADMIN][DEV]');
		$method = self::get_method('test_role');
		
		/*$this->assertTrue(
			$method->invokeArgs($this->instance, array('[WEB][FB:USER:93][DEVELOPER]'))
		);
		$this->assertFalse(
			$method->invokeArgs($this->instance, array('[WEB2][FB:USER:93][DEVELOPER]'))
		);*/
	}
	
	public function test_split_special_role() {
		$method = self::get_method('_split_special_role');
		
		/*$this->assertEquals(
			array('FB','USER',array('93')),
			$method->invokeArgs($this->instance, array('[FB:USER:93]'))
		);
		$this->assertEquals(
			array('GEO','RADIUS',array('39.0335','-78.4838','90','KM')),
			$method->invokeArgs($this->instance, array('[GEO:RADIUS:39.0335:-78.4838:90:KM]'))
		);*/
	}
}
?>