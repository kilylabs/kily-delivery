<?php
namespace Kily\Delivery\Provider;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-09-27 at 15:13:22.
 */
class EdostTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Edost
     */
    protected $object;

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
     * @covers Kily\Delivery\Provider\Edost::calculateInternal
     * @todo   Implement testGetName().
     */
    public function testCalculateInternal()
    {
        if (!isset($_SERVER['EDOST_API_SHOP_ID']) || !isset($_SERVER['EDOST_API_PASS'])) {
            $this->markTestSkipped('You need to configure the EDOST_API_SHOP_ID and EDOST_API_PASS value in phpunit.xml');
        }

    }

    /**
     * @covers Kily\Delivery\Provider\Edost::getName
     */
    public function testGetName()
    {
        $object = new Edost('fake','fake');
        $this->assertEquals('edost',$object->getName());
    }

    /**
     * @covers Kily\Delivery\Provider\Edost::supports
     */
    public function testSupports()
    {
        $object = new Edost('fake','fake');
        $this->assertInternalType('array',$object->supports());
    }

    /**
     * @covers Kily\Delivery\Provider\Edost::setOptions
     * @todo   Implement testSetOptions().
     */
    public function testSetOptions()
    {
        $object = new Edost('fake','fake');
        $this->options = [
            'weight'=>1,
            'dimensions'=>'30x30x30',
        ];
    }
}