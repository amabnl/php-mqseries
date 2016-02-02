<?php
/**
 * MQ Series Client library
 * 
 * Uses php's mqseries extension.
 * 
 * @package	  MQSeries
 * @author    dieter <ddevlieghere@benelux.amadeus.com>
 * @copyright 2005-2012 Copyright (c) Amadeus Benelux 
 * @link      http://www.amadeus.com/benelux/benelux.html
 */

namespace MqSeries;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;


/**
 * MQ Service object
 * 
 * This class is the public interface to use when connecting to an MQ Server.
 * 
 * Prerequisites: 
 * - IBM's C MQ Client API installed on the machine running this code (http://www-01.ibm.com/support/docview.wss?uid=swg24007092)
 * - php mqseries extension active on PHP (http://php.net/manual/en/book.mqseries.php)
 * - a queue manager and queue to connect to ;)
 * - Zend Framework 1 (for logging support)
 * 
 * @package	MQSeries
 * @subpackage Service
 * @author  dieter <ddevlieghere@benelux.amadeus.com>
 * @link    http://www.amadeus.com/benelux/benelux.html
 * @version	1.0
 */
class Service implements LoggerAwareInterface
{
    use LoggerAwareTrait;

	/**
     * Default message buffer size.
     *
	 * @var int
	 */
	const MSG_BUFFER_SIZE = 10240;
	
	
	/**
	 * @var Connectx\Params
	 */
	protected $connectParams;
	
	/**
	 * Handle to the Connection object
	 * 
	 * @var mixed
	 */
	protected $connection;
	/**
	 * Handle to the Queue object
	 * 
	 * @var mixed
	 */
	protected $queue;
	/**
	 * Generic handle to an opened object
	 * 
	 * @var mixed
	 */
	protected $objHandle;
	/**
	 * Default buffer size for messages to be reserved when doing MQ_GET
	 * 
	 * @var int
	 */
	protected $defaultMessageBuffer;
	
	/**
	 * Last message retrieved 
	 * 
	 * @var mixed
	 */
	protected $msg;
	
	/**
	 * Size of last retrieved message
	 * 
	 * @var int
	 */
	protected $msgSize;
	
	/**
	 * Completion code of last mqseries call
	 * 
	 * @var int
	 */
	protected $completionCode;
	
	/**
	 * Reason for the completion code in $this->_completionCode
	 * 
	 * @var int
	 */
	protected $reason;
	
	/**
	 * Status variable to know if we are connected to queue manager
	 * 
	 * @var boolean
	 */
	protected $isConnected = false;
	
	/**
	 * Status variable to know if connection to a queue is available
	 * 
	 * @var boolean
	 */
	protected $isQueueOpened = false;
	
	/**
	 * Whether the MQ Extension is loaded
	 * 
	 * @var boolean|null
	 */
	protected $mqExtensionLoaded;


    /**
	 * Get Completion Code from last MQSeries call
	 * 
	 * MQSERIES_MQCC_OK			= 0
	 * MQSERIES_MQCC_WARNING	= 1
	 * MQSERIES_MQCC_FAILED		= 2
	 * MQSERIES_MQCC_UNKNOWN	= -1
	 * 
	 * @return int|null
	 */
	public function getLastCompletionCode()
	{
		return $this->completionCode;
	}
	
	/**
	 * Get Reason code from last MQSeries call
	 * 
	 * One of the constants defined in cmqc.h MQRC_*
	 * 
	 * Available in PHP as MQSERIES_MQRC_*
	 * 
	 * @link http://publib.boulder.ibm.com/infocenter/wmqv7/v7r0/index.jsp?topic=%2Fcom.ibm.mq.csqsao.doc%2Ffm12040_1.htm
	 * @return int|null
	 */
	public function getLastCompletionReasonCode()
	{
		return $this->reason;
	}
	
	/**
	 * Get Reason code from last MQSeries call as text
	 * 
	 * @return string|null
	 */
	public function getLastCompletionReason()
	{
		$msg = "";
		
		if (!$this->mqseriesExtensionLoaded()) {
			$msg = 'CANNOT CONVERT REASON CODE ' . $this->reason . ' TO STRING. NO MQ EXTENSION.';
		} else {
			$msg = mqseries_strerror($this->reason);
		}
		return $msg;
	}
	
	/**
	 * Get the last message retrieved
	 * 
	 * @return string|null
	 */
	public function getLastMessage()
	{
		return $this->msg;
	}
	
	/**
	 * Get the size of the last message retrieved
	 * 
	 * @return int|null nr of Bytes of null if no last message
	 */
	public function getLastMessageSize()
	{
		return $this->msgSize;
	}
	
	/**
	 * Set the default Message Buffer size
	 * 
	 * @param int $newBuffer
	 * @throws \InvalidArgumentException if the provided buffer is not an integer
	 * @return void
	 */
	public function setDefaultMessageBuffer($newBuffer)
	{
		if (is_int($newBuffer)) {
			$this->defaultMessageBuffer = $newBuffer;
		} else {
			throw new \InvalidArgumentException("Buffer should be an integer");
		}
	}
	
	/**
	 * Check if there is a queue connection available
	 * 
	 * @return boolean
	 */
	public function isQueueOpened()
	{
		return $this->isQueueOpened;
	}
	
	
	/**
	 * Create MQSeries service object
	 * 
	 * @param LoggerInterface $logger Error logging object
	 * @param Connectx\Params|null $connectParams (OPTIONAL) Params for making connection to queue manager
	 * @param int|null $defaultMsgBuffer (OPTIONAL) Default message buffer size, defaults to self::MSG_BUFFER_SIZE
	 * @throws \InvalidArgumentException
	 */
	public function __construct($logger = null, $connectParams = null, $defaultMsgBuffer = self::MSG_BUFFER_SIZE)
	{
        if ($logger instanceof LoggerInterface) {
            $this->setLogger($logger);
        } else {
            $this->setLogger(
                new NullLogger()
            );
        }

		$this->connectParams = $connectParams;
		$this->setDefaultMessageBuffer($defaultMsgBuffer);
	}

    /**
     * Destructor closes opened connections if needed.
     */
	public function __destruct()
	{
		if ($this->isQueueOpened) {
			$this->close();
		}
		
		if ($this->isConnected) {
			$this->disconnect();
		}
	}
	
	
	/**
	 * Make a connection to an MQ Server
	 * 
	 * @param Connectx\Params $connectParams
	 * @return boolean
	 * @throws \RuntimeException If we have no connection parameters or MQSeries extension is not available
	 */
	public function connect($connectParams = null)
	{
		if ($connectParams != null) {
			$this->connectParams = $connectParams;
		}
		
		if (!$this->mqseriesExtensionLoaded()) {
			throw new ExtensionNotLoadedException("mqseries PHP extension not available");
		}
		
		if (!($this->connectParams instanceof Connectx\Params)) {
			throw new \RuntimeException("No connection parameters available");
		}
		
		mqseries_connx(
			$this->connectParams->queueManagerName,
			$this->connectParams->buildConnectionOptions(),
			$this->connection,
			$this->completionCode,
			$this->reason
		);
		
		if ($this->completionCode !== MQSERIES_MQCC_OK) {
			$this->logger->log(
                LogLevel::ERROR,
				__METHOD__."(): " . sprintf(
					"Connx CompCode:%d Reason:%d Text:%s\n",
					$this->completionCode,
					$this->reason,
					mqseries_strerror($this->reason)
				)
			);
		} else {
			$this->logger->log(
                LogLevel::INFO,
				__METHOD__."(): CONNECTED TO " . 
				$this->connectParams->queueManagerName . " - " .
				$this->connectParams->serverConnectionChannel
			);
			$this->isConnected = true;
		}
		
		return $this->isConnected;
	}
	
	/**
	 * Opens a queue manager
	 * 
	 * @param Open\Params $params
	 * @return boolean success or fail
	 * @throws QueueManagerConnectionFailedException If no connection could be made to QM
	 */
	public function openQM($params)
	{
		$funcResult = false;
		
		if (!$this->isConnected) {
			$connected = $this->connect();
			
			if (!$connected) {
				throw new QueueManagerConnectionFailedException("Could not connect to queue manager");
			}
		}
		
		$this->completionCode = null;
		$this->reason = null;
		
		mqseries_open(
			$this->connection,
			$params->buildMQODS(),
			$params->option,
			$this->objHandle,
			$this->completionCode,
			$this->reason
		);
		
		if ($this->completionCode !== MQSERIES_MQCC_OK) {
			$this->logger->error(
				__METHOD__."(): " . sprintf(
					"Open CompCode:%d Reason:%d\n",
					$this->completionCode,
					$this->reason
				)
			);
			
		} else {
			$this->logger->info(__METHOD__."(): Object successfully opened.");
			$funcResult = true;
		}
		
		return $funcResult;
	}
	
	/**
	 * Opens a queue on a queue manager
	 * 
	 * @param Open\Params $params
	 * @return boolean
	 * @throws QueueManagerConnectionFailedException If we cannot connect to queue manager
	 */
	public function openQueueOnQM($params)
	{
		if (!$this->isConnected) {
			$connected = $this->connect();
			
			if (!$connected) {
				throw new QueueManagerConnectionFailedException("Could not connect to queue manager");
			}
		}
		
		$this->completionCode = null;
		$this->reason = null;
		
		$this->logger->debug(__METHOD__."(): ABOUT TO CALL mqseries_open().");
		
		mqseries_open(
			$this->connection,
			$params->buildMQODS(),
			$params->option,
			$this->queue,
			$this->completionCode,
			$this->reason
		);
		
		$this->logger->debug(__METHOD__."(): mqseries_open() has been called.");
		
		if ($this->completionCode !== MQSERIES_MQCC_OK) {
			
			$this->logger->error(
				__METHOD__."(): " . sprintf(
					"Open CompCode:%d Reason:%d\n",
					$this->completionCode,
					$this->reason
				)
			);
			$this->isQueueOpened = false;
		} else {
			$this->logger->info(__METHOD__."(): Queue " . $params->objectName ." successfully opened.");
			$this->isQueueOpened = true;
		}
		
		return $this->isQueueOpened;
	}
	
	/**
	 * Get a message from an open queue
	 * 
	 * @param Get\Params $params
	 * @return string|boolean
     * @throws QueueUnavailableException when there is no open queue
	 */
	public function getMessageFromQueue($params)
	{
		if (!$this->isConnected || !$this->isQueueOpened) {
			throw new QueueUnavailableException("There is no queue to get a message from!");
		}

        $mqmd = $params->buildMQMD();
        $mqgmo = $params->buildMQGMO();

		mqseries_get(
			$this->connection,
			$this->queue,
            $mqmd,
            $mqgmo,
			$this->defaultMessageBuffer,
			$this->msg,
			$this->msgSize,
			$this->completionCode,
			$this->reason
		);
			
		if ($this->completionCode !== MQSERIES_MQCC_OK) {
			$this->logger->error(
				__METHOD__."(): ERROR " . sprintf(
					"GET CompCode:%d Reason:%d Text:%s\n",
					$this->completionCode,
					$this->reason,
					mqseries_strerror($this->reason)
				)
			);

            //Check if response is "2080 MQRC TRUNCATED MSG FAILED" and if so, increase message buffer:
            if ($this->getLastCompletionReasonCode() == 2080 &&
                $this->getLastCompletionCode() == MQSERIES_MQCC_WARNING) {
                $this->logger->notice(
                    'Message buffer too small. Increasing to ' . $this->getLastMessageSize() .
                    ' and trying again...'
                );
                $this->setDefaultMessageBuffer(
                    $this->getLastMessageSize()
                );

                return $this->getMessageFromQueue($params);
            } else {
                return false;
            }
		} else {
			return $this->msg;
		}
	}
	
	
	/**
	 * Closes a connection to the queue
	 * 
	 * @return boolean
	 */
	public function close()
	{
		if (!$this->isConnected || !$this->isQueueOpened) {
			return true;
		}
		
		mqseries_close(
			$this->connection,
			$this->queue,
			MQSERIES_MQCO_NONE, 
			$this->completionCode,
			$this->reason
		);
		
		if ($this->completionCode !== MQSERIES_MQCC_OK) {
			$this->logger->error(
				__METHOD__."(): " . sprintf(
					"CLOSE CompCode:%d Reason:%d Text:%s\n",
					$this->completionCode,
					$this->reason,
					mqseries_strerror($this->reason)
				)
			);
			return false;
		}
		
		$this->isQueueOpened = false;
		
		return true;
	}
	
	
	/**
	 * Disconnect an open connection to an MQ Server
	 * 
	 * @return boolean
	 */
	public function disconnect()
	{
		if (!$this->isConnected || $this->connection === null) {
			$this->isConnected = false;
			return true;
		}
		
		mqseries_disc($this->connection, $this->completionCode, $this->reason);
		
		if ($this->completionCode !== MQSERIES_MQCC_OK) {
			$this->logger->error(
				__METHOD__."(): " . sprintf(
					"DISC CompCode:%d Reason:%d Text:%s\n",
					$this->completionCode,
					$this->reason,
					mqseries_strerror($this->reason)
				)
			);
			return false;
		}
		
		$this->isQueueOpened = false;
		$this->isConnected = false;
		
		return true;	
	}
	
	
	/**
	 * Checks if PHP mqseries extension is available
	 * 
	 * If not loaded, tries to load extension through dl function.
	 * 
	 * @return boolean
	 */
	protected function mqseriesExtensionLoaded()
	{
		if (is_null($this->mqExtensionLoaded)) {
			$result = false;
			
			if (extension_loaded('mqseries')) {
				$result = true;
			} else {
				if (function_exists('dl')) {
					if (preg_match('/windows/i', getenv('OS'))) {
						$result = dl('php_mqseries.dll');
					} else {
						$result = dl('mqseries.so');
					}
				}
			}
			$this->mqExtensionLoaded = $result;
		}
		
		return $this->mqExtensionLoaded;
	}
}
