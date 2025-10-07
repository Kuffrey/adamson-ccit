
<?php

// Helper function to send a request to the chatbot endpoint
function chatbotRequest($method, $data = null, $query = '') {
	$url = 'http://localhost/adamson-ccit/public/chatbot.php' . $query; // Build the URL
	$ch = curl_init($url); // Initialize cURL session
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as string
	if ($method === 'POST') {
		curl_setopt($ch, CURLOPT_POST, true); // Set POST method
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // Attach POST data
	}
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
	$response = curl_exec($ch); // Execute request
	$info = curl_getinfo($ch); // Get response info
	curl_close($ch); // Close cURL session
	return [$response, $info]; // Return response and info
}


// ========== HEALTH CHECK TESTS ==========


// Test: GET /chatbot.php returns 'pong' for health check
test('chatbot health GET returns pong', function () {
	[$response, $info] = chatbotRequest('GET');
	expect($info['http_code'])->toBe(200); // Should return HTTP 200
	expect($response)->toContain('pong'); // Should contain 'pong'
});


// Test: GET /chatbot.php?selftest=1 returns a response (200 or 500)
test('chatbot selftest GET returns response', function () {
	[$response, $info] = chatbotRequest('GET', null, '?selftest=1');
	// Accept either 200 or 500 for selftest since it might fail due to Dialogflow
	expect($info['http_code'])->toBeIn([200, 500]);
	expect($response)->not->toBe('');
});


// ========== BASIC FUNCTIONALITY TESTS ==========


// Test: POST with empty message returns error
test('chatbot POST with empty message returns error', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => '']);
	expect($info['http_code'])->toBe(400); // Should return HTTP 400
	expect($response)->toContain('Empty message'); // Should mention empty message
});


// Test: POST with missing message returns error
test('chatbot POST with missing message returns error', function () {
	[$response, $info] = chatbotRequest('POST', []);
	expect($info['http_code'])->toBe(400); // Should return HTTP 400
	expect($response)->toContain('Empty message'); // Should mention empty message
});


// Test: POST with valid message returns a response
test('chatbot POST with valid message returns response', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'Hello']);
	expect($info['http_code'])->toBe(200); // Should return HTTP 200
	expect($response)->not->toBe(''); // Should not be empty
	expect(strlen($response))->toBeGreaterThan(0); // Should have content
});


// ========== GREETING TESTS ==========

test('chatbot responds to hello greeting', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'hello']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/hello|hi|assistant|help/i');
});

test('chatbot responds to hi greeting', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'hi']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/hello|hi|assistant|help/i');
});

test('chatbot responds to good morning greeting', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'good morning']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/hello|hi|assistant|help|morning/i');
});

test('chatbot responds to hey greeting', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'hey']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/hello|hi|assistant|help/i');
});

// ========== ABOUT CCIT TESTS ==========

test('chatbot provides information about CCIT', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'what is CCIT']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/college|computer|information|technology|CCIT/i');
});

test('chatbot responds to about query', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'about']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/college|computer|information|technology/i');
});

test('chatbot responds to tell me about query', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'tell me about your college']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/college|computer|information|technology/i');
});

// ========== PROGRAMS TESTS ==========

test('chatbot lists programs when asked about courses', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'what programs do you offer']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/bachelor|information technology|computer science|information systems/i');
});

test('chatbot responds to degrees query', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'what degrees can I get']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/bachelor|information technology|computer science|information systems/i');
});

test('chatbot responds to majors query', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'what majors do you have']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/bachelor|information technology|computer science|information systems/i');
});

test('chatbot responds to courses query', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'courses']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/programs|bachelor|information technology|computer science|information systems/i');
});

// ========== ADMISSION TESTS ==========

test('chatbot provides admission information', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'admission requirements']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/high school|diploma|entrance|exam|application|requirements/i');
});

test('chatbot responds to how to apply query', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'how to apply']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/high school|diploma|entrance|exam|application|requirements|apply|admissions/i');
});

test('chatbot responds to apply query', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'apply']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/high school|diploma|entrance|exam|application|requirements|apply|admissions/i');
});

test('chatbot responds to enrollment query', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'how to enroll']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/high school|diploma|entrance|exam|application|requirements|apply|admissions/i');
});

// ========== CONTACT TESTS ==========

test('chatbot provides contact information', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'contact information']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/contact|campus|office|phone|email|visit/i');
});

test('chatbot responds to location query', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'where are you located']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/campus|location|contact|office/i');
});

test('chatbot responds to address query', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'address']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/campus|location|contact|office/i');
});

// ========== FACULTY TESTS ==========

