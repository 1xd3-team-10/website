window.addEventListener("load", () => {
    const calendarEl = document.getElementById("calendar");
    const startTimeEl = document.getElementById("start_time");
    const endTimeEl = document.getElementById("end_time");

    if (!calendarEl) return;

    let currentDate = new Date();

    function startOfMonth(date) {
        return new Date(date.getFullYear(), date.getMonth(), 1);
    }

    function endOfMonth(date) {
        return new Date(date.getFullYear(), date.getMonth() + 1, 0);
    }

    function formatDateKey(date) {
        return date.toISOString().split("T")[0];
    }

    function groupEventsByDay(events) {
        const map = {};
        events.forEach(e => {
            const key = e.start_time.split(" ")[0];
            if (!map[key]) map[key] = [];
            map[key].push(e);
        });
        return map;
    }

    function render() {
        calendarEl.innerHTML = "";

        const header = document.createElement("div");
        header.className = "calendar-header";

        const title = document.createElement("h2");
        title.textContent = currentDate.toLocaleString("default", {
            month: "long",
            year: "numeric"
        });

        const prevBtn = document.createElement("button");
        prevBtn.textContent = "<";
        prevBtn.onclick = () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            render();
        };

        const nextBtn = document.createElement("button");
        nextBtn.textContent = ">";
        nextBtn.onclick = () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            render();
        };

        header.appendChild(prevBtn);
        header.appendChild(title);
        header.appendChild(nextBtn);

        const grid = document.createElement("div");
        grid.className = "calendar-grid";

        const start = startOfMonth(currentDate);
        const end = endOfMonth(currentDate);

        const eventsByDay = groupEventsByDay(window.EVENTS || []);
        console.log(eventsByDay)
        const firstDay = start.getDay();
        for (let i = 0; i < firstDay; i++) {
            grid.appendChild(document.createElement("div"));
        }

        for (let d = 1; d <= end.getDate(); d++) {
            const date = new Date(currentDate.getFullYear(), currentDate.getMonth(), d);
            const key = formatDateKey(date);

            const cell = document.createElement("div");
            cell.className = "calendar-cell";

            const dayLabel = document.createElement("div");
            dayLabel.className = "calendar-day";
            dayLabel.textContent = d;

            cell.appendChild(dayLabel);

            if (eventsByDay[key]) {
                eventsByDay[key].forEach(event => {
                    const ev = document.createElement("div");
                    ev.className = "calendar-event";
                    ev.textContent = event.title;

                    ev.addEventListener("click", () => {
                        window.location.href = `/Team10/app/events/edit.php?id=${eventsByDay[key][0].event_id}`
                    })
                    cell.appendChild(ev);
                });
            }

            cell.addEventListener("click", (e) => {
                const tempTime = new Date()
                tempTime.setTime(date.getTime()) 
                startTimeEl.value = tempTime.toISOString().slice(0, 16);
                tempTime.setTime(date.getTime() + 3600000)
                endTimeEl.value = tempTime.toISOString().slice(0, 16);
            })

            grid.appendChild(cell);
        }

        calendarEl.appendChild(header);
        calendarEl.appendChild(grid);
    }

    render();
});
