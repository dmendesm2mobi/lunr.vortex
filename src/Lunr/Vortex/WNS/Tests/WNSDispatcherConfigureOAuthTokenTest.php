<?php
/**
 * This file contains the WNSDispatcherConfigureOAuthTokenTest Class
 *
 * @package    Lunr\Vortex\WNS
 * @author     Heinz Wiesinger <heinz.wiesinger@moveagency.com>
 * @copyright  2023, Move Agency Group B.V., Zwolle, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Vortex\WNS\Tests;

use WpOrg\Requests\Exception as RequestsException;

/**
 * Class WNSDispatcherConfigureOAuthTokenTest tests the Authentication to the WNS Server
 * @covers Lunr\Vortex\WNS\WNSDispatcher
 */
class WNSDispatcherConfigureOAuthTokenTest extends WNSDispatcherTest
{

    /**
     * Prepares the configuration for a $config call.
     *
     * @param string $client_id     The client_id you want returned from the config
     * @param string $client_secret The client_secret you want returned
     *
     * @return void
     */
    private function expectFromConfig($client_id, $client_secret): void
    {
        $this->set_reflection_property_value('client_id', $client_id);
        $this->set_reflection_property_value('client_secret', $client_secret);
    }

    /**
     * Test that using configure_oauth_token queries with config variables.
     *
     * @covers Lunr\Vortex\WNS\WNSDispatcher::configure_oauth_token
     */
    public function testConfigureOauthTokenMakesCorrectRequest(): void
    {
        $request_post = [
            'grant_type'    => 'client_credentials',
            'client_id'     => '012345',
            'client_secret' => '012345678',
            'scope'         => 'notify.windows.com',
        ];

        $headers = [ 'Content-Type' => 'application/x-www-form-urlencoded' ];
        $url     = 'https://login.live.com/accesstoken.srf';

        $this->response->status_code = 200;
        $this->response->body        = '{"access_token":"access_token"}';

        $this->expectFromConfig('012345', '012345678');

        $this->http->expects($this->once())
                   ->method('post')
                   ->with($this->equalTo($url), $this->equalTo($headers), $this->equalTo($request_post))
                   ->will($this->returnValue($this->response));

        $this->class->configure_oauth_token();

        $this->assertPropertySame('oauth_token', 'access_token');
    }

    /**
     * Test that using configure_oauth_token responds with FALSE if the response is erroneous.
     *
     * @covers Lunr\Vortex\WNS\WNSDispatcher::configure_oauth_token
     */
    public function testConfigureOauthTokenRespondsFalseIfRequestError(): void
    {
        $request_post = [
            'grant_type'    => 'client_credentials',
            'client_id'     => '012345',
            'client_secret' => '012345678',
            'scope'         => 'notify.windows.com',
        ];

        $this->expectFromConfig('012345', '012345678');

        $headers = [ 'Content-Type' => 'application/x-www-form-urlencoded' ];
        $url     = 'https://login.live.com/accesstoken.srf';

        $this->http->expects($this->once())
                   ->method('post')
                   ->with($this->equalTo($url), $this->equalTo($headers), $this->equalTo($request_post))
                   ->will($this->throwException(new RequestsException('Network error!', 'curlerror', NULL)));

        $this->logger->expects($this->once())
                     ->method('warning')
                     ->with('Requesting token failed: No response');

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Requesting token failed: No response');

        $this->class->configure_oauth_token();
    }

    /**
     * Test that using configure_oauth_token responds with FALSE if the response is invalid JSON.
     *
     * @covers Lunr\Vortex\WNS\WNSDispatcher::configure_oauth_token
     */
    public function testConfigureOauthTokenRespondsFalseIfInvalidJSON(): void
    {
        $request_post = [
            'grant_type'    => 'client_credentials',
            'client_id'     => '012345',
            'client_secret' => '012345678',
            'scope'         => 'notify.windows.com',
        ];

        $this->expectFromConfig('012345', '012345678');

        $headers = [ 'Content-Type' => 'application/x-www-form-urlencoded' ];
        $url     = 'https://login.live.com/accesstoken.srf';

        $this->response->status_code = 200;
        $this->response->body        = 'HELLO I\'m an invalid JSON object';

        $this->http->expects($this->once())
                   ->method('post')
                   ->with($this->equalTo($url), $this->equalTo($headers), $this->equalTo($request_post))
                   ->will($this->returnValue($this->response));

        $this->logger->expects($this->once())
                     ->method('warning')
                     ->with('Requesting token failed: Malformed JSON response');

        $this->expectException('UnexpectedValueException');
        $this->expectExceptionMessage('Requesting token failed: Malformed JSON response');

        $this->class->configure_oauth_token();
    }

    /**
     * Test that using configure_oauth_token responds with FALSE if the response is incomplete JSON.
     *
     * @covers Lunr\Vortex\WNS\WNSDispatcher::configure_oauth_token
     */
    public function testConfigureOauthTokenRespondsFalseIfIncompleteJSON(): void
    {
        $request_post = [
            'grant_type'    => 'client_credentials',
            'client_id'     => '012345',
            'client_secret' => '012345678',
            'scope'         => 'notify.windows.com',
        ];

        $this->expectFromConfig('012345', '012345678');

        $headers = [ 'Content-Type' => 'application/x-www-form-urlencoded' ];
        $url     = 'https://login.live.com/accesstoken.srf';

        $this->response->status_code = 200;
        $this->response->body        = '{"Message": "HELLO I\'m an invalid JSON object"}';

        $this->http->expects($this->once())
                   ->method('post')
                   ->with($this->equalTo($url), $this->equalTo($headers), $this->equalTo($request_post))
                   ->will($this->returnValue($this->response));

        $this->logger->expects($this->once())
                     ->method('warning')
                     ->with('Requesting token failed: Not a valid JSON response');

        $this->expectException('UnexpectedValueException');
        $this->expectExceptionMessage('Requesting token failed: Not a valid JSON response');

        $this->class->configure_oauth_token();
    }

    /**
     * Test that using configure_oauth_token response.
     *
     * @covers Lunr\Vortex\WNS\WNSDispatcher::configure_oauth_token
     */
    public function testConfigureOauthTokenRespondsCorrectly(): void
    {
        $request_post = [
            'grant_type'    => 'client_credentials',
            'client_id'     => '012345',
            'client_secret' => '012345678',
            'scope'         => 'notify.windows.com',
        ];

        $this->expectFromConfig('012345', '012345678');

        $headers = [ 'Content-Type' => 'application/x-www-form-urlencoded' ];
        $url     = 'https://login.live.com/accesstoken.srf';

        $this->response->status_code = 200;
        $this->response->body        = '{"access_token":"access_token"}';

        $this->http->expects($this->once())
                   ->method('post')
                   ->with($this->equalTo($url), $this->equalTo($headers), $this->equalTo($request_post))
                   ->will($this->returnValue($this->response));

        $this->class->configure_oauth_token();

        $this->assertPropertySame('oauth_token', 'access_token');
    }

}
