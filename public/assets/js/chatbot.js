(function () {
  const chat = document.getElementById('chat');
  const form = document.getElementById('chat-form');
  const input = document.getElementById('chat-input');
  const sendBtn = document.getElementById('chat-send');

  function escapeHTML(str) {
    const div = document.createElement('div');
    div.innerText = str;
    return div.innerHTML;
  }

  function scrollToBottom() {
    chat.scrollTop = chat.scrollHeight;
  }

  function appendUserMessage(text) {
    const el = document.createElement('div');
    el.className = 'msg user';
    el.innerHTML = escapeHTML(text);
    chat.appendChild(el);
    scrollToBottom();
  }

  function appendTypingBubble() {
    const wrapper = document.createElement('div');
    wrapper.className = 'msg model';
    wrapper.setAttribute('data-typing', 'true');
    wrapper.innerHTML = '<span class="typing" aria-label="Assistant is typing">' +
                        '<span class="dot"></span><span class="dot"></span><span class="dot"></span>' +
                        '</span>';
    chat.appendChild(wrapper);
    scrollToBottom();
    return wrapper;
  }

  function replaceTypingWithReply(typingEl, replyText) {
    typingEl.removeAttribute('data-typing');
    typingEl.innerHTML = escapeHTML(replyText);
    scrollToBottom();
  }

  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const message = (input.value || '').trim();
    if (!message) return;

    // 1) Instant user echo
    appendUserMessage(message);

    // 2) Typing indicator
    const typingEl = appendTypingBubble();

    // 3) Disable while waiting
    input.value = '';
    input.focus();
    sendBtn.disabled = true;
    chat.setAttribute('aria-busy', 'true');

    try {
      let convoId = null;

      const res = await fetch('endpoints/chat.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message, convoId  }),
      });

      // Session expired / not authorized
      if (res.status === 401) {
        replaceTypingWithReply(typingEl, 'Ikke autorisert. Omdirigerer til innlogging …');
        window.location = 'login.php';
        return;
      }

      if (!res.ok) {
        const text = await res.text().catch(() => '');
        throw new Error(text || ('HTTP ' + res.status));
      }

      const data = await res.json();
      if (typeof data.convoId === 'number') {
        convoId = data.convoId;
      }
      const reply = (data && data.responseMessage)
        ? data.responseMessage
        : 'Sorry, I could not generate a response.';

      replaceTypingWithReply(typingEl, reply);
    } catch (err) {
      console.error(err);
      replaceTypingWithReply(typingEl, 'Sorry, something went wrong. Please try again.');
    } finally {
      sendBtn.disabled = false;
      chat.setAttribute('aria-busy', 'false');
      scrollToBottom();
    }
  });
})();
