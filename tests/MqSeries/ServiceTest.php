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

namespace Test\MqSeries;

use MqSeries\Service;
use Psr\Log\NullLogger;

/**
 * ServiceTest
 *
 * @package Test\MqSeries
 */
class ServiceTest extends \PHPUnit_Framework_TestCase
{
	/* http://rubywmq.rubyforge.org/doc/classes/WMQ.html */
	public function setUp()
	{
		parent::setUp();
	
		/**
		defined('MQSERIES_MQCNO_STANDARD_BINDING')
			|| define('MQSERIES_MQCNO_STANDARD_BINDING', 0x00000000);
		
		defined('MQSERIES_MQXPT_TCP')
			|| define('MQSERIES_MQXPT_TCP', 2);
		
		defined('MQSERIES_MQCC_WARNING')
			|| define('MQSERIES_MQCC_WARNING', 1);
			
		defined('MQSERIES_MQCC_OK')
			|| define('MQSERIES_MQCC_OK', 0);

		defined('MQSERIES_MQCC_FAILED')
			|| define('MQSERIES_MQCC_FAILED', 2);
			
		defined('MQSERIES_MQCC_UNKNOWN')
			|| define('MQSERIES_MQCC_UNKNOWN', -1);

		defined('MQSERIES_MQCO_NONE')
			|| define('MQSERIES_MQCO_NONE', 0x00000000);
			
		defined('MQSERIES_MQOT_Q')
			|| define('MQSERIES_MQOT_Q', 1);	
		
		defined('MQSERIES_MQOO_INPUT_AS_Q_DEF')
			|| define('MQSERIES_MQOO_INPUT_AS_Q_DEF', 0x00000001);	
		
		defined('MQSERIES_MQOO_FAIL_IF_QUIESCING')
			|| define('MQSERIES_MQOO_FAIL_IF_QUIESCING', 0x00002000);	
			
		defined('MQSERIES_MQMT_DATAGRAM')
			|| define('MQSERIES_MQMT_DATAGRAM', 8);	
			
		defined('MQSERIES_MQPER_NOT_PERSISTENT')
			|| define('MQSERIES_MQPER_NOT_PERSISTENT', 0);	
		
		defined('MQSERIES_MQFMT_STRING')
			|| define('MQSERIES_MQFMT_STRING', 'MQSTR');	
		
		defined('MQSERIES_MQGMO_FAIL_IF_QUIESCING')
			|| define('MQSERIES_MQGMO_FAIL_IF_QUIESCING', 0x00002000);	
		
		defined('MQSERIES_MQGMO_WAIT')
			|| define('MQSERIES_MQGMO_WAIT', 0x00000001);	
		
		defined('MQSERIES_MQGMO_CONVERT')
			|| define('MQSERIES_MQGMO_CONVERT', 0x00004000);	
		*/
		
	}
	
	public function testPhpHasMqExtension()
	{
		$this->markTestIncomplete('No dll available yet');
		
		$test = new Service(new NullLogger());
		
		$method = $this->getMethod($test, "mqseriesExtensionLoaded");
		
		$mqExtensionLoaded = $method->invokeArgs($test, array());
		
		$this->assertTrue($mqExtensionLoaded, "php mqseries extension is not loaded. Cannot use this library!");
	}
	
	/**
	 * @return \MqSeries\Service
	 */
	public function testCanInstantiate()
	{
		$serviceObj = new Service(new NullLogger(), $this->makeConnParamsObj());
		
		$this->assertInstanceOf("MqSeries\\Service", $serviceObj);
		
		return $serviceObj;
	}
	
	
	
	/**
	 * Dummy MQCONNECTX params
	 * 
	 * @return \MqSeries\Connectx\Params
	 */
	protected function makeConnParamsObj()
	{
		$parObj = new \MqSeries\Connectx\Params();
		
		$parObj->queueManagerName = "QUEUEMANAGER";
    	$parObj->serverConnectionChannel = "CONNECTIONCHANNEL";
    	$parObj->serverIp = "QUEUEMANAGERSERVERIP";
    	$parObj->serverPort = "1555";
    	$parObj->keyRepository = "PATHTOKEYREPOSITORY";
    	$parObj->responderUrl = "http://dummy.OCSP.responder";
    	$parObj->sslCipherSpec = "NULL_SHA";
		$parObj->options = MQSERIES_MQCNO_STANDARD_BINDING;
		
		return $parObj;
	}

    /**
     * Get a protected or private method from object
     *
     * @param string $name
     * @return \ReflectionMethod
     */
    protected static function getMethod($object, $name)
    {
        $method = new \ReflectionMethod($object, $name);
        $method->setAccessible(true);
        return $method;
    }
}