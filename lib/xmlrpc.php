<?php
#############################################################################
## xmlrpc.php - helper XMLRPC handling routines                            ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################

// submits XMLRPC request through HTTP protocol and retrieves HTTP response
function _requestHTTP($host,$port,$request)
{
	$socket = fsockopen($host, $port);
	if (!$socket) return;
	$content_len = strlen($request);
	$http_request=
         "POST / HTTP/1.0\r\n" .
         "User-Agent: PHP\r\n" .
         "Host: $host:$port\r\n" .
         "Content-Type: text/xml\r\n" .
         "Content-Length: $content_len\r\n" . 
         "\r\n" .
         $request;

	fputs($socket, $http_request);

	$header_parser = false;
	while (!feof($socket))
	{
		$line = fgets($socket, 4096);
		if (!$header_parsed)
		{
			if ($line === "\r\n" || $line === "\n")
				$header_parsed = true;
		}
		else
			$response .= $line;
	}

	return $response;
}

// performs XMLRPC call by encoding input request and submitting it through HTTP
function xmlrpc_call($host, $port, $method, $params)
{
	$request = xmlrpc_encode_request($method, $params);
	if ($request)
		$response = _requestHTTP($host, $port, $request);

	return xmlrpc_decode($response);
}
?>