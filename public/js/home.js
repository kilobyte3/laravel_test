class Home
{
    calendar;

    run()
    {
        const this_ = this;

        const refreshReservations = function(runWhenFinished)
        {
            const workSpace = document.getElementById('allReservationsList');
            workSpace.innerHTML = '<div style="position: absolute"><img src="/img/flow.gif" width="32" height="32"></div>'+workSpace.innerHTML;
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/getreservations', true);
            xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
            xhr.send();
            xhr.onload = function() {
                if (this.status === 200)
                {
                    let reservations;
                    try
                    {
                        reservations = JSON.parse(this.response);
                    }
                    catch(e)
                    {
                        alert('Nem várt válasszal tért vissza a szerver! Próbálja meg később!');
                        return;
                    }
                    let s = '';
                    reservations.forEach(function(item) {
                        s+= '<li>'+item.start+'<br>'+item.end+'<br>[<b>'+item.clientname+'</b>] <a class="delete_reservation" data-id="'+item.id+'" href="javascript:void(0);">🗑️</a></li>';
                    });
                    workSpace.innerHTML = s;
                    if (typeof runWhenFinished !== 'undefined')
                    {
                        runWhenFinished();
                    }
                }
                else
                {
                    alert('Hiba történt a hálózattal! Próbálja meg később!');
                }
            }
        }

        const refreshReceptions = function(runWhenFinished)
        {
            const workSpace = document.getElementById('allReceptionDatesList');
            workSpace.innerHTML = '<div style="position: absolute"><img src="/img/flow.gif" width="32" height="32"></div>'+workSpace.innerHTML;
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/getreceptions', true);
            xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
            xhr.send();
            xhr.onload = function() {
                if (this.status === 200)
                {
                    let reservations;
                    try
                    {
                        reservations = JSON.parse(this.response);
                    }
                    catch(e)
                    {
                        alert('Nem várt válasszal tért vissza a szerver! Próbálja meg később!');
                        return;
                    }
                    let s = '';
                    reservations.forEach(function(item) {
                        s+= '<li>'+item.start+'<br>'+item.end+'<br>[<i>'+item.repetitivity+'</i>';
                        if (item.repetitivityday !== '')
                        {
                            s+= ', ';
                        }
                        s+= '<b>'+item.repetitivityday+'</b>';
                        if(item.until !== '')
                        {
                            s+= ', ';
                        }
                        s+= item.until+']</li>';
                    });
                    workSpace.innerHTML = s;
                    if (typeof runWhenFinished !== 'undefined')
                    {
                        runWhenFinished();
                    }
                }
                else
                {
                    alert('Hiba történt a hálózattal! Próbálja meg később!');
                }
            }
        }

        const refreshCalendar = function()
        {
            const loader = document.getElementById('calendarLoader');
            loader.innerHTML = '<img class="loadingDiv" style="position: absolute" src="/img/flow.gif" width="256" height="256">';
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/getcalendar', true)
            xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
            xhr.send();
            xhr.onload = function() {
                loader.innerHTML = '';
                if (this.status === 200)
                {
                    let response;
                    try
                    {
                        response = JSON.parse(this.response);
                    }
                    catch(e)
                    {
                        alert('Nem várt válasszal tért vissza a szerver! Próbálja meg később!');
                        return;
                    }
                    if (response.length !== 0)
                    {
                        refreshCalendarByValues(response.receptionTimesPure, response.reservedTimesPure);
                    }
                }
                else
                {
                    alert('Hiba történt a hálózattal! Próbálja meg később!');
                }
            }
        }

        const refreshCalendarByValues = function(receptionDates, reservationDates)
        {
            this_.calendar.removeAllEvents();
            reservationDates.forEach(function(item) {
                this_.calendar.addEvent({
                    classNames: ['home_calendar_reservationtime'],
                    textColor : 'gray',
                    title     : item.clientname,
                    start     : new Date(item.start),
                    end       : new Date(item.end),
                    allDay    : false
                });
            });
            receptionDates.forEach(function(item) {
                const e = {
                    classNames: ['home_calendar_receptiontime'],
                    textColor : 'gray',
                    title     : '['+item.start.substring(11)+'-'+item.end.substring(11)+']',
                    allDay    : false
                };
                if (item.repetitivity === null)
                {
                    e.start = new Date(item.start);
                    e.end   = new Date(item.end);
                }
                else
                {
                    e.daysOfWeek = [parseInt(item.repetitivityday)];
                    e.startTime  = item.start.substring(11);
                    e.startRecur = item.start.substring(0,10)+' 00:00:00';
                    e.endTime    = item.end.substring(11);
                    if (item.until !== null)
                    {
                        e.endRecur = item.until+' 00:00:00';
                    }
                    if (parseInt(item.repetitivity) === 2) // páratlan hét
                    {
                        e.extendedProps = {'odd': 1};
                    }
                    if (parseInt(item.repetitivity) === 1) // páros hét
                    {
                        e.extendedProps = {'even': 1};
                    }
                }
                this_.calendar.addEvent(e);
            });
        }

        this.calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            slotLabelFormat: {
                hour: 'numeric',
                minute: '2-digit',
                hour12: false
            },
            /*eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                meridiem: false,
            },*/
            displayEventTime: false, // szándékosan kapcsoltam ki, a title-ba írom bele a kezdő és végidőpontokat
            selectable: true,
            firstDay: 1,
            contentHeight: 'auto',
            stickyHeaderDates: true,
            eventClick: function(event) {
                this.changeView('list', event.event.start);
                //event.jsEvent.cancelBubble = true;
                //event.jsEvent.preventDefault(); letiltja, de akkor sem lehet kijelölni időtartományokat
            },
            select: function(selectionInfo) {
                let start = selectionInfo.start;
                let end = selectionInfo.end;
                if (selectionInfo.allDay)
                {
                    start.setUTCHours(0,0,0, 0);
                    start.setDate(start.getDate()+1);
                    end.setUTCHours(23,59,59, 999);
                    start = new Date(start.toLocaleString());
                    end = new Date(end.toLocaleString());
                    start = [start.getUTCFullYear(), start.getUTCMonth()+1, start.getUTCDate()].join('-')+' '+[start.getUTCHours(), start.getUTCMinutes(), start.getUTCSeconds()].join(':');
                    end = [end.getUTCFullYear(), end.getUTCMonth()+1,end.getUTCDate()].join('-')+' '+[end.getUTCHours(), end.getUTCMinutes(), end.getUTCSeconds()].join(':');
                }
                else
                {
                    start = [start.getFullYear(), start.getMonth()+1, start.getDate()].join('-')+' '+[start.getHours(), start.getMinutes(), start.getSeconds()].join(':');
                    end = [end.getFullYear(), end.getMonth()+1,end.getDate()].join('-')+' '+[end.getHours(), end.getMinutes(), end.getSeconds()].join(':');
                }
                const clientName = prompt('Új foglalás felvitele:\n\n'+start+' -\n'+end+'\n\nírd be az ügyfél nevét', '');
                if (clientName)
                {
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '/addreservation', true);
                    xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
                    xhr.send(JSON.stringify({'start': start, 'end': end, 'clientname': clientName}));
                    xhr.onload = function() {
                        if (this.status === 200)
                        {
                            let response;
                            try
                            {
                                response = JSON.parse(this.response);
                            }
                            catch(e)
                            {
                                alert('Nem várt válasszal tért vissza a szerver! Próbálja meg később!');
                                return;
                            }
                            if (response[0] === 'ok')
                            {
                                alert('Az időpont sikeresen lefoglalva!');
                                refreshReservations(function() {
                                    refreshCalendar();
                                });
                            }
                            else
                            {
                                let s = 'Az időpont nem foglalható:\n\n';
                                switch(response[0])
                                {
                                    case 'nwl':
                                        s+= 'nincs ügyfélfogadási időben!';
                                        break;
                                    case 'pwl':
                                        s+= 'csak részben van foglalási időben:\n\n'+response[1]+' -\n'+response[2]+'\n\n'+'teljesen benne kell hogy legyen!';
                                        break;
                                    case 'bl':
                                        s+= 'ütközik ezzel a foglalással:\n\n'+response[1]+' -\n'+response[2]+'\n\n'+response[3];
                                        break;
                                }
                                alert(s);
                            }
                        }
                        else
                        {
                            alert('Hiba történt a hálózattal! Próbálja meg később!');
                        }
                    }
                }
            },
            eventDidMount: function(info) {
                // sajnos egy kicsit "hackelni" kellett, hogy működjön páros, páratlan hetenként is
                const oneJan = new Date(info.event.start.getFullYear(), 0, 1);
                const numberOfDays = Math.floor((info.event.start - oneJan) / (24 * 60 * 60 * 1000));
                const numberOfWeek = Math.ceil((info.event.start.getDay() + 1 + numberOfDays) / 7);
                const isOdd = numberOfWeek & 1; // páratlan
                if (info.event.extendedProps.even === 1 && isOdd) // páros hét
                {
                    info.el.style.display = 'none';
                }
                if (info.event.extendedProps.odd === 1 && !isOdd) // páratlan
                {
                    info.el.style.display = 'none';
                }
            }
        });
        this.calendar.render();

        refreshCalendar();
        refreshReservations();
        refreshReceptions();

        // click events
        document.addEventListener("click", function(e) {
            if (e.target.className === 'delete_reservation')
            {
                e.target.dataset.saveHtml = e.target.innerHTML;
                e.target.innerHTML = '<img src="/img/flow.gif" width="32" height="32">';
                e.target.className = '';
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/deletereservation', true);
                xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
                xhr.send(JSON.stringify({'id': e.target.dataset.id}));
                xhr.onload = function() {
                    e.target.innerHTML = e.target.dataset.saveHtml;
                    if (this.status === 200)
                    {
                        try
                        {
                            alert(JSON.parse(this.response));
                        }
                        catch(e)
                        {
                            alert('Nem várt válasszal tért vissza a szerver! Az oldal most újratölt!');
                            location.reload(true);
                            return;
                        }
                        refreshReservations(function() {
                            refreshCalendar();
                        });
                    }
                    else
                    {
                        alert('Hiba történt a hálózattal! Az oldal most újratölt!');
                        location.reload(true);
                    }
                }
            }
        });
    }
}
