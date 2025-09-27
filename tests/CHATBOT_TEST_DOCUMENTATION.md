# Chatbot Test Suite Documentation

## Overview
This document describes the comprehensive test suite created for the CCIT chatbot system. The test suite includes 43 different test cases covering all aspects of chatbot functionality.

## Test Categories

### 1. Health Check Tests (2 tests)
- **chatbot health GET returns pong**: Verifies basic GET endpoint health check
- **chatbot selftest GET returns response**: Tests the selftest endpoint (handles both success and Dialogflow failures)

### 2. Basic Functionality Tests (3 tests)
- **chatbot POST with empty message returns error**: Validates error handling for empty messages
- **chatbot POST with missing message returns error**: Validates error handling for missing message parameter
- **chatbot POST with valid message returns response**: Basic functionality test for valid input

### 3. Greeting Tests (4 tests)
Tests various greeting inputs:
- "hello"
- "hi" 
- "good morning"
- "hey"

Expected response patterns: Hello, hi, assistant, help messages

### 4. About CCIT Tests (3 tests)
Tests information requests about the college:
- "what is CCIT"
- "about"
- "tell me about your college"

Expected response patterns: College, computer, information, technology references

### 5. Programs Tests (4 tests)
Tests program and course inquiries:
- "what programs do you offer"
- "what degrees can I get"
- "what majors do you have"
- "courses"

Expected response patterns: Bachelor, information technology, computer science, information systems

### 6. Admission Tests (4 tests)
Tests admission-related queries:
- "admission requirements"
- "how to apply"
- "apply"
- "how to enroll"

Expected response patterns: High school, diploma, entrance, exam, application, requirements

### 7. Contact Tests (3 tests)
Tests contact information requests:
- "contact information"
- "where are you located"
- "address"

Expected response patterns: Contact, campus, office, phone, email, visit

### 8. Faculty Tests (3 tests)
Tests faculty information queries:
- "tell me about your faculty"
- "teachers"
- "professors"

Expected response patterns: Faculty, teachers, professors, experienced, professionals, qualified, educators

### 9. Facilities Tests (3 tests)
Tests facility information requests:
- "what facilities do you have"
- "computer lab"
- "library"

Expected response patterns: Facilities, computer, lab, library, equipment

### 10. Thank You Tests (2 tests)
Tests gratitude expressions:
- "thank you"
- "thanks"

Expected response patterns: Welcome, happy, help, anything else

### 11. Goodbye Tests (3 tests)
Tests farewell messages:
- "goodbye"
- "bye"
- "see you later"

Expected response patterns: Goodbye, take care, feel free, anytime

### 12. Unknown Query Tests (2 tests)
Tests handling of unrecognized input:
- Random/unknown queries
- Unclear queries

Expected response patterns: Helpful suggestions directing to available topics

### 13. Edge Case Tests (4 tests)
Tests unusual input scenarios:
- **Very long messages**: 100+ word messages
- **Special characters**: Messages with symbols (@#$%^&*())
- **Mixed case queries**: ALL CAPS input
- **Whitespace handling**: Messages with extra spaces

### 14. Performance Tests (2 tests)
Tests system performance:
- **Response time**: Must respond within 2 seconds
- **Multiple requests**: Sequential request handling

### 15. Conversation Flow Tests (1 test)
Tests complete conversation scenarios:
1. Greeting → Hello response
2. Program inquiry → Program information
3. Admission inquiry → Admission details
4. Thank you → Acknowledgment

## Test Infrastructure

### Helper Function
```php
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
```

### Test Framework
- **Framework**: Pest PHP
- **HTTP Method**: cURL for HTTP requests
- **Assertions**: Expect-based assertions with pattern matching

## Running the Tests

### Command
```bash
cd "c:\xampp\htdocs\adamson-ccit"
.\vendor\bin\pest tests/Feature/ChatbotTest.php
```

### Expected Output
- **Total Tests**: 43
- **Total Assertions**: 101
- **Expected Duration**: ~30-35 seconds
- **Expected Result**: All tests should pass

## Test Coverage

### Response Validation
- HTTP status codes (200, 400, 500)
- Response content patterns
- Response time limits
- Content length validation

### Input Validation
- Empty messages
- Missing parameters
- Special characters
- Long messages
- Whitespace handling

### Functionality Coverage
- All major chatbot topics (greetings, programs, admission, contact, etc.)
- Error handling
- Edge cases
- Performance requirements

## Maintenance

### Adding New Tests
1. Follow existing naming conventions
2. Use appropriate test categories
3. Include both positive and negative test cases
4. Update this documentation

### Updating Expectations
When chatbot responses change:
1. Update regex patterns in affected tests
2. Ensure patterns are flexible enough for variations
3. Test both success and fallback scenarios

## Notes
- Tests handle both Dialogflow and fallback responses
- Flexible pattern matching accommodates response variations
- Performance tests ensure acceptable response times
- Edge case tests ensure robust error handling