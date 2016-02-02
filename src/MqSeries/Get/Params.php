<?php
/**
 * MQSeries PHP Library
 *
 * @package	  MQSeries
 * @author    dieter <ddevlieghere@benelux.amadeus.com>
 * @copyright 2005 - 2012 Copyright (c) Amadeus Benelux
 * @link      http://www.amadeus.com/benelux/benelux.html
 */

namespace MqSeries\Get;

/**
 * Parameters object for the MQGET command
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
