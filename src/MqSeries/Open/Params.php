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

namespace MqSeries\Open;

/**
 * Parameters object for the MQOPEN command
 *
 * @package	MqSeries\Open
 * @author  dieter <dieter.devlieghere@benelux.amadeus.com>
 */
class Params
{
	/**
	 * @var int
	 */
	public $objectDescType;
	
	/**
	 * @var string
	 */
	public $objectName;
	
	/**
	 * @var string
	 */
	public $objectQMName;
	
	/**
	 * @var int
	 */
	public $option;
	
	/**
	 * @var int
	 */
	public $version;
	
	/**
	 * @var string
	 */
	public $strucId;
	
	
	/**
	 * @return array
	 */
	public function buildMQODS()
	{
		$arr = [];
		
		if (isset($this->objectDescType)) {
			$arr['ObjectType'] = $this->objectDescType;
		}
		
		if (isset($this->objectName)) {
			$arr['ObjectName'] = $this->objectName;
		}
		
		if (isset($this->objectQMName)) {
			$arr['ObjectQMgrName'] = $this->objectQMName;			
		}
		
		if (isset($this->version)) {
			$arr['Version'] = $this->version;
		}
		
		if (isset($this->strucId)) {
			$arr['StrucID'] = $this->strucId;
		}
		
		return $arr;
	}
}