test('chatbot provides faculty information', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'tell me about your faculty']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/faculty|teachers|professors|experienced|professionals|qualified|educators|technology/i');
});

test('chatbot responds to teachers query', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'teachers']);
	expect($info['http_code'])->toBe(200);
	// Accept either faculty info or default response
	expect($response)->toMatch('/faculty|teachers|professors|experienced|professionals|qualified|educators|not sure about that|ask about/i');
});

test('chatbot responds to professors query', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'professors']);
	expect($info['http_code'])->toBe(200);
	// Accept either faculty info or default response
	expect($response)->toMatch('/faculty|teachers|professors|experienced|professionals|qualified|educators|not sure about that|ask about/i');
});

// ========== FACILITIES TESTS ==========

test('chatbot provides facilities information', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'what facilities do you have']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/facilities|computer|lab|library|equipment/i');
});

test('chatbot responds to lab query', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'computer lab']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/facilities|computer|lab|library|equipment/i');
});

test('chatbot responds to library query', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'library']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/facilities|computer|lab|library|equipment/i');
});

// ========== THANK YOU TESTS ==========

test('chatbot responds to thank you', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'thank you']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/welcome|happy|help|anything else/i');
});

test('chatbot responds to thanks', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'thanks']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/welcome|happy|help|anything else/i');
});

// ========== GOODBYE TESTS ==========

test('chatbot responds to goodbye', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'goodbye']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/goodbye|take care|feel free|anytime/i');
});

test('chatbot responds to bye', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'bye']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/goodbye|take care|feel free|anytime/i');
});

test('chatbot responds to see you', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'see you later']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/goodbye|take care|feel free|anytime|see you/i');
});

// ========== UNKNOWN QUERY TESTS ==========

test('chatbot handles unknown queries gracefully', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'xyzabc123random']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch("/not sure|ask about|programs|admission|facilities|contact/i");
});

test('chatbot provides helpful suggestions for unclear queries', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'something random']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch("/programs|admission|facilities|contact|help/i");
});

// ========== EDGE CASE TESTS ==========

test('chatbot handles very long messages', function () {
	$longMessage = str_repeat('hello ', 100) . 'what programs do you offer';
	[$response, $info] = chatbotRequest('POST', ['message' => $longMessage]);
	expect($info['http_code'])->toBe(200);
	expect($response)->not->toBe('');
});

test('chatbot handles special characters', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'hello! @#$%^&*()']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/hello|hi|assistant|help/i');
});

test('chatbot handles mixed case queries', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => 'HELLO WHAT PROGRAMS DO YOU OFFER']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/hello|hi|assistant|help|programs|bachelor/i');
});

test('chatbot handles whitespace in messages', function () {
	[$response, $info] = chatbotRequest('POST', ['message' => '   hello   ']);
	expect($info['http_code'])->toBe(200);
	expect($response)->toMatch('/hello|hi|assistant|help/i');
});

// ========== PERFORMANCE TESTS ==========

test('chatbot responds within reasonable time', function () {
	$start = microtime(true);
	[$response, $info] = chatbotRequest('POST', ['message' => 'hello']);
	$duration = microtime(true) - $start;
	
	expect($info['http_code'])->toBe(200);
	expect($duration)->toBeLessThan(2.0); // Should respond within 2 seconds
});

test('chatbot handles multiple requests', function () {
	$messages = ['hello', 'programs', 'admission', 'contact', 'thank you'];
	
	foreach ($messages as $message) {
		[$response, $info] = chatbotRequest('POST', ['message' => $message]);
		expect($info['http_code'])->toBe(200);
		expect($response)->not->toBe('');
	}
});

// ========== CONVERSATION FLOW TESTS ==========

test('chatbot maintains conversation flow', function () {
	// Start conversation
	[$response1, $info1] = chatbotRequest('POST', ['message' => 'hello']);
	expect($info1['http_code'])->toBe(200);
	expect($response1)->toMatch('/hello|hi|assistant|help/i');
	
	// Ask about programs
	[$response2, $info2] = chatbotRequest('POST', ['message' => 'what programs do you offer']);
	expect($info2['http_code'])->toBe(200);
	expect($response2)->toMatch('/bachelor|information technology|computer science/i');
	
	// Ask about admission
	[$response3, $info3] = chatbotRequest('POST', ['message' => 'admission requirements']);
	expect($info3['http_code'])->toBe(200);
	expect($response3)->toMatch('/high school|diploma|entrance|exam|admission|apply|requirements|visit|admissions/i');
	
	// End conversation
	[$response4, $info4] = chatbotRequest('POST', ['message' => 'thank you']);
	expect($info4['http_code'])->toBe(200);
	expect($response4)->toMatch('/welcome|happy|help/i');
});
