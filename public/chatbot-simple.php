<?php
// public/chatbot-simple.php - Simple pattern-based chatbot (no Google Cloud required)
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

/* ---------- GET: health check ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(['ok' => true, 'message' => 'simple chatbot ready']);
  exit;
}

/* ---------- POST: simple chat ---------- */
$userMessage = trim((string)($_POST['message'] ?? ''));
if ($userMessage === '') {
  http_response_code(400);
  echo 'Empty message.';
  exit;
}

// Simple pattern matching responses
$responses = [
  // Greetings
  'hello|hi|hey|good morning|good afternoon|good evening' => [
    "Hello! I'm your CCIT assistant. How can I help you today?",
    "Hi there! What would you like to know about our college?",
    "Hello! I'm here to help with any questions about CCIT."
  ],
  
  // About CCIT
  'about|what is|tell me about|information about' => [
    "CCIT stands for College of Computer and Information Technology. We offer excellent programs in IT, Computer Science, and Information Systems.",
    "We're a leading technology college offering undergraduate and graduate programs in computer-related fields."
  ],
  
  // Programs
  'programs|courses|degrees|what can i study|majors' => [
    "We offer several programs:\n• Bachelor of Science in Information Technology\n• Bachelor of Science in Computer Science\n• Bachelor of Science in Information Systems\n• Graduate programs are also available!",
    "Our main programs include IT, Computer Science, and Information Systems. Would you like details about any specific program?"
  ],
  
  // Admission
  'admission|apply|requirements|how to apply|enroll' => [
    "For admission requirements:\n• High school diploma or equivalent\n• Entrance exam scores\n• Application form\n• Required documents\n\nVisit our admissions office for detailed requirements!",
    "You can apply online or visit our admissions office. Requirements vary by program - would you like specific details?"
  ],
  
  // Contact
  'contact|phone|email|address|location|where' => [
    "You can reach us at:\n• Visit our campus\n• Check our official website\n• Call our main office\n• Email our admissions team\n\nWould you like specific contact details for any department?",
    "We're located at our main campus. For specific contact information, please visit our contact page or admissions office."
  ],
  
  // Faculty
  'faculty|teachers|professors|staff' => [
    "Our faculty consists of experienced professionals and educators in technology fields. Many have industry experience and advanced degrees.",
    "We have qualified faculty members specializing in various areas of computer science, IT, and information systems."
  ],
  
  // Facilities
  'facilities|lab|library|computer lab|equipment' => [
    "Our facilities include:\n• Modern computer laboratories\n• Well-equipped library\n• High-speed internet\n• Audio-visual rooms\n• Study areas",
    "We have state-of-the-art facilities including computer labs, library resources, and modern learning spaces."
  ],
  
  // Thanks
  'thank you|thanks|thank u' => [
    "You're welcome! Feel free to ask if you have any other questions.",
    "Happy to help! Is there anything else you'd like to know about CCIT?",
    "You're very welcome! Let me know if you need more information."
  ],
  
  // Goodbye
  'bye|goodbye|see you|take care' => [
    "Goodbye! Feel free to return anytime if you have more questions about CCIT.",
    "Take care! We're always here to help with your CCIT inquiries.",
    "See you later! Don't hesitate to ask if you need more information."
  ]
];

// Find matching pattern
$response = "I'm not sure about that. Could you ask about our programs, admission requirements, facilities, or contact information? I'm here to help with CCIT-related questions!";

$message = strtolower($userMessage);
foreach ($responses as $pattern => $possibleResponses) {
  $patterns = explode('|', $pattern);
  foreach ($patterns as $p) {
    if (strpos($message, trim($p)) !== false) {
      $response = $possibleResponses[array_rand($possibleResponses)];
      break 2;
    }
  }
}

header('Content-Type: text/plain; charset=UTF-8');
echo $response;