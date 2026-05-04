// Recherche vocale
document.addEventListener('DOMContentLoaded', function() {
    const voiceSearchButton = document.getElementById('voiceSearchButton');
    const searchInput = document.getElementById('searchInput');

    if (voiceSearchButton && searchInput) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

        if (!SpeechRecognition) {
            voiceSearchButton.style.opacity = 0.5;
            voiceSearchButton.title = "Recherche vocale non supportée";
            return;
        }

        const recognition = new SpeechRecognition();
        recognition.lang = 'fr-FR';
        recognition.interimResults = false;

        recognition.onstart = function() {
            voiceSearchButton.classList.add('active');
            voiceSearchButton.innerHTML = '<i class="bi bi-mic-mute-fill"></i>';
        };

        recognition.onend = function() {
            voiceSearchButton.classList.remove('active');
            voiceSearchButton.innerHTML = '<i class="bi bi-mic-fill"></i>';
        };

        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript.trim();
            searchInput.value = transcript;
            searchInput.form.submit();
        };

        recognition.onerror = function(event) {
            console.error("Erreur de reconnaissance vocale:", event.error);
            voiceSearchButton.classList.remove('active');
            voiceSearchButton.innerHTML = '<i class="bi bi-mic-fill"></i>';

            if (event.error === 'not-allowed') {
                alert("Veuillez autoriser l'accès au micro pour utiliser la recherche vocale.");
            }
        };

        voiceSearchButton.addEventListener('click', function() {
            if (voiceSearchButton.classList.contains('active')) {
                recognition.stop();
            } else {
                recognition.start();
            }
        });
    }
});
