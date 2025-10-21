<!-- HTML structure copied from https://medium.com/winkhosting/create-a-chatbot-using-ai-chatgpt-and-php-41bb752f6403 -->
<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pairfect - Your Wine Pairing Assistant</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <script type="module" src="assets/js/chat.js"></script>
    <link rel="stylesheet" href="assets/stylesheet.css">
</head>

<body>

   

    <!-- Site Header -->
    <nav class="navbar is-light" role="navigation" aria-label="main navigation">
        <div class="container">
            <div class="navbar-brand">
                <a class="navbar-item" href="./">
                    <img src="assets/Pair.png" alt="Pairfect logo" class="pairfect-logo">
                </a>
                <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="mainNav">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>
            </div>

            <div id="mainNav" class="navbar-menu">
                <div class="navbar-end">
                    <div class="navbar-item">
                        <div class="buttons">
                            <?php if (!isset($_SESSION['user_id'])): ?>
                                <a class="button is-light" href="./register.php">Register</a>
                                <a class="button is-primary" href="./login.php">Logg inn</a>
                            <?php else: ?>
                                <form method="post" action="logout.php" style="display:inline;">
                                    <button class="button is-danger" type="submit">Logg ut</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

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
    <script>
        // Bulma navbar burger toggle for mobile
        document.addEventListener('DOMContentLoaded', function () {
            var burgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
            burgers.forEach(function (burger) {
                burger.addEventListener('click', function () {
                    var target = burger.dataset.target;
                    var $target = document.getElementById(target);
                    burger.classList.toggle('is-active');
                    if ($target) {
                        $target.classList.toggle('is-active');
                    }
                });
            });
        });
    </script>
</body>

</html>