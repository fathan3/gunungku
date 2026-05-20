<?php
require_once __DIR__ . '/../config/database.php'; require_once __DIR__ . '/../includes/functions.php'; requireLogin(); $pageTitle='Chatbot Asisten Pendakian';$currentPage='chatbot'; include __DIR__.'/../includes/header.php'; include __DIR__.'/../includes/layout_top.php';
?>
<div class="grid xl:grid-cols-[1fr_360px] gap-6 h-[calc(100vh-190px)] min-h-[650px]">
  <div class="editorial-card overflow-hidden flex flex-col">
    <div class="p-6 border-b border-outline-variant/30 flex items-center justify-between"><div class="flex items-center gap-4"><div class="w-12 h-12 rounded-2xl bg-primary text-white flex items-center justify-center"><span class="material-symbols-outlined">smart_toy</span></div><div><h3 class="text-xl font-extrabold">Gunungku Assistant</h3><p class="text-sm text-on-surface-variant">Asisten pendakian, simaksi, dan perlengkapan</p></div></div><div class="flex items-center gap-3"><span class="badge-soft bg-green-100 text-green-800">Online</span><button onclick="clearChatHistory()" class="flex items-center justify-center w-8 h-8 rounded-full hover:bg-surface-variant/40 text-on-surface-variant transition" title="Hapus Riwayat Chat"><span class="material-symbols-outlined text-lg">delete</span></button></div></div>
    <div id="chat-container" class="flex-1 p-6 overflow-auto chat-scrollbar space-y-5 bg-[#fbfcf7]">
      <div class="flex gap-3"><div class="w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center"><span class="material-symbols-outlined text-sm">smart_toy</span></div><div class="max-w-[75%] rounded-2xl rounded-tl-sm bg-surface-container-high px-5 py-4"><p class="font-semibold">Halo <?=e(userName())?>! Mau merencanakan pendakian kemana?</p><p class="text-sm text-on-surface-variant mt-1">Saya bisa bantu cek jalur, checklist gear, dan alur simaksi.</p></div></div>
    </div>
    <form class="p-5 border-t border-outline-variant/30 bg-white flex gap-3" onsubmit="handleChatSubmit(event); return false;" action="javascript:void(0);"><input id="chat-input" class="flex-1 rounded-2xl bg-surface-variant/40 border-0 px-5 py-4 focus:ring-2 focus:ring-primary outline-none" placeholder="Tulis pertanyaan pendakian..."><button type="submit" class="btn-primary"><span class="material-symbols-outlined">send</span></button></form>
  </div>
  <aside class="space-y-6"><div class="editorial-card p-6 bg-alpine-gradient text-white"><h3 class="text-xl font-extrabold mb-3">Quick Prompt</h3><p class="text-white/80 mb-4">Gunakan pertanyaan cepat untuk simulasi asisten.</p><div class="space-y-3"><button onclick="sendQuickPrompt('Cek cuaca Merbabu')" class="w-full bg-white/10 border border-white/20 hover:bg-white/20 transition rounded-2xl px-4 py-3 text-left">Cek cuaca Merbabu</button><button onclick="sendQuickPrompt('Buat checklist 2 hari')" class="w-full bg-white/10 border border-white/20 hover:bg-white/20 transition rounded-2xl px-4 py-3 text-left">Buat checklist 2 hari</button><button onclick="sendQuickPrompt('Syarat simaksi')" class="w-full bg-white/10 border border-white/20 hover:bg-white/20 transition rounded-2xl px-4 py-3 text-left">Syarat simaksi</button></div></div><div class="editorial-card p-6"><h3 class="text-xl font-extrabold mb-4">Fitur</h3><div class="space-y-3"><div class="editorial-card-soft p-4">Rekomendasi jalur</div><div class="editorial-card-soft p-4">Checklist gear</div><div class="editorial-card-soft p-4">Info simaksi</div></div></div></aside>
</div>
<script>
  const userName = "<?=e(userName())?>";
  const userInitials = "<?=e(initialName())?>";
  
  let isLoading = true;

  const loadChatHistory = () => {
    const stored = localStorage.getItem('chatHistory');
    if(stored){
      try{ const history = JSON.parse(stored);
        history.forEach(item => addMessage(item.text, item.isUser));
      }catch(e){ console.error('Failed to parse chat history', e); }
    }
    isLoading = false;
  };

  const saveMessage = (text, isUser) => {
    const stored = localStorage.getItem('chatHistory');
    let history = [];
    if(stored){
      try{ history = JSON.parse(stored); }catch(e){ history = []; }
    }
    history.push({text, isUser});
    localStorage.setItem('chatHistory', JSON.stringify(history));
  };

  const parseMarkdown = (text) => {
    // Escape HTML to prevent XSS
    let html = text
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");

    // Bold (**text** or __text__)
    html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    html = html.replace(/__(.*?)__/g, '<strong>$1</strong>');

    // Italic (*text* or _text_)
    html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');
    html = html.replace(/_(.*?)_/g, '<em>$1</em>');

    // Code blocks (```code```)
    html = html.replace(/```([\s\S]*?)```/g, '<pre class="bg-surface-variant/50 p-3 rounded-lg overflow-x-auto my-2 font-mono text-xs text-slate-800">$1</pre>');

    // Inline code (`code`)
    html = html.replace(/`(.*?)`/g, '<code class="bg-surface-variant/50 px-1.5 py-0.5 rounded font-mono text-xs text-slate-800">$1</code>');

    // Headers (e.g., ### Header)
    html = html.replace(/^\s*###\s+(.*?)$/gm, '<h4 class="text-base font-bold mt-3 mb-1 text-slate-900">$1</h4>');
    html = html.replace(/^\s*##\s+(.*?)$/gm, '<h3 class="text-lg font-bold mt-4 mb-2 text-slate-900">$1</h3>');
    html = html.replace(/^\s*#\s+(.*?)$/gm, '<h2 class="text-xl font-bold mt-5 mb-2 text-slate-900">$1</h2>');

    // Bullet list items (starting with - or * or +)
    html = html.replace(/^\s*[-*+]\s+(.*?)$/gm, '<li class="ml-4 list-disc">$1</li>');

    // Numbered list items (starting with 1., 2. etc)
    html = html.replace(/^\s*\d+\.\s+(.*?)$/gm, '<li class="ml-4 list-decimal">$1</li>');

    // Replace double newlines with paragraphs, and single newlines with <br>
    html = html.split('\n').map(line => {
      const trimmed = line.trim();
      if (trimmed.startsWith('<li') || trimmed.startsWith('<h') || trimmed.startsWith('<pre') || trimmed.startsWith('</pre>')) {
        return line;
      }
      return line ? `<p class="mb-1">${line}</p>` : '<div class="h-2"></div>';
    }).join('\n');

    return html;
  };

  const addMessage = (text, isUser) => {
    const container = document.getElementById('chat-container');
    const msgDiv = document.createElement('div');
    if (isUser) {
      msgDiv.className = 'flex gap-3 justify-end opacity-0 transition-opacity duration-300';
      msgDiv.innerHTML = `<div class="max-w-[75%] rounded-2xl rounded-tr-sm bg-primary text-white px-5 py-4">${text}</div><div class="w-9 h-9 rounded-full bg-primary-container text-white flex items-center justify-center font-bold">${userInitials}</div>`;
    } else {
      msgDiv.className = 'flex gap-3 opacity-0 transition-opacity duration-300';
      msgDiv.innerHTML = `<div class="w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center"><span class="material-symbols-outlined text-sm">smart_toy</span></div><div class="max-w-[80%] rounded-2xl rounded-tl-sm bg-surface-container-high px-5 py-4 text-slate-800">${parseMarkdown(text)}</div>`;
    }
    container.appendChild(msgDiv);
    setTimeout(() => msgDiv.classList.remove('opacity-0'), 50);
    container.scrollTop = container.scrollHeight;
    if(!isLoading) saveMessage(text, isUser);

  };

  let isFetchingResponse = false;
  let typingIndicator = null;

  const showTypingIndicator = () => {
    if (typingIndicator) return;
    const container = document.getElementById('chat-container');
    typingIndicator = document.createElement('div');
    typingIndicator.className = 'flex gap-3 opacity-0 transition-opacity duration-300';
    typingIndicator.innerHTML = `
      <div class="w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center">
        <span class="material-symbols-outlined text-sm">smart_toy</span>
      </div>
      <div class="max-w-[80%] rounded-2xl rounded-tl-sm bg-surface-container-high px-5 py-4 flex items-center gap-1.5">
        <div class="w-2 h-2 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 0ms;"></div>
        <div class="w-2 h-2 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 150ms;"></div>
        <div class="w-2 h-2 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 300ms;"></div>
      </div>
    `;
    container.appendChild(typingIndicator);
    setTimeout(() => typingIndicator.classList.remove('opacity-0'), 50);
    container.scrollTop = container.scrollHeight;
  };

  const removeTypingIndicator = () => {
    if (typingIndicator) {
      typingIndicator.remove();
      typingIndicator = null;
    }
  };

  const setFormState = (disabled) => {
    const input = document.getElementById('chat-input');
    const button = document.querySelector('form button[type="submit"]');
    if (input) input.disabled = disabled;
    if (button) {
      button.disabled = disabled;
      if (disabled) {
        button.classList.add('opacity-50', 'cursor-not-allowed');
      } else {
        button.classList.remove('opacity-50', 'cursor-not-allowed');
      }
    }
  };

  document.addEventListener('DOMContentLoaded', () => {
    loadChatHistory();
  });

  // Fetch response from Gemini API backend
  const fetchGeminiResponse = async (message) => {
    try {
      const res = await fetch('api/gemini.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message })
      });
      if (!res.ok) {
        const errData = await res.json().catch(() => ({}));
        return `Error: ${errData.error || 'Terjadi kesalahan pada server.'}`;
      }
      const data = await res.json();
      return data.reply || 'Maaf, saya tidak menerima respons dari AI.';
    } catch (err) {
      console.error('Gemini fetch error', err);
      return 'Maaf, gagal menghubungi asisten pendakian. Silakan coba lagi.';
    }
  };

  // Handle form submission, send user message and display bot reply
  const handleChatSubmit = async (e) => {
    if (e) e.preventDefault();
    if (isFetchingResponse) return;
    const input = document.getElementById('chat-input');
    const text = input.value.trim();
    if (!text) return;
    
    isFetchingResponse = true;
    addMessage(text, true);
    input.value = '';
    
    setFormState(true);
    showTypingIndicator();
    
    const reply = await fetchGeminiResponse(text);
    
    removeTypingIndicator();
    setFormState(false);
    isFetchingResponse = false;
    
    addMessage(reply, false);
  };

  const sendQuickPrompt = async (text) => {
    if (isFetchingResponse) return;
    
    isFetchingResponse = true;
    addMessage(text, true);
    
    setFormState(true);
    showTypingIndicator();
    
    const reply = await fetchGeminiResponse(text);
    
    removeTypingIndicator();
    setFormState(false);
    isFetchingResponse = false;
    
    addMessage(reply, false);
  };

  const clearChatHistory = () => {
    if (confirm('Apakah Anda yakin ingin menghapus semua riwayat chat?')) {
      localStorage.removeItem('chatHistory');
      const container = document.getElementById('chat-container');
      container.innerHTML = `
        <div class="flex gap-3"><div class="w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center"><span class="material-symbols-outlined text-sm">smart_toy</span></div><div class="max-w-[75%] rounded-2xl rounded-tl-sm bg-surface-container-high px-5 py-4"><p class="font-semibold">Halo ${userName}! Mau merencanakan pendakian kemana?</p><p class="text-sm text-on-surface-variant mt-1">Saya bisa bantu cek jalur, checklist gear, dan alur simaksi.</p></div></div>
      `;
    }
  };

</script>
<?php include __DIR__.'/../includes/layout_bottom.php'; include __DIR__.'/../includes/footer.php'; ?>
