<?php
function Template($file, $params = array()) {
	foreach($params as $k => $v) {
		$$k = $v;
	}

	ob_start();
	include $file;
	return ob_get_clean();
}

function auth($user, $subdomain) {
    $link = 'https://'.$subdomain.'.amocrm.ru/private/api/auth.php?type=json';

    $response = send_request($link, $user, 'CURLOPT_POST');
    $response = json_decode($response["response_str"], TRUE);

    return isset($response["response"]["auth"]);
}

function send_request($link, $post_data = [], $type = FALSE) {
    $curl = curl_init(); #��������� ���������� ������ cURL

    #������������� ����������� ����� ��� ������ cURL
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
    curl_setopt($curl, CURLOPT_URL, $link);
    if($type == 'CURLOPT_POST') {
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data));
    } elseif($type == 'CURLOPT_CUSTOMREQUEST') {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }
    curl_setopt($curl, CURLOPT_HEADER, FALSE);
    curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

    sleep(1); #���� �������

    $out = curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
    $code = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE); #������� HTTP-��� ������ �������
    curl_close($curl); #��������� ����� cURL

    return ["response_str" => $out, "code" => $code];
}