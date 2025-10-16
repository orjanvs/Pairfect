window.addEventListener('load', (event) => {

    function formatTime(input) {
        const d = input instanceof Date ? input : new Date(input);
        const h = String(d.getHours()).padStart(2, '0');   // 00–23
        const m = String(d.getMinutes()).padStart(2, '0'); // 00–59
        return `${h}:${m}`;
    }

    const chatBox = document.querySelector(".messageHistory");
    const currHour = new Date();
    const starterMessage = `<div class="columns">
                                <div class="column">
                                    <div class="notification is-info">
                                        <h6 class="subtitle is-6">${formatTime(currHour)}</h6>
                                        Hi! I'm Pairfect, your AI wine pairing assistant. 
                                        Ask me for a wine pairing based on a food, cuisine, or ingredient!
                                        I will try to give you the best possible wine pairing suggestions.
                                    </div>
                                </div>
                                <div class="column is-one-third"></div>
                            </div>`;

    chatBox.innerHTML += starterMessage;

    document.querySelector(".message").addEventListener('keydown', e => {
        if (e.key === 'Enter') document.querySelector(".sendMessage").click();
    });


    document.querySelector(".sendMessage").addEventListener('click', (event) => {
        const val = document.querySelector(".message").value.trim();
        if (!val) return;

        event.currentTarget.classList.add('is-loading');
        event.currentTarget.disabled = true;

        document.querySelector(".result").parentElement.classList.add("is-hidden");
        document.querySelector(".error").parentElement.classList.add("is-hidden");

        let currHour = new Date();

        const userMsgTemplate = `<div class="columns">
                                        <div class="column is-one-third"></div>
                                        <div class="column">
                                            <div class="notification is-success">
                                                <h6 class="subtitle is-6">${formatTime(currHour)}</h6>
                                                ${document.querySelector(".message").value}
                                            </div>
                                        </div>
                                    </div>`


        let chatBox = document.querySelector(".messageHistory");

        chatBox.innerHTML += userMsgTemplate;
        chatBox.scrollIntoView(false);

        const payload = JSON.stringify({
            "message": document.querySelector(".message").value
        });

        document.querySelector(".message").value = "";

        fetch('requestmanager.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: payload,
        }).then(response => response.json())
            .then(data => {

                let currHour = new Date();

                data.responseMessage = (data.responseMessage || '').replace(/\n/g, '<br>');


                let aiMsgTemplate = `<div class="columns">
                                                <div class="column">
                                                    <div class="notification is-info">
                                                        <h6 class="subtitle is-6">${formatTime(currHour)}</h6>
                                                        ${data.responseMessage}
                                                    </div>
                                                </div>
                                                <div class="column is-one-third"></div>
                                            </div>`

                chatBox.innerHTML += aiMsgTemplate;
                chatBox.scrollIntoView(false);

            })
            .catch((error) => {
                console.error('Error:', error);
            }).finally(() => {
                document.querySelector(".sendMessage").classList.remove('is-loading');
                document.querySelector(".sendMessage").disabled = false;
            });
    });

});