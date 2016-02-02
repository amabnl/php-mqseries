<?php
/**
 * php-mqseries
 *
 * Copyright 2015-2016 Amadeus Benelux NV
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package MqSeries
 * @license https://opensource.org/licenses/Apache-2.0 Apache 2.0
 */

namespace Test\MqSeries\Connectx;

/**
 * ParamsTest
 *
 * @package Test\MqSeries\Connectx
 */
class ParamsTest extends \PHPUnit_Framework_TestCase
{	
	/* http://rubywmq.rubyforge.org/doc/classes/WMQ.html */
	public function setUp()
	{
		parent::setUp();
		
		defined('MQSERIES_MQCNO_STANDARD_BINDING')
    		|| define('MQSERIES_MQCNO_STANDARD_BINDING', 0x00000000);
    		
    	defined('MQSERIES_MQXPT_TCP')
    		|| define('MQSERIES_MQXPT_TCP', 2);
	}
	
	public function testCanBuildParams()
	{
		$params = $this->makeConnParamsObj();
		
		$this->assertInstanceOf("MQSeries_Connectx_Params", $params);
		
		$buildArray = $params->buildConnectionOptions();
		
		$this->assertInternalType('array', $buildArray);
		
		$this->assertArrayHasKey('Version', $buildArray);
		$this->assertArrayHasKey('Options', $buildArray);
		$this->assertArrayHasKey('MQCD', $buildArray);
		$this->assertArrayHasKey('MQSCO', $buildArray);
		
		$this->assertEquals(4, $buildArray['Version']);
		$this->assertEquals(MQSERIES_MQCNO_STANDARD_BINDING, $buildArray['Options']);
		
		$this->assertInternalType('array', $buildArray['MQCD']);
		
		$this->assertArrayHasKey('Version', $buildArray['MQCD']);
		$this->assertArrayHasKey('ChannelName', $buildArray['MQCD']);
		$this->assertArrayHasKey('ConnectionName', $buildArray['MQCD']);
		$this->assertArrayHasKey('TransportType', $buildArray['MQCD']);
		$this->assertArrayHasKey('SSLCipherSpec', $buildArray['MQCD']);
		
		$this->assertEquals(7, $buildArray['MQCD']['Version']);
		$this->assertEquals("ACO_BNL_GWT11.BT1", $buildArray['MQCD']['ChannelName']);
		$this->assertEquals('82.150.225.70(1651)', $buildArray['MQCD']['ConnectionName']);
		$this->assertEquals(MQSERIES_MQXPT_TCP, $buildArray['MQCD']['TransportType']);
		$this->assertEquals('NULL_SHA', $buildArray['MQCD']['SSLCipherSpec']);
		
		$this->assertInternalType('array', $buildArray['MQSCO']);
		
		$this->assertArrayHasKey('KeyRepository', $buildArray['MQSCO']);
		$this->assertArrayHasKey('MQAIR', $buildArray['MQSCO']);
		
		$this->assertEquals("/var/mqm/qmgrs/GWT11.BT1/ssl2/key", $buildArray['MQSCO']['KeyRepository']);
		$this->assertInternalType('array', $buildArray['MQSCO']['MQAIR']);
		
		$this->assertArrayHasKey('Version', $buildArray['MQSCO']['MQAIR']);
		$this->assertArrayHasKey('AuthInfoType', $buildArray['MQSCO']['MQAIR']);
		$this->assertArrayHasKey('OCSPResponderURL', $buildArray['MQSCO']['MQAIR']);
		
		$this->assertEquals(2, $buildArray['MQSCO']['MQAIR']["Version"]);
		$this->assertEquals(2, $buildArray['MQSCO']['MQAIR']["AuthInfoType"]);
		$this->assertEquals("http://dummy.OCSP.responder", $buildArray['MQSCO']['MQAIR']["OCSPResponderURL"]);
	}
	
	
	
	/**
	 * Dummy MQCONNECTX params
	 *
	 * @return \MqSeries\Connectx\Params
	 */
	protected function makeConnParamsObj()
	{
		$parObj = new \MqSeries\Connectx\Params();
	
		$parObj->queueManagerName = "GWT11.BT1";
		$parObj->serverConnectionChannel = "ACO_BNL_GWT11.BT1";
		$parObj->serverIp = "82.150.225.70";
		$parObj->serverPort = "1651";
		$parObj->keyRepository = "/var/mqm/qmgrs/GWT11.BT1/ssl2/key";
		$parObj->responderUrl = "http://dummy.OCSP.responder";
		$parObj->sslCipherSpec = "NULL_SHA";
		$parObj->options = MQSERIES_MQCNO_STANDARD_BINDING;
	
		return $parObj;
	}
}
