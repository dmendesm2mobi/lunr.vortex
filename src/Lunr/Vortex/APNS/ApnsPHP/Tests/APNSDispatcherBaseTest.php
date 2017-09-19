<?php

/**
 * This file contains the APNSDispatcherTest class.
 *
 * PHP Version 5.6
 *
 * @package    Lunr\Vortex\APNS
 * @author     Damien Tardy-Panis <damien@m2mobi.com>
 * @copyright  2016-2017, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Vortex\APNS\ApnsPHP\Tests;

use Lunr\Halo\PropertyTraits\PsrLoggerTestTrait;

/**
 * This class contains test for the constructor of the APNSDispatcher class.
 *
 * @covers Lunr\Vortex\APNS\ApnsPHP\APNSDispatcher
 */
class APNSDispatcherBaseTest extends APNSDispatcherTest
{

    use PsrLoggerTestTrait;

    /**
     * Test that the endpoints property is set to an empty array by default.
     */
    public function testEndpointsIsEmptyArray()
    {
        $this->assertPropertySame('endpoints', []);
    }

    /**
     * Test that the payload property is set to an empty JSON object by default.
     */
    public function testPayloadIsEmptyJSONObject()
    {
        $this->assertPropertySame('payload', '{}');
    }

    /**
     * Test that the APNS message property is set to NULL.
     */
    public function testAPNSMessageIsNull()
    {
        if (defined('REFLECTION_BUG_72194') && REFLECTION_BUG_72194 === TRUE)
        {
            $this->markTestSkipped('Reflection can\'t handle this right now');
            return;
        }

        $this->assertPropertyEmpty('apns_message');
    }

}

?>