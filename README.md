# php-mqseries
A library to interface with a WebSphere MQ Queue Manager using the PHP mqseries pecl extension

# Purpose

This library was built to be able to make a queue listener for a WebSphere MQ Queue in PHP, using the PECL module mqseries.

It helps you with creating the correct structures to make a connection to a WebSphere MQ Queue Manager. 

It currently supports opening a connection to a queue manager, opening a specific queue, and retrieving messages from the queue.

# How

- Install the WebSphere MQ Client library. 
  - [Download from here](http://www-01.ibm.com/software/integration/wmq/clients/ "Download from here") 
  - [IBM documentation](http://www-01.ibm.com/support/knowledgecenter/SSFKSJ_7.1.0/com.ibm.mq.doc/zi00110_.htm "IBM Documentation")
- Install the PECL module mqseries. Download from here: https://pecl.php.net/package/mqseries
  - [documentation](http://www.php.net/mqseries) 
- Install this library
- Make a connection to a queue and retrieve messages.

# Code sample

```php	
	<?php

	use MqSeries;

	//Create connection:
    $connectParams = new Connectx\Params();
	$connectParams->queueManagerName = 'QUEUEMANAGERNAME';
	$connectParams->serverConnectionChannel = 'CONNECTIONCHANNEL';
	$connectParams->serverIp = 'QUEUEMANAGERIPADDRESS';
	$connectParams->serverPort = 6666; //Port number to connect to
	$connectParams->keyRepository = '/var/mqm/qmgrs/path/to/ssl/key/repository/'; //OPTIONAL SSL KEY REPO IF SSL IS USED
	$connectParams->responderUrl = 'http://dummy.OCSP.responder'; //OPTIONAL OCSP Responder
	$connectParams->sslCipherSpec = 'RC4_SHA_US'; //OPTIONAL SSL cipher spec
	//$connectParams->localAddress = '127.0.0.1'; //OPTIONAL: TO CONNECT FROM A SPECIFIC LOCAL NETWORK INTERFACE.
	//$connectParams->localPort = 16666; //OPTIONAL: TO CONNECT FROM A SPECIFIC LOCAL PORT
	$connectParams->options = MQSERIES_MQCNO_STANDARD_BINDING;

	$client = new Service(
		new Psr\Log\NullLogger(),
		$connectParams,
		50000  //Default message size
	);

	//Open Queue:
	$openParams = new Open\Params(); 
	$openParams->objectDescType = MQSERIES_MQOT_Q;
	$openParams->objectName = 'QUEUENAME';
	$openParams->objectQMName  = 'QUEUEMANAGERNAME';
	$openParams->option = MQSERIES_MQOO_INPUT_AS_Q_DEF | MQSERIES_MQOO_FAIL_IF_QUIESCING;

	try {
		$queueOpenResult = $client->openQueueOnQM($openParams);
	} catch (QueueManagerConnectionFailedException $ex) {
		die('Exception when opening queue: ' . $ex->getCode() . ' - ' . $ex->getMessage());
	} catch (ExtensionNotLoadedException $ex) {
		die('YOU MUST FIRST ENABLE THE mqseries PHP EXTENSION');
	} catch (NoConnectionParametersException $ex) {
		die('YOU DID NOT PROVIDE CONNECTX PARAMS!');
	} 

	//Get one message from queue:
	if ($queueOpenResult !== true) {
		die(
			'SOMETHING WENT WRONG WHEN OPENING THE QUEUE: ' . 
			sprintf(
				"CompCode:%d Reason:%d Text:%s\n",
				$client->getLastCompletionCode(), 
				$client->getLastCompletionReasonCode(), 
				$client->getLastCompletionReason()
			)
		);
	}

	$mqGetParams = new Get\Params(); 
	$mqGetParams->mdMsgType = MQSERIES_MQMT_DATAGRAM;
	$mqGetParams->mdPersistence = MQSERIES_MQPER_NOT_PERSISTENT;
	$mqGetParams->mdFormat = MQSERIES_MQFMT_STRING;
	$mqGetParams->mdApplOriginData = 'IEDI';
	$mqGetParams->mdReplyToQ = 'ERRORQUEUENAME';
	$mqGetParams->mdReplyToQMgr = 'QUEUEMANAGERTOREPLYTO';
	$mqGetParams->gmoOptions = MQSERIES_MQGMO_FAIL_IF_QUIESCING | MQSERIES_MQGMO_WAIT | MQSERIES_MQGMO_CONVERT;
	$mqGetParams->gmoWaitInterval = 15000;
	
	try {
		$messageContent = $client->getMessageFromQueue($this->makeMqGetMessageFromQParams());
	catch (QueueIsEmptyException $ex) {
		echo "The queue is empty, no big deal.";
	}

	if (is_string($messageContent)) {
		echo 'message retrieved from queue: ' . $messageContent;
	} else {
		die(
			'SOMETHING WENT WRONG WHEN RETRIEVING A MESSAGE: ' . 
			sprintf(
				"CompCode:%d Reason:%d Text:%s\n",
				$client->getLastCompletionCode(), 
				$client->getLastCompletionReasonCode(), 
				$client->getLastCompletionReason()
			)
		);
	}

	//Close & disconnect:
	$client->close();
    $client->disconnect();

```

# Versions

This library is tested with a WebSphere MQ Client 7.1 release on PHP 5.4, 5.5, 5.6 using mqseries-0.14.2.