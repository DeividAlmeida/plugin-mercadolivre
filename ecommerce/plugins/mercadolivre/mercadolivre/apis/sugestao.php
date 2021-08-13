<?php
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.mercadolibre.com/sites/MLB/domain_discovery/search?q='.$_GET['nome'],
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer APP_USR-6746500504163026-100511-067db8df0d5d351ad6f4efaf67c69c57-177309895',
    'Cookie: _d2id=158348cf-9209-4017-aa35-0ee8cb0ab3a1-n'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;