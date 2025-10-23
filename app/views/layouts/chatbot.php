<?php /* app/views/layouts/chatbot.php */ ?>
<div id="ccit-chat-root">
  <!-- Floating Chatbot Button -->
  <button id="chatbot-toggle" type="button" title="Chat with us!">💬</button>

  <!-- Chatbot Box -->
  <div id="chatbot-box" aria-live="polite" hidden>
    <div class="header">
      <span>CCIT Chatbot</span>
      <!-- Inline fallback ensures it closes even if some JS listener didn't bind -->
      <button id="chatbot-close" type="button" aria-label="Close" onclick="window.__ccitCloseChat && window.__ccitCloseChat()">×</button>
    </div>

    <div id="chat-output" class="chat-output">
      <div class="bot-msg">Hi! I'm your CCIT assistant. How can I help you today?</div>
    </div>

    <form id="chat-form" autocomplete="off">
      <input type="text" id="chat-input" placeholder="Type your question..." required />
      <button type="submit" id="chat-send">➤</button>
    </form>
  </div>
</div>

