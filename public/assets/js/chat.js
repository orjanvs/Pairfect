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