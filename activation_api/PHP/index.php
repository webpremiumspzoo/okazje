<?php
	$url = 'https://okazje.webpremium.pl/crontab/activateapi';

	// generujemy parametry polaczenia
	$params['client_id'] = 'WPROWADZ DANE';
	$params['client_secret'] = 'WPROWADZ DANE';
	$params['promotion_id'] = "3000000000";
	$params['action'] = 'activate';
	$checkSum = md5(json_encode($params));
	$params['checksum'] = $checkSum;
	
	// tworzymy polaczenie
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( $params ) );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$output = curl_exec ($ch);
	
	echo $output;

?> 
