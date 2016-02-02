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

	//Create connection:
    $connectParams = new MqSeries\Connectx\Params();

	$client = new MqSeries\Service(
		new Psr\Log\NullLogger(),
		$params,
		50000  //Default message size
	);

	//Open Queue:
	$openParams = new MqSeries\Open\Params(); 

	try {
		$client->openQueueOnQM($openParams);
	} catch (MqSeries\OpenQueueException $ex) {
		die('Exception when opening queue: ' . $ex->getCode() . '$ex->getMessage());
	}

	//Get one message from queue:
	

	//Close & disconnect:

	

# Versions

This library is tested with a WebSphere MQ Client 7.1 release on PHP 5.4, 5.5, 5.6 using mqseries-0.14.2.