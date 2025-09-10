// public/assets/js/chatbot.js
(function () {
  if (window.__ccitChatInit) return; window.__ccitChatInit = true;

  document.addEventListener("DOMContentLoaded", () => {
    // Remove duplicates if the partial was accidentally included twice
    const boxes = Array.from(document.querySelectorAll("#chatbot-box"));
    const toggles = Array.from(document.querySelectorAll("#chatbot-toggle"));
    for (let i = 1; i < boxes.length; i++) boxes[i].remove();
    for (let i = 1; i < toggles.length; i++) toggles[i].remove();

    const box     = document.getElementById("chatbot-box");
    const toggle  = document.getElementById("chatbot-toggle");
    const closeBtn= document.getElementById("chatbot-close");
    const form    = document.getElementById("chat-form");
    const input   = document.getElementById("chat-input");
    const output  = document.getElementById("chat-output");
    if (!box || !toggle || !closeBtn || !form || !input || !output) return;

    // --- robust open/close: toggle both hidden attribute and inline display ---
    const showBox = () => { box.hidden = false; box.style.display = "flex"; };
    const hideBox = () => { box.hidden = true;  box.style.display = "none"; };

    const open  = () => { showBox(); setTimeout(() => input && input.focus(), 20); };
    const close = () => { hideBox(); };

    // Expose a global fallback the close button can call via onclick
    window.__ccitCloseChat = close;

    // Toggle button: true toggle
    toggle.addEventListener("click", (e) => {
      e.preventDefault(); e.stopPropagation();
      if (box.hidden || box.style.display === "none") open(); else close();
    });

    // ✕ must always close
    closeBtn.addEventListener("click", (e) => {
      e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
      close();
    });

    // ESC closes
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && !(box.hidden || box.style.display === "none")) close();
    });

    // Click outside closes; clicks inside do not bubble
    document.addEventListener("click", (e) => {
      if (!(box.hidden || box.style.display === "none") && !box.contains(e.target) && e.target !== toggle) close();
    });
    box.addEventListener("click", (e) => e.stopPropagation());

    // --- chat logic (unchanged) ---
    const ENDPOINT = "chatbot.php";

    const add = (role, text) => {
      const div = document.createElement("div");
      div.className = role === "user" ? "user-msg" : "bot-msg";
      div.textContent = text;
      output.appendChild(div);
      output.scrollTop = output.scrollHeight;
    };

    const withTimeout = (p, ms) =>
      Promise.race([p, new Promise((_, rej) => setTimeout(() => rej(new Error("timeout")), ms))]);

    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      const message = (input.value || "").trim();
      if (!message) return;

      add("user", message);
      input.value = "";

      const typing = document.createElement("div");
      typing.className = "bot-msg";
      typing.textContent = "Typing...";
      output.appendChild(typing);
      output.scrollTop = output.scrollHeight;

      try {
        const res = await withTimeout(fetch(ENDPOINT, {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({ message })
        }), 15000);

        const text = (await res.text() || "").trim();
        if (!res.ok) throw new Error(text || `HTTP ${res.status}`);
        if (/<!DOCTYPE|<html|<head|<body/i.test(text)) throw new Error("Unexpected HTML response");

        typing.remove();
        add("bot", text || "I didn’t catch that.");
      } catch (err) {
        typing.remove();
        add("bot",
          err && err.message === "timeout"
            ? "The server took too long. Please try again."
            : (err?.message || "Network/server error.")
        );
      }
    });
  });
})();
