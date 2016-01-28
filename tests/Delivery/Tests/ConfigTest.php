<?php
namespace Kily\Delivery;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-09-27 at 15:12:20.
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kily\Delivery\Config::getInstance
     */
    public function testGetInstance()
    {
        $this->assertInstanceOf('\Kily\Delivery\Config',Config::getInstance());
    }

    /**
     * @covers Kily\Delivery\Config::get
     */
    public function testGet()
    {
        Config::get('test');
    }

    /**
     * @covers Kily\Delivery\Config::set
     */
    public function testSet()
    {
        Config::set('test','test');
    }
}