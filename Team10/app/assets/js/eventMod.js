const confirmations = {
    
}

const deleteConfirmation = (el, id) => {
    const formData = new FormData()
    formData.append("event_id", id)
    if (confirmations[id] !== undefined) {
        fetch(`/Team10/app/events/delete.php`, {
            method: "POST",
            body: new URLSearchParams(formData)
        }).then(() => {
            confirmations[id] = undefined;
            window.location.href = window.location.href
        })
        return;
    }
    el.style.backgroundColor = "red";
    el.innerHTML = "Confirm?"
    confirmations[id] = true
    return;
}