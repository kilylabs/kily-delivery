<?php
namespace Kily\Delivery\Base;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-09-27 at 15:13:22.
 */
class ComponentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Component
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        require_once(__DIR__.'/fixtures/TestComponent.php');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kily\Delivery\Base\Component::__get
     */
    public function test__get()
    {
        $object = new TestComponent;
        $object->thing = 'ololo';
        $this->assertEquals('ololo',$object->thing);
        $this->assertEquals('ololo',$object->getThing());
        $this->assertEquals(1,$object->readonly);
    }

    /**
     * @covers Kily\Delivery\Base\Component::__get
     * @expectedException \Kily\Delivery\Exception\PropertyNotDefined
     */
    public function test__getNotDefined()
    {
        $object = new Component;
        $object->test;
    }

    /**
     * @covers Kily\Delivery\Base\Component::__set
     */
    public function test__set()
    {
        $object = new TestComponent;
        $object->thing = 'ololo';
        $this->assertEquals('ololo',$object->thing);
    }

    /**
     * @covers Kily\Delivery\Base\Component::__set
     * @expectedException \Kily\Delivery\Exception\PropertyNotDefined
     */
    public function test__setNotDefined()
    {
        $object = new TestComponent;
        $object->test = 3;
    }

    /**
     * @covers Kily\Delivery\Base\Component::__set
     * @expectedException \Kily\Delivery\Exception\PropertyReadonly
     */
    public function test__setReadonly()
    {
        $object = new TestComponent;
        $object->readonly = 3;
    }

    /**
     * @covers Kily\Delivery\Base\Component::__isset
     */
    public function test__isset()
    {
        $object = new TestComponent;
        $this->assertFalse(isset($object->test));
    }

    /**
     * @covers Kily\Delivery\Base\Component::__unset
     */
    public function test__unset()
    {
        $object = new TestComponent;
        unset($object->thing);
    }

    /**
     * @covers Kily\Delivery\Base\Component::__unset
     */
    public function test__unsetNotDefined()
    {
        $object = new TestComponent;
        unset($object->test);
    }

    /**
     * @covers Kily\Delivery\Base\Component::__unset
     * @expectedException \Kily\Delivery\Exception\PropertyReadonly
     */
    public function test__unsetReadonly()
    {
        $object = new TestComponent;
        unset($object->readonly);
    }
}
