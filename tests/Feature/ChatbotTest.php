<?php

function chatbotRequest($method, $data = null, $query = '') {
	$url = 'http://localhost/adamson-ccit/public/chatbot.php' . $query;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	if ($method === 'POST') {
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	}
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	$response = curl_exec($ch);
	$info = curl_getinfo($ch);
	curl_close($ch);
	return [$response, $info];
}

test('chatbot health GET returns pong', function () {
	[$response, $info] = chatbotRequest('GET');
	expect($info['http_code'])->toBe(200);
	expect($response)->toContain('pong');
});

test('chatbot selftest GET returns ok or error', function () {
	[$response, $info] = chatbotRequest('GET', null, '?selftest=1');
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/ok|Error/');
});

test('chatbot POST returns a response', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'Hello']);
	expect($info['http_code'])->toBe(200);
	expect($response)->not->toBe('');
});
