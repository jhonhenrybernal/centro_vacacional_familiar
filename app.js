
  // FullCalendar Integration
  document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const reservationForm = document.getElementById('reservation-form');
    const selectedDatesEl = document.getElementById('selected-dates');
    const bookingForm = document.getElementById('bookingForm');
  
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      selectable: true,
      events: 'get_reservations.php',
      select: function (info) {
        const start = info.startStr;
        const end = info.endStr;
  
        // Muestra las fechas seleccionadas en el formulario
        selectedDatesEl.textContent = `${start} a ${end}`;
        reservationForm.style.display = 'block';
  
        // Escucha el envío del formulario de reserva
        bookingForm.onsubmit = function (event) {
          Swal.fire({
            title: "Reserva realizada!",
            text: "Estaremos en contacto pronto y su reserva llegara via correo!",
            icon: "success"
          });
          event.preventDefault(); // Evita el envío tradicional del formulario
  
          const name = document.getElementById('name').value;
          const email = document.getElementById('email').value;
          const whatsapp = document.getElementById('whatsapp').value;
  
          // Enviar datos al backend
          fetch('reserve.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ start, end, name, email, whatsapp })
          })
          .then(response => response.json())
          .then(data => {
            alert(data.message);
            reservationForm.style.display = 'none'; // Oculta el formulario tras la confirmación
            calendar.unselect(); // Deselecciona las fechas en el calendario
          });
        };
      }
    });
  
    calendar.render();
  });
    