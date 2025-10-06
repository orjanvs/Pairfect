<!-- HTML structure copied from https://medium.com/winkhosting/create-a-chatbot-using-ai-chatgpt-and-php-41bb752f6403 -->

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pairfect - Your Wine Pairing Assistant</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <script type="module">
        window.addEventListener('load', (event) => {

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
                                                <h6 class="subtitle is-6">${currHour.getHours() + ":" + currHour.getMinutes()}</h6>
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
                                                        <h6 class="subtitle is-6">${currHour.getHours() + ":" + currHour.getMinutes()}</h6>
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
    </script>
</head>

<body>
    <section class="section">
        <div class="container">
            <div class="columns  box">
                <div class="column">
                    <div class="columns">
                        <div class="column has-text-centered">
                            <h1 class="title">
                                Pairfect - Your Wine Pairing Assistant
                            </h1>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column">
                            <div class="card-content" style="height:600px;overflow:auto;flex-grow: 1;flex-shrink: 1;">
                                <div class="content messageHistory">


                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="columns">
                        <div class="column">
                            <input class="input message" type="text" placeholder="Type your message">
                        </div>
                        <div class="column is-narrow">
                            <button class="button is-info sendMessage">
                                Send
                            </button>
                        </div>
                    </div>

                </div>
            </div>
            <div class="columns box is-hidden">
                <div class="column result"></div>
            </div>
            <div class="columns box is-hidden">
                <div class="column notification is-danger error has-text-centered">
                </div>
            </div>
        </div>
    </section>
</body>

</html>