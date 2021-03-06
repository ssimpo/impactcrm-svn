<?php
require_once('globals.php');

/**
 *	Unit Test for the Template class.
 *
 *	@author Stephen Simpson <me@simpo.org>
 *	@version 0.1.1
 *	@license http://www.gnu.org/licenses/lgpl.html
 *	@package UnitTests.Impact
 *	@extends ImpactPHPUnit
 */
class Test_Template_Constant extends ImpactPHPUnit {
	private $acl;
	
	protected function setUp() {
		$this->init();
		$this->acl = $this->getMock('Acl',array('allowed'));
		$this->acl->expects($this->any())->method('allowed')->will($this->returnValue(true));
	}
	
	public function test_parse() {
		$result1 = 'Impact';
		define('CLASSNAME','Impact');
        
        $this->instance->init(array('acl'=>$this->acl));
        
        $data = array(
            'block'=>'','tagname'=>'variable','content'=>'CLASSNAME',
            'attributes'=>array()
        );
        $this->assertEquals(
            $result1,
            $this->instance->parse($data)
        );
	}
}