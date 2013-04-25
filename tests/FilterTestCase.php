<?php
abstract class FilterTestCase extends PHPUnit_Framework_TestCase
{
    protected $_phpB;

    public function setUp()
    {
        $this->_phpB = new PHP_Beautifier();
        $this->_phpB->addFilter('DWS');
    }

    protected function assertFormat($input, $output)
    {
        $this->_phpB->setInputString($input);
        $this->_phpB->process($input);
        $this->assertEquals($output, $this->_phpB->get());
    }
}
