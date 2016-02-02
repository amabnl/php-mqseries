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

namespace MqSeries\Get;

/**
 * Parameters object for the MQGET command
 *
 * @package	MqSeries\Get
 * @author  dieter <dieter.devlieghere@benelux.amadeus.com>
 */
class Params
{
	/**
	 * @var int
	 */
	public $mdMsgType;
	/**
	 * @var int
	 */
	public $mdPersistence;
	/**
	 * @var string
	 */
	public $mdFormat;
	/**
	 * @var string
	 */
	public $mdApplOriginData;
	/**
	 * @var string
	 */
	public $mdReplyToQ;
	/**
	 * @var string
	 */
	public $mdReplyToQMgr;
	/**
	 * @var int
	 */
	public $gmoOptions;
	/**
	 * @var int
	 */
	public $gmoWaitInterval;
	
	/**
	 * Builds message descriptor
	 * 
	 * @return array
	 */
	public function buildMQMD()
	{
		$mqmd = array(
			'MsgType' => $this->mdMsgType,
			'Persistence' => $this->mdPersistence,
			'Format' => $this->mdFormat, 
			'ApplOriginData' => $this->mdApplOriginData, 
			'ReplyToQ' => $this->mdReplyToQ,
			'ReplyToQMgr' => $this->mdReplyToQMgr   
		);
		
		return $mqmd;
	}

	/**
	 * Builds message options
	 * 
	 * @return array
	 */
	public function buildMQGMO()
	{
		$mqgmo = array(
			'Options' => $this->gmoOptions,
			'WaitInterval' => $this->gmoWaitInterval, 
		);
		
		return $mqgmo;
	}
}
