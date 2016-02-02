<?php
/**
 * MQSeries PHP Library
 *
 * @package	  MQSeries
 * @author    dieter <ddevlieghere@benelux.amadeus.com>
 * @copyright 2005 - 2012 Copyright (c) Amadeus Benelux
 * @link      http://www.amadeus.com/benelux/benelux.html
 */

namespace MqSeries\Open;

/**
 * Parameters object for the MQOPEN command
 *
 * @package	MQSeries
 * @author  dieter <ddevlieghere@benelux.amadeus.com>
 * @link    http://www.amadeus.com/benelux/benelux.html
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
