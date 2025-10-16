<!-- HTML structure copied from https://medium.com/winkhosting/create-a-chatbot-using-ai-chatgpt-and-php-41bb752f6403 -->

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pairfect - Your Wine Pairing Assistant</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <script type="module" src="assets/js/chat.js"></script>
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
                            <input class="input message" type="text" placeholder="Type your message" maxlength="200">
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