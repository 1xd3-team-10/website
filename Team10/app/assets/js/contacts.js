window.addEventListener(("load"), () => {
    const contacts = document.querySelectorAll("#contact");
    const sendMessage = document.querySelector("#sendMessage");
    const messageInput = document.querySelector("#send_message_input");
    const chatWindow = document.querySelector("#chatWindow");
    let recipient = "";
    let recipient_id = -1;
    let seenUpdates = [];

    contacts.forEach((contact) => {
        contact.addEventListener("click", () => {
            messageInput.disabled = false
            recipient = contact.innerHTML;
            fetch("../events/getMessages.php", {
                method: "POST",
                credentials: "include",
                body: JSON.stringify({
                    recipient: recipient
                })
            }).then((res) => {
                if (res.ok) {
                    return res.json()
                }
            }).then((data) => {
                const recipientID = data.recipientID;
                chatWindow.innerHTML = ""
                data.messages.forEach((msg) => {
                    const chatMsg = createMessage(msg.content, msg.sender_id !== data.recipientID)
                    recipient_id = data.recipientID
                    chatWindow.append(chatMsg)
                })
            })
        })
    })

    sendMessage.addEventListener("click", () => {
        send();
    })

    messageInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") send();
    })

    const send = () => {
        if (messageInput.value === "") {
            const errorMsg = document.createElement('div');
            errorMsg.classList = "auth_error";
            errorMsg.onclick = () => { errorMsg.style.display = 'none' }
            errorMsg.innerText = "Please enter a valid message";
            document.body.prepend(errorMsg);
            return;
        }
        fetch("../events/sendMessage.php", {
            method: "POST",
            credentials: "include",
            body: JSON.stringify({
                recipient: recipient,
                msg: messageInput.value
            })
        }).then((res) => {
            if (res.ok) {
                const msg = messageInput.value;
                const chatMsg = createMessage(msg, true);
                chatWindow.append(chatMsg);
                scrollToBottom();
                messageInput.value = "";
            }
        })
    }

    const createMessage = (msg, byUser) => {
        const newMessage = document.createElement('div');
        newMessage.innerText = msg;
        if (byUser) {
            newMessage.classList = "userMessage"
        } else {
            newMessage.classList = "recipientMessage"
        }
        return newMessage;
    }

    const scrollToBottom = () => {
        chatWindow.scrollTop = chatWindow.scrollHeight;
    };

    setInterval(() => {
        fetch("../events/getUpdates.php", {
            method: "POST",
            credentials: "include",
            body: JSON.stringify({
                seenUpdates: seenUpdates
            })
        }).then(res => res.ok ? res.json() : [])
        .then(data => {
            data.updates.forEach(update => {
                seenUpdates.push(update.update_id);
                const updateData = JSON.parse(update.content);
                const updateDataData = updateData.data; // ✅ no extra parse
                if (updateData.type === "message" && updateDataData.sender_id === recipient_id) {
                    const chatMsg = createMessage(updateDataData.content, false);
                    chatWindow.append(chatMsg); // ✅ append instead of push
                    scrollToBottom();
                }
            });
        });
    }, 500);
})