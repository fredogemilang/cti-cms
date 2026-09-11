
let checkMarkSVG = `icons/check-mark.svg`;
let crossMarkSVG = `icons/cross-mark.svg`;
let paperPlaneSVG = `icons/paper-plane.svg`;
let timesSVG = `icons/times.svg`;
let attachChatSVG = `attach-chat.svg`;
let emoticonSVG = `emoticon-chat.svg`;
let sendChatSVG = `send-chat.svg`;
let bubbleChatSVG = `bubble-chat.svg`;
let closeChatSVG = `close-chat.svg`;
let wavesSVG = `waves.svg`;

let whatsAppButtonTitle = '';
let whatsAppFormLabel = {};
let whatsAppFormTexts = {
  'en' : {
    'form_button': 'Contact via WhatsApp',
    'title': 'Contact via WhatsApp',
    'name': 'Full name',
    'email': 'E-mail',
    'whatsapp_number': 'WhatsApp number',
    'checkbox_policy': 'By ticking this box, I agree that my personal information will be given to Central Data Technology (CDT)',
    'privacy_policy': "By submitting your personal data in the required fields above, <span class='cdt_name_text'>PT Central Data Technology</span> and its affiliates collect and proceed with such data. To learn more about our privacy practices, please refer to: <a href='https://www.centraldatatech.com/privacy-policy' target='_blank'><span class='cdt_name_text'>PT Central Data Technology's</span> privacy policy</a>",
    'success_title': "Thank you!",
    'success_message': "Your form has been successfully submitted. Our team will contact you on working days Monday to Friday (08:30 – 17:30 WIB). In the meantime, feel free to continue the conversation with Tedy.",
    'error_title': 'Error!',
    'error_message': "A connection error occured. Please try again.",
    'send_button': 'Submit',
    'greeting': 'Hi 👋 How can I help you?',
    'message_input': "Enter your message...",
    'send_chat_error': `Our chatbot is currently experiencing a technical problem. We apologize for the inconvenience. For assistance, please contact us at <a href="mailto:marketing@centraldatatech.com">marketing@centraldatatech.com</a>`
  },
  'id': {
    'form_button': 'Hubungi via WhatsApp',
    'title': 'Hubungi via WhatsApp',
    'name': 'Nama lengkap',
    'email': 'Alamat email',
    'whatsapp_number': 'Nomor WhatsApp',
    'checkbox_policy': 'Dengan mencentang kotak ini, saya setuju bahwa informasi pribadi saya akan diberikan kepada Central Data Technology (CDT)',
    'privacy_policy': "Dengan mengisi data pribadi Anda pada kolom di atas, <span class='cdt_name_text'>PT Central Data Technology</span> dan afiliasinya akan mengumpulkan dan memroses data tersebut. Untuk mengetahui lebih lanjut tentang kebijakan privasi kami, silahkan kunjungi: <a href='https://www.centraldatatech.com/id/privacy-policy' target='_blank'>Kebijakan Privasi <span class='cdt_name_text'>PT Central Data Technology</span></a>",
    'success_title': "Terima kasih!",
    'success_message': "Formulir Anda telah berhasil dikirim. Tim kami akan menghubungi Anda pada hari kerja Senin - Jumat (08.30 – 17.30 WIB). Sambil menunggu, silakan lanjutkan percakapan dengan Tedy.",
    'error_title': "Gagal!",
    'error_message': "Terjadi kesalahan. Silakan coba lagi.",
    'send_button': 'Kirim',
    'greeting': 'Hi 👋 How can I help you?',
    'message_input': "Enter your message...",
    'send_chat_error': `Chatbot sedang mengalami gangguan teknis. Mohon maaf atas ketidaknyamanannya. Untuk bantuan, mohon hubungi kami di <a href="mailto:marketing@centraldatatech.com">marketing@centraldatatech.com</a>`
  }
};


// Fungsi untuk memformat timestamp
function formatTimestamp(date) {
  const hours = String(date.getHours()).padStart(2, '0'); // Jam (2 digit)
  const minutes = String(date.getMinutes()).padStart(2, '0'); // Menit (2 digit)
  return `${hours}:${minutes}`; // Format: HH:MM
}


 // Fungsi untuk mengonversi teks menjadi hyperlink
 function convertTextToHyperlinks(text) {
  // Escape HTML
  var html = text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');

  // Markdown table
  html = html.replace(/^(\|.+\|)\n(\|[\s\-:|]+\|)\n((\|.+\|\n?)+)/gm, function(match) {
    var rows = match.trim().split('\n');
    var isId = /[a-zA-Z]/.test(match) && (/alamat|nama|judul|tanggal|lokasi|posisi|deskripsi|informasi/i.test(match) || /artikel|hubungi|lihat/i.test(match));
    var hint = isId ? 'Geser tabel untuk melihat info lengkap →' : 'Scroll table to see full info →';
    var table = '<div class="table-hint" style="font-size:11px;color:#cc0000;margin-bottom:2px;font-style:italic;">' + hint + '</div>';
    table += '<div class="table-scroll-wrapper" style="overflow-x:auto;margin:0 0 8px 0;padding-bottom:8px;position:relative;">';
    table += '<table style="border-collapse:collapse;width:max-content;min-width:100%;font-size:13px;">';
    for (var i = 0; i < rows.length; i++) {
      if (i === 1) continue;
      var cells = rows[i].split('|').filter(function(c) { return c.trim() !== ''; });
      var tag = i === 0 ? 'th' : 'td';
      var s = 'style="border:1px solid #ddd;padding:6px 8px;text-align:left;white-space:nowrap;"';
      table += '<tr>' + cells.map(function(c) { return '<' + tag + ' ' + s + '>' + c.trim() + '</' + tag + '>'; }).join('') + '</tr>';
    }
    table += '</table></div>';
    return table;
  });

  // Bold **text**
  html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
  // Italic *text*
  html = html.replace(/\*([^*]+)\*/g, '<em>$1</em>');
  // Markdown links [text](url)
  html = html.replace(/\[([^\]]+)\]\(((?:https?:\/\/|mailto:)[^\s)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer" style="color:#007bff;text-decoration:underline;">$1</a>');
  // Plain URLs
  html = html.replace(/(^|[^"'])(https?:\/\/[^\s<]+)/g, '$1<a href="$2" target="_blank" rel="noopener noreferrer" style="color:#007bff;text-decoration:underline;">$2</a>');
  // Newlines
  html = html.replace(/\n/g, '<br>');

  return html;
}

function uuidv4() {
    return "10000000-1000-4000-8000-100000000000".replace(/[018]/g, c =>
      (+c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> +c / 4).toString(16)
    );
  }

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(";").shift();
}

function setDynamicBackground() {
  const chatHeader = document.querySelector(".chat-header");
  if (chatHeader && window.chatBackgroundUrl) {
      chatHeader.style.background = `url(${window.chatBackgroundUrl}) no-repeat center center`;
      chatHeader.style.backgroundSize = "cover";
  }
}

// Fungsi untuk menampilkan efek mengetik
function typeText(element, text, speed = 50) {
  let index = 0;
  return new Promise((resolve) => {
    const type = () => {
      if (index < text.length) {
        element.textContent += text[index];
        index++;

        // Jangan memicu scroll otomatis selama mengetik
        requestIdleCallback(() => type(), { timeout: speed });
      } else {
        resolve();
      }
    };
    type();
  });
}

// Fungsi untuk memeriksa apakah chat berada di posisi paling bawah (atau mendekati)
function isNearBottom() {
  const chatBody = document.getElementById("chat-body");
  const threshold = 50; // Jarak dari bawah dalam piksel
  return chatBody.scrollTop + chatBody.clientHeight >= chatBody.scrollHeight - threshold;
}

// Fungsi untuk menambahkan pesan ke chat
function addMessage(content, sender = "bot", timestamp = null, isAborted = false) {
  const chatBody = document.getElementById("chat-body");
  const message = document.createElement("div");
  message.classList.add("message", sender);

  const bubble = document.createElement("div");
  bubble.classList.add("bubble");

  if (isAborted) {
      bubble.innerHTML = whatsAppFormLabel.send_chat_error;
      message.classList.add("aborted-message");
  } else if (/https?:\/\/citra\.contact/i.test(content)) {
      const waButton = document.createElement("button");
      waButton.textContent = "Hubungi Saya via WA";
      waButton.classList.add("wa-button");
      waButton.addEventListener("click", () => {
          showWAForm();
      });
      bubble.appendChild(waButton);
  } else {
      bubble.innerHTML = convertTextToHyperlinks(content);
  }

  if (timestamp) {
      const timestampElement = document.createElement("span");
      timestampElement.classList.add("timestamp");
      timestampElement.textContent = formatTimestamp(timestamp);
      message.appendChild(timestampElement);
  }

  message.appendChild(bubble);
  chatBody.appendChild(message);
  chatBody.scrollTop = chatBody.scrollHeight;
}

function showWAForm() {
  // 1. Sembunyikan chat widget
  const chatWidget = document.getElementById("chat-widget");
  chatWidget.classList.remove("active"); // Hapus kelas 'active' untuk menyembunyikan chat widget

  // 2. Buat popup form
  const popupForm = document.createElement("div");
  popupForm.id = "wa-form-popup";
  popupForm.innerHTML = `
      <div class="d-flex justify-content-center align-items-center w-100">
        <div class="wa-form-container">
            <button id="close-wa-form" class="close-button">${timesSVG}</button>
            <form id="wa-form">
              <h3>${whatsAppFormLabel.title}</h3>
              <div class="dot-separator">
                <span class="dot small"></span>
                <span class="dot large"></span>
                <span class="dot small"></span>
              </div>
                <div id="wa-form-alert">
                </div>
                <div id="form-group-wrapper">
                  <div class="form-group">
                      <input type="text" id="full_name" name="full_name" oninput="setInputLabel(this)" required>
                      <label for="full_name">${whatsAppFormLabel.name}</label>
                  </div>
                  <div class="form-group">
                      <input type="email" id="email" name="email" oninput="setInputLabel(this)" required >
                      <label for="email">${whatsAppFormLabel.email}</label>
                  </div>
                  <div class="form-group">
                      <input type="tel" id="phone_number" name="phone_number" oninput="setInputLabel(this)" required >
                      <label for="phone_number">${whatsAppFormLabel.whatsapp_number}</label>
                  </div>
                  <div class="form-checkbox">
                      <input type="checkbox" id="data_agreement" name="data_agreement" required >
                      <label for="data_agreement">${whatsAppFormLabel.checkbox_policy}</label>
                  </div>
                  <div class="form-privacy-policy">
                      <p>${whatsAppFormLabel.privacy_policy}</p>
                  </div>
                  <div class="button-group">
                      <button type="submit" class="w-100" id="submit-wa-form">${paperPlaneSVG}<span>${whatsAppFormLabel.send_button}</span></button>
                  </div>
                </div>
            </form>
        </div>
      </div>
  `;

  document.body.appendChild(popupForm);

  // Tampilkan popup form
  popupForm.classList.add("show");

  // Event listener untuk submit form
  const waForm = document.getElementById("wa-form");
  waForm.addEventListener("submit", (e) => {
      e.preventDefault(); // Mencegah reload halaman
      submitWAForm();
  });

  // Event listener untuk tombol close
  const closeButton = document.getElementById("close-wa-form");
  closeButton.addEventListener("click", (e) => {
    e.preventDefault();
    closeWAForm();
  });
}

function setInputLabel(elm) {
  if (elm.value=="") {
    elm.classList.remove('active')
  } else {
    elm.classList.add('active')
  }
}

function closeWAForm() {
  
  // 1. Hapus popup form dari DOM
  const popupForm = document.getElementById("wa-form-popup");
  if (popupForm) {
    popupForm.remove();
  }

  // 2. Tampilkan kembali chat widget
  const chatWidget = document.getElementById('chat-widget');
  chatWidget.classList.add("active");
  return
}

async function submitWAForm() {
  const full_name = document.getElementById("full_name").value;
  const email = document.getElementById("email").value;
  const phone_number = document.getElementById("phone_number").value;

  // if (!full_name || !email || !phone_number) {
  //     alert("Harap isi semua field!");
  //     return;
  // }
  const scriptParams = getParameters();
  const apiUrl = scriptParams.apiUrl;
  const embedId = scriptParams.embedId;

  let sesId = localStorage.getItem(`allm_${embedId}_session_id`);

  const submitButton = document.getElementById("submit-wa-form");
  const closeButton = document.getElementById("close-wa-form")
  const formGroupWrapper = document.getElementById("form-group-wrapper");
  formGroupWrapper.className = ""
  setWAFormAlert("", "")

  try {
    submitButton.disabled = true;
    closeButton.disabled = true;

    const response = await fetch(`${apiUrl}/embed/${embedId}/${sesId}/submit-contact`, { // Ganti dengan URL API Anda
      method: 'POST',
      headers: {
          'Content-Type': 'application/json'
      },
      body: JSON.stringify({ full_name, email, phone_number })
    });

    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    formGroupWrapper.className = "hidden"

    setWAFormAlert(whatsAppFormLabel.success_message, 'success');

    setTimeout(closeWAForm, 5000)

  } catch (error) {
    setWAFormAlert(whatsAppFormLabel.error_message, 'error');
  } finally {
    submitButton.disabled = false;
    closeButton.disabled = false;
  }
}

function setWAFormAlert(message, type) {
  const alertDiv = document.getElementById("wa-form-alert");

  let title = whatsAppFormLabel.success_title;
  let icon = checkMarkSVG;

  if (type=='error') {
    icon = crossMarkSVG
    title = whatsAppFormLabel.error_title;
  }

  alertDiv.innerHTML = "<div class='d-flex align-items-center'>"+icon +"</div>"
    +"<div>"
    +"<div id='wa-form-alert-title'>"+ title +"</div>"
    + "<div id='wa-form-alert-message'>"+ message +"</div>"
    + "</div>";

  // reset CSS background
  alertDiv.className = type;
}

function getParameters() {
  const scriptParams = document.querySelector('script[data-name="teddy_widget"]')
  const avatarImageUrl = scriptParams.getAttribute('data-avatar-url') || "-";
  const avatarName = "TEDY";
  const apiUrl = scriptParams.getAttribute('data-base-api-url') || "-";
  const embedId = scriptParams.getAttribute('data-embed-id') || "-";
  const bottomChatButton = parseInt(scriptParams.getAttribute('data-widget-bottom')) || 120;
  const rightChatButton = parseInt(scriptParams.getAttribute('data-widget-right')) || 20;
  const buttonSize = parseInt(scriptParams.getAttribute('data-button-size')) || 60;
  let appUrl = scriptParams.getAttribute('src');
  appUrl = appUrl.replace(/[^/]+\.js$/, '');

  return {
    avatarImageUrl: avatarImageUrl,
    avatarName: avatarName,
    apiUrl: apiUrl,
    embedId: embedId,
    appUrl: appUrl,
    bottomChatButton: bottomChatButton,
    rightChatButton: rightChatButton,
    buttonSize: buttonSize
  }

}

function initImages(appUrl) {
  checkMarkSVG = `<img src="${appUrl}${checkMarkSVG}">`;
  crossMarkSVG = `<img src="${appUrl}${crossMarkSVG}">`;
  paperPlaneSVG = `<img src="${appUrl}${paperPlaneSVG}">`;
  sendChatSVG = `<img src="${appUrl}${sendChatSVG}">`;
  timesSVG = `<img src="${appUrl}${timesSVG}">`;
  attachChatSVG = `<img src="${appUrl}${attachChatSVG}">`;
  emoticonSVG = `<img src="${appUrl}${emoticonSVG}">`;
  bubbleChatSVG = `<img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg'%3E%3C/svg%3E">`;
  closeChatSVG = `<img src="${appUrl}${closeChatSVG}">`;
  wavesSVG = `url("${appUrl}${wavesSVG}")`;
} 

const observer = new MutationObserver(function (mutations) {
  const openIcon = document.querySelector(".open-icon");
  const closeIcon = document.querySelector(".close-icon");
  mutations.forEach(function (mutation) {

    if (mutation.target.classList.contains('active')) {
      openIcon.style.visibility = "hidden";
      openIcon.style.opacity = 0;
      openIcon.style.transform = "translateX(0)";

      closeIcon.style.visibility = "visible";
      closeIcon.style.opacity = 1;
      closeIcon.style.transform = "translateX(-12px)";
    } else {
      openIcon.style.visibility = "visible";
      openIcon.style.opacity = 1;
      openIcon.style.transform = "translateX(12px)";

      closeIcon.style.visibility = "hidden";
      closeIcon.style.opacity = 0;
      closeIcon.style.transform = "translateX(0px)";
    }
  });
});

(function () {
  const settingParams = getParameters();
  initImages(settingParams.appUrl);

  const pageLanguage = document.documentElement.lang;

  if (pageLanguage == 'id-ID' || pageLanguage == 'id' || pageLanguage == 'in') {
    whatsAppFormLabel = whatsAppFormTexts.id;
  } else {
    whatsAppFormLabel = whatsAppFormTexts.en;
  }
  whatsAppButtonTitle = whatsAppFormLabel.form_button;

  const initWidgetOnReady = function() {

    const chatWidgetEl = document.querySelector('#chat-widget');
    if (chatWidgetEl) {
      observer.observe(chatWidgetEl, {
        attributes: true,
        attributeFilter: ['class']
      });
    }

    const openIcon = document.querySelector("#chat-button .open-icon");
    const closeIcon = document.querySelector("#chat-button .close-icon");

    if (openIcon) {
      openIcon.innerHTML = bubbleChatSVG;
    }

    if (closeIcon) {
      closeIcon.innerHTML = closeChatSVG;
      closeIcon.style.visibility = "hidden";
      closeIcon.style.opacity = 0;
      closeIcon.style.transform = "translateX(0px)";
    }

    const sendButton = document.querySelector(".send-button"); 
    if (sendButton) {
      sendButton.innerHTML = sendChatSVG; 
    }

    const attachButton = document.querySelector("#attach-file-btn");
    const emoticonButton = document.querySelector("#emoticon-btn");

    if (attachButton) {
      attachButton.innerHTML = attachChatSVG; 
    }

    if (emoticonButton) {
      emoticonButton.innerHTML = emoticonSVG; 
    }

    const chatHeader = document.querySelector(".chat-header");
    if (chatHeader) {
      chatHeader.style.background = `${wavesSVG} no-repeat center center`;
      chatHeader.style.backgroundSize = "cover";
    }
  };

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initWidgetOnReady);
  } else {
    initWidgetOnReady();
  }

  // Mengambil variabel yang didefinisikan di luar (misalnya di dalam tag <script>)

  const scriptParams = getParameters();
  const avatarImageUrl = scriptParams.avatarImageUrl;
  const avatarName = scriptParams.avatarName;
  const apiUrl = scriptParams.apiUrl;
  const embedId = scriptParams.embedId;
  
  const buttonSize = scriptParams.buttonSize;
  const bottomPosition = scriptParams.bottomChatButton;
  const rightPosition = scriptParams.rightChatButton;
  const rightWidget = rightPosition;
  const bottomWidget = bottomPosition + buttonSize + 20;

  // Menambahkan CSS secara dinamis ke dalam halaman
  const style = document.createElement("style");
  style.innerHTML = `
     
    .chat-widget {
      position: fixed;
      bottom: ${bottomWidget}px;
      right: ${rightWidget}px;
      width: 380px;
      font-family: Arial, sans-serif;
      border-radius: 10px;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      border: 1px solid #ccc;
      background-color: #fff;
      opacity: 0;
      transform: translateY(50px);
      transition: opacity 0.4s ease, transform 0.4s ease;
      z-index: 1000;
      pointer-events: none;
    }
  
    .chat-widget.active {
      opacity: 1;
      transform: translateY(0);
      pointer-events: auto;
    }
  
    .chat-widget.center {
      right: 50%;
      transform: translate(50%, 0);
    }
  
    @media (max-width: 768px) {
      .chat-widget {
        width: 90%;
        bottom: ${bottomWidget}px;
        right: 50%;
        transform: translate(50%, 50px);
      }
      .chat-widget.active {
        transform: translate(50%, 0);
      }
      
      .wa-form-container {
          background-color: #fff;
          padding: 20px;
          width: 100%; /* Sesuaikan lebar sesuai kebutuhan */
      }
    }
  
    #emoticon-btn {
      width: auto;
      padding: 0 8px 0 0;
      margin: 0;
    }
  
    .chat-header {
     
      background-size: cover;
      padding: 15px 20px 60px 15px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
  
    .user-info {
        display: flex;
        align-items: center;
        gap: 8px;  /* Memberikan jarak antara avatar dan teks */
    }

    .user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
    }

    .user-names {
        display: flex;
        flex-direction: column;
        text-align: left;
    }

    .short-name {
        font-size: 20px;
        font-weight: bold;
        color: #fff;
    }

    .full-name {
        font-size: 11px; /* Mengurangi ukuran font */
        color: #fff;
        font-style: italic; /* Membuat teks italic */
    }
  
    .chat-header img {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      margin-right: 10px;
    }
    
  
    .chat-body {
      background-color: #fff;
      padding: 10px;
      max-height: 300px;
      min-height: 300px;
      overflow-y: auto;
    }
  
    .chat-body .message {
      margin-bottom: 10px;
    }
  
    .chat-body .message.user {
      text-align: right;
    }
  
    .chat-body .message .bubble {
      display: inline-block;
      padding: 8px 12px;
      border-radius: 15px;
      max-width: 70%;
    }
  
    .chat-body .message.bot .bubble {
      background-color: #f1f1f1;
      color: #333;
      border-radius: 20px;
      margin-bottom: 10px;
      min-width: 80%;
      max-width: 90%; /* Membatasi lebar maksimum bubble */
      word-wrap: break-word; /* Membungkus kata panjang */
      overflow-wrap: break-word; /* Mendukung pemotongan otomatis */
      word-break: break-word; /* Memotong kata panjang */
      white-space: pre-wrap; /* Memastikan teks multi-baris tetap rapi */
      overflow: hidden; /* Mengatasi masalah overflow */
    }
  
    .chat-body .message.user .bubble {
      background: linear-gradient(to right, #dd0204, #014af7);;
      color: #fff;
      border-radius: 20px;
      text-align: left;
    }
  
    .chat-footer {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      border-top: 1px solid #ccc;
      margin: 10px;
      padding: 10px;
      background-color: #fff;
    }
  
    .chat-footer .row {
      display: flex;
      width: 100%;
      justify-content: space-between;
    }
  
  
    .chat-footer input {
      width: 80%;
      padding: 5px;
      border: none;
      border-radius: 5px;
      margin-bottom: 5px; 
    }
  
    .chat-footer input:focus {
      outline: none;
      box-shadow: none;
      border: none;
    }
  
    .chat-footer button {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 8px;
      background: linear-gradient(to right, #dd0204, #014af7);
      width: 50px;
      height: 50px;
      border-radius: 50%;
    }
  
    .icon-buttons {
      display: flex;
      justify-content: flex-start; /* Menyusun ikon ke kanan */
      width: 100%;
    }
  
    .chat-footer .icon-button {
      background: none;
      border: none;
      padding: 5px;
      margin: 5px;
      cursor: pointer;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 10px;
    }
  
    .chat-footer .icon-button .icon {
      width: 20px;
      height: 20px;
    }
  
    .icon-button {
      background: none;
      border: none;
      padding: 5px;
      cursor: pointer;
      display: flex;
      justify-content: center;
      align-items: center;
      margin-left: 10px; /* Jarak antar ikon */
    }
  
    #chat-button {
      position: fixed;
      bottom: ${bottomPosition}px;
      right: ${rightPosition}px;
      background-color: #1c8ef9;
      color: white;
      width: ${buttonSize}px;
      height: ${buttonSize}px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      border: none;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
      cursor: pointer;
      transform: scale(1);
      transition: transform 0.2s ease, background-color 0.2s ease;
      z-index: 1001;
      padding: 0;
    }
  
    #chat-button:hover {
      transform: scale(1.1);
      background-color: #155baf;
    }
  
    .chat-widget.active + .chat-button {
      background-color: #923dff;
    }
  
    .chat-widget.active +  {
      display: none;
    }
  
    .chat-widget.active +  {
      display: block;
    }

    #chat-button div.close-icon {
      transform: translateX(0);
      transition: 0.3s ease-in-out;
    }

    #chat-button div.open-icon {
      transform: translateX(12px);
      transition: 0.3s ease-in-out;
    }

    #chat-button img {
      vertical-align: middle;
      width: 100%;
      max-width: 100%;
    }

    .chat-widget.active {
      pointer-events: auto !important;
    }

    .chat-widget.active * {
      pointer-events: auto !important;
    }
  
    @keyframes typing {
        0% { opacity: 0.2; }
        33% { opacity: 0.5; }
        66% { opacity: 0.8; }
        100% { opacity: 1; }
    }
    
    .typing-bubble {
      display: flex;
      align-items: center;
      justify-content: flex-start; 
      gap: 5px; 
      margin-left: 10px; 
    }
  
    .typing-bubble span {
      display: inline-block;
      width: 8px;
      height: 8px;
      background-color: #ccc;
      border-radius: 50%;
      animation: wave 1.2s infinite;
    }
  
    .typing-bubble span:nth-child(1) {
      animation-delay: 0s;
    }
  
    .typing-bubble span:nth-child(2) {
      animation-delay: 0.2s;
    }
  
    .typing-bubble span:nth-child(3) {
      animation-delay: 0.4s;
    }
  
    /* Kurangi tinggi animasi */
    @keyframes wave {
      0%, 100% {
        transform: translateY(0);
        opacity: 0.3;
      }
      50% {
        transform: translateY(-4px); /* Sebelumnya 10px */
        opacity: 1;
      }
    }
  
  
   
    .status-indicator {
    width: 15px;
    height: 15px;
    background-color: #28a745;
    border-radius: 50%;
    display: inline-block;
    border: 2px solid #fff; 
    }
    .emoticon-picker {
      position: absolute;
      bottom: 60px;
      /* right: 20px; */
      background: #fff;
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 10px;
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
      z-index: 1001;
      display: none;
    }
  
    .emoticon-item {
      font-size: 24px;
      cursor: pointer;
      transition: transform 0.2s ease;
    }
  
    .emoticon-item:hover {
      transform: scale(1.2);
    }
  
    .emoticon-picker.active {
      display: flex;
    }
    .hidden {
      display: none;
    }
  
    .emoticon-picker {
      display: none;
      position: absolute;
      bottom: 100px;
      
      background: white;
      border-radius: 10px;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
      padding: 10px;
      z-index: 1002;
      max-height: 150px;
      overflow-y: auto;
    }
  
    .emoticon-picker.show {
      display: block;
    }
  
    .emoticon-item {
      font-size: 24px;
      margin: 5px;
      cursor: pointer;
    }

   .bubble a {
        color: #007bff; /* Warna biru untuk link */
        text-decoration: underline; /* Garis bawah untuk link */
        cursor: pointer; /* Ubah kursor menjadi pointer saat diarahkan ke link */
    }

    .bubble a:hover {
        color: #0056b3; /* Warna biru lebih gelap saat hover */
        text-decoration: none; /* Hilangkan garis bawah saat hover */
    }
    
   .message {
    position: relative; /* Container pesan */
    margin-bottom: 20px; /* Beri jarak antar pesan */
  }

    /* Bubble untuk pesan user dan bot */
    .bubble {
        padding: 8px 12px;
        border-radius: 15px;
        max-width: 70%;
        word-wrap: break-word;
        position: relative; /* Bubble sebagai referensi posisi */
    }
    
    .aborted-message {
      color: red;
      font-weight: bold;
    }

    .chat-body .message.bot.aborted-message .bubble {
      color: red;
      font-weight: bold;
      font-size: 12px;
    }

    .message.user .timestamp {
    display: block;
    font-size: 10px;
    color: #888;
    text-align: right;
    margin-top: 4px;
    position: absolute; /* Posisi absolut di dalam container pesan */
    bottom: -15px; /* Jarak dari bawah bubble */
    right: 5px; /* Posisi di kanan */
}

/* Timestamp untuk pesan bot */
.message.bot .timestamp {
    display: block;
    font-size: 10px;
    color: #888;
    text-align: left; /* Posisi di kiri untuk bot */
    margin-top: 4px;
    position: absolute; /* Posisi absolut di dalam container pesan */
    bottom: -3px; /* Jarak dari bawah bubble */
    left: 5px; /* Posisi di kiri */
}
 .abort-message {
  font-size: 0.85rem;
  color: red;
  margin-top: 4px; /* Beri jarak dengan bubble */
  display: block; /* Tampilkan di bawah bubble */
  text-align: right; /* Sejajarkan dengan timestamp */
}
  .wa-button {
  background: #25d366; /* Warna hijau WhatsApp */
  color: #fff;
  border: none;
  padding: 8px 12px;
  border-radius: 8px;
  cursor: pointer;
  margin-top: 10px;
  font-size: 14px;
}

.wa-button:hover {
  background: #128c7e; /* Warna hijau lebih gelap saat hover */
}

#wa-form-popup.show {
    display: flex;
}

#wa-form-popup {
    display: none; /* Awalnya disembunyikan */
    border-radius: 10px;
    position: fixed; /* atau absolute, sesuai kebutuhan */
    background-color: rgba(0, 0, 0, 0.5);
    border: 1px solid #ccc;
    padding: 20px;
    z-index: 1001; /* Pastikan di atas chat widget */
    height: 538px;
    width: 380px;
    bottom: ${bottomWidget}px;
    right: ${rightWidget}px;
}

.d-flex {
  display: flex !important;
}

.justify-content-center{
  justify-content: center !important;
}

.align-items-center{
  align-items: center !important;
}

.w-100{
  width: 100% !important;
}

.wa-form-container {
    background-color: #fff;
    border-radius: 10px;
    padding: 1rem;
    width: 100%;
    position: relative;
}

.wa-form-container h3 {
  text-align: left;
  font-weight: bold;
  font-size: 1.25rem;
  padding-bottom: 1.15rem;
}

  #close-wa-form:disabled {
    opacity: 0.5;
    text-decoration: none;
    cursor: default;
  }
  
  #wa-form button:disabled {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
    opacity: 0.65;
    text-decoration: none;
  }

  #wa-form .dot-separator {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 1rem 0;
    display:none;
  }

  #wa-form .dot {
    display: inline-block;
    border-radius: 50%;
    background-color: #C4CADA;
  }

  #wa-form .dot.small {
    width: 6px;
    height: 6px;
  }

  #wa-form .dot.large {
    width: 10px;
    height: 10px;
  }

  #wa-form-alert {
    margin-bottom: 1.5rem;
    padding: 0.75rem 1.25rem;
    border-radius:8px;
    display:none;
    font-size: 0.875rem;
    gap: 1rem;
  }

  #wa-form-alert.error {
    color: #f8285a;
    background-color: #ffeef3;
    border: 1px solid #fca9bd;
    display: flex;
  }

  #wa-form-alert.success {
    color: #17c653;
    background-color: #dfffea;
    border: 1px solid #a2e8ba;
    display: flex;
  }
  
  #wa-form-alert-title {
    font-weight: 600;
  }

  #wa-form-alert-message {
    font-size: 0.75rem;
  }

  .form-group {
      margin-bottom: 15px;
      position:relative;
  }

  .form-group label {
    position: absolute;
    top: 0;
    max-width: 90%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    left: .75rem;
    padding-top: .75rem;
    pointer-events: none;
    transform-origin: 0 0;
    transition: all .2s ease-out;
    margin-bottom: 0;  
    color: #252F4A;
  }

  .form-group input:focus~label, .form-group input.active~label {
    transform: translateY(-0.75rem) translateY(0.1rem) scale(0.8);
    background-color: #fff;
    padding: 0 0.25rem 0.15rem 0.25rem;
  }

  .form-group input:focus~label {
    color: #1B84FF !important;
  }

  .form-group input {
  max-width: -webkit-fill-available;
  max-width: -moz-available;  
      display: block;
      width: 100%;
      padding: .875rem 1rem;
      font-size: 0.875rem;
      font-weight: 400;
      line-height: 1.5;
      color: #000;
      appearance: none;
      background-color: #fff;
      background-clip: padding-box;
      border: 1px solid #DBDFE9;
      border-radius: .475rem;
      box-shadow: false;
      transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
  }

  .form-group input:focus {
    border-color:  #1B84FF;
  }

  .form-checkbox {
    justify-content: space-evenly;
    display: flex;
    align-content: stretch;
    margin-bottom: 15px;
    font-size: 10px;
  }

  .form-checkbox label {
    margin-left: 5px;
  }

  .form-privacy-policy {
    margin-bottom: 15px;
    font-size: 12px;
  }

  .form-privacy-policy a {
    color: #cf3932;
    font-weight: bold;
  }

  .form-privacy-policy .cdt_name_text {
    color: black;
    font-weight: bold;
  }

  .button-group {
      text-align: right;
  }

  .button-group button {
      display: inline-flex;
      gap: 0.5rem;
      padding: 0.875rem 1rem;
      font-size: 0.875rem;
      font-weight: 500;
      line-height: 1.5;
      text-align: center;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      user-select: none;
      border: 0;
      border-radius: 0.5rem;
      box-shadow: none;
      transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
  }

  .button-group button[type="submit"] {
      background-color: #1B84FF;
      color: #FFFFFF;
  }

  .button-group button[type="button"] {
      background-color: #ccc;
      color: #333;
  }

  .close-button {
      position: absolute;
      top: 0;
      right: 0;
      background: none;
      border: none;
      font-size: 20px;
      cursor: pointer;
      color: #888;
      transform: translate(25%, -25%);
  }

  .close-button:hover {
      color: #333;
  }

  INPUT:-webkit-autofill, SELECT:-webkit-autofill, TEXTAREA:-webkit-autofill {
    animation-name: onautofillstart;
    -webkit-background-clip: text;
    box-shadow: inset 0 0 20px 20px var(--mdb-body-bg);
    -webkit-box-shadow: 0 0 20px 20px var(--mdb-body-bg) inset !important;
  }
      
    .table-scroll-wrapper::-webkit-scrollbar {
      height: 6px;
    }
    .table-scroll-wrapper::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 3px;
    }
    .table-scroll-wrapper::-webkit-scrollbar-thumb {
      background: #cc0000;
      border-radius: 3px;
    }
    .table-scroll-wrapper {
      scrollbar-color: #cc0000 #f1f1f1;
      scrollbar-width: thin;
    }
    `;
  document.head.appendChild(style);

  // Menambahkan HTML widget ke halaman
  const chatWidgetHTML = `
      <button class="chat-button" id="chat-button">
        <div src="" alt="Open Chat" class="open-icon"></div>
        <div src="" alt="Close" class="close-icon"></div>
      </button>
  
      <div class="chat-widget" id="chat-widget">
        <div class="chat-header">
          <div class="user-info">
              <img src="${avatarImageUrl}" alt="Avatar" class="user-avatar">
              <div class="user-names">
                  <span class="short-name">${avatarName}</span>
                  <span class="full-name"></span>
              </div>
          </div>
          <div class="status-indicator"></div>
        </div>
        <div class="chat-body" id="chat-body">
          <div class="message bot">
            <div class="bubble">${whatsAppFormLabel.greeting}</div>
          </div>
        </div>
  
        <div class="chat-footer">
          <div class="d-flex w-100 align-items-center">
            <button id="emoticon-btn" class="icon-button">
              <img src="" alt="Emoticon" class="icon"> 
            </button>
            <input type="text" id="user-input" placeholder="${whatsAppFormLabel.message_input}">
            <button id="send-button" class="send-button" aria-label="Send Message"></button>
          </div>
          <div class="icon-buttons">
            
          </div>
        </div>
        <div class="emoticon-picker" id="emoticon-picker"></div>
      </div>
    `;
  document.body.insertAdjacentHTML("beforeend", chatWidgetHTML);

  // Fungsi untuk menangani pilihan pengguna


  // Fungsi untuk menambahkan pesan baru ke dalam chat




  // Fungsi untuk menampilkan bubble typing
    function showTypingBubble() {
      const typingBubble = document.createElement("div");
      typingBubble.classList.add("message", "bot", "typing-bubble");

      typingBubble.innerHTML = `
          <span></span>
          <span></span>
          <span></span>
      `;

    const chatBody = document.getElementById("chat-body");
    chatBody.appendChild(typingBubble);
    chatBody.scrollTop = chatBody.scrollHeight;

    return typingBubble;
  }


  // Fungsi untuk mengirim pesan
  async function sendMessage() {
    const userInput = document.getElementById("user-input");
    const text = userInput.value.trim();

    if (!text) return;

    const userTimestamp = new Date();
    addMessage(text, "user", userTimestamp);
    userInput.value = "";

    let sesId = localStorage.getItem(`allm_${embedId}_session_id`);

    const payload = {
      message: text,
      sessionId: sesId || null,
    };

    const typingBubble = showTypingBubble();

    try {
      const response = await fetch(`${apiUrl}/embed/${embedId}/stream-chat`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "text/event-stream",
        },
        body: JSON.stringify(payload),
        keepalive: true,
      });

      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }

      const reader = response.body.getReader();
      const decoder = new TextDecoder("utf-8");
      let messageBuffer = "";
      let completeMessage = "";
      let botMessageBubble = null;

      while (true) {
        const { done, value } = await reader.read();
        if (done) break;

        const chunk = decoder.decode(value, { stream: true });
        messageBuffer += chunk;

        const lines = messageBuffer.split("\n");
        for (let i = 0; i < lines.length - 1; i++) {
          const line = lines[i].trim();
          if (line.startsWith("data:")) {
            const jsonString = line.replace("data:", "").trim();
            try {
              const jsonResponse = JSON.parse(jsonString);

              if (jsonResponse?.type === "abort") {
                typingBubble.remove();
                addMessage("", "bot", null, true);
                return;
              }

              if (jsonResponse?.textResponse) {
                completeMessage += jsonResponse.textResponse;

                if (!botMessageBubble) {
                  botMessageBubble = document.createElement("div");
                  botMessageBubble.classList.add("message", "bot", "bubble");
                  const botMessage = document.createElement("div");
                  botMessage.classList.add("message", "bot");
                  botMessage.appendChild(botMessageBubble);
                  document.getElementById("chat-body").appendChild(botMessage);
                }

                // Tampilkan pesan tanpa memicu scroll otomatis
                if (document.visibilityState === "visible") {
                  await typeText(botMessageBubble, jsonResponse.textResponse, 50);
                } else {
                  botMessageBubble.textContent += jsonResponse.textResponse;
                }

                // URL regex
                const urlRegex = /https?:\/\/[^\s]+/g;
                const urls = completeMessage.match(urlRegex);

                botMessageBubble.innerHTML = convertTextToHyperlinks(completeMessage);

                // check for whatsApp button trigger
                const toWhatsAppKeyword = "Hubungi via WhatsApp"
                const hasWhatsAppDirective = completeMessage.match(toWhatsAppKeyword)

                if (botMessageBubble && hasWhatsAppDirective ) {
                  const waButton = `<button class="wa-button" onclick="showWAForm()">${whatsAppButtonTitle}</button>`;
                  
                  botMessageBubble.innerHTML = botMessageBubble.innerHTML.replace(toWhatsAppKeyword, waButton);
                }

                typingBubble.remove();

                // Scroll ke bawah hanya jika chat sudah berada di posisi paling bawah
                if (isNearBottom()) {
                  document.getElementById("chat-body").scrollTop = document.getElementById("chat-body").scrollHeight;
                }
              }

              if (jsonResponse?.assistant_timestamp && document.querySelector(".message.bot:last-child .timestamp")==null) {
                const botMessage = document.querySelector(".message.bot:last-child");
                const timestampElement = document.createElement("span");
                timestampElement.classList.add("timestamp");
                const date = new Date(jsonResponse.assistant_timestamp);
                timestampElement.textContent = formatTimestamp(date);
                botMessage.appendChild(timestampElement);
              }
            } catch (err) {
              console.error("Error parsing JSON:", err);
            }
          }
        }
        messageBuffer = lines[lines.length - 1];
      }

    } catch (error) {
      typingBubble.remove();
      addMessage("", "bot", new Date(), true);
    }
  }



  
  

  // Event listener untuk tombol kirim pesan
  const sendButton = document.getElementById("send-button");
  sendButton.addEventListener("click", sendMessage);

  // Fungsi untuk melampirkan file
  // const attachFileBtn = document.getElementById("attach-file-btn");
  // const fileInput = document.getElementById("file-input");

  // attachFileBtn.addEventListener("click", () => {
  //   fileInput.click(); // Menampilkan dialog file upload
  // });

  // fileInput.addEventListener("change", (e) => {
  //   const file = e.target.files[0];
  //   if (file) {
  //     addMessage(`File attached: ${file.name}`, "user");
  //   }
  // });

  // Fungsi untuk memilih emotikon
  const emoticons = [
    "😊",
    "😂",
    "😍",
    "😢",
    "😡",
    "🙌",
    "👏",
    "🎉",
    "👍",
    "👎",
  ];
  const emoticonPicker = document.getElementById("emoticon-picker");
  const userInput = document.getElementById("user-input");
  const emoticonButton = document.getElementById("emoticon-btn");

  function generateEmoticonPicker() {
    if (emoticonPicker.childElementCount === 0) {
      emoticons.forEach((emoticon) => {
        const emoticonElement = document.createElement("span");
        emoticonElement.className = "emoticon-item";
        emoticonElement.textContent = emoticon;
        emoticonElement.addEventListener("click", () => {
          userInput.value += emoticon; // Menambahkan emotikon ke input teks
        });
        emoticonPicker.appendChild(emoticonElement);
      });
    }
  }

  // Menampilkan popup emotikon saat tombol emotikon diklik
  emoticonButton.addEventListener("click", (event) => {
    generateEmoticonPicker();
    emoticonPicker.classList.toggle("show"); // Toggles visibility
    event.stopPropagation(); // Mencegah event ini bubble ke document
  });

  // Menutup popup emotikon saat klik di luar popup
  document.addEventListener("click", (event) => {
    if (
      !emoticonPicker.contains(event.target) &&
      !emoticonButton.contains(event.target)
    ) {
      emoticonPicker.classList.remove("show"); // Menyembunyikan popup emotikon
    }
  });

  // Fungsi untuk membuka dan menutup widget
  const chatButton = document.getElementById("chat-button");
  const chatWidget = document.getElementById("chat-widget");

//   document.getElementById("chat-button").addEventListener("click", function() {
    
// });

chatButton.addEventListener("click", (e) => {
  e.stopPropagation();
  chatWidget.classList.toggle("active");
  
  if (chatWidget.classList.contains("active")) {
    const interactiveElements = chatWidget.querySelectorAll('input, button, textarea, a');
    interactiveElements.forEach(el => {
      el.style.pointerEvents = 'auto';
      el.style.cursor = el.tagName === 'INPUT' ? 'text' : 'pointer';
    });
  }
});

  userInput.addEventListener("keydown", (event) => {
    if (event.key === "Enter" && !event.shiftKey) {
      event.preventDefault(); // Mencegah baris baru saat Enter ditekan
      sendMessage();
    }
  });

  // Tutup widget jika klik di luar area chat
  document.addEventListener("click", (event) => {
    if (
      !chatButton.contains(event.target) &&
      !chatWidget.contains(event.target)
    ) {
      chatWidget.classList.remove("active");
    }
  });

  document.getElementById('chat-button').addEventListener('click', function () {
    // Cek apakah ini pertama kali
    const key_sesId = `allm_${embedId}_session_id`
    const key_time = `allm_time`
    if (!localStorage.getItem(key_sesId)) {
      // Simpan penanda ke local storage
      localStorage.setItem(key_sesId, uuidv4());
      // Lakukan sesuatu di sini (hanya untuk pertama kali)

      localStorage.setItem(key_time, Date.now());
    }

    // renew session key
    const currentTime = Date.now()
    const latestSetKey = localStorage.getItem(key_time)
    const timeDifference = (currentTime - latestSetKey) / 1000
    if (timeDifference > 86400) {
      localStorage.setItem(key_sesId, uuidv4());
      localStorage.setItem(key_time, Date.now());
    }

  });

  // Simpan state chat saat tab tidak aktif
  let chatState = {
    isTyping: false,
    messages: [],
  };

  // Proses state saat tab kembali aktif
  document.addEventListener("visibilitychange", () => {
    if (document.visibilityState === "hidden") {
      // Jika tab tidak aktif, langsung tampilkan seluruh pesan
      const botMessageBubble = document.querySelector(".message.bot:last-child .bubble");
      // if (botMessageBubble) {
      //   botMessageBubble.textContent = completeMessage; // Tampilkan seluruh pesan
      // }
    }
  });

  setDynamicBackground();
  

  
})();