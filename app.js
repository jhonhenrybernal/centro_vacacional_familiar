document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('calendar');
  const reservationForm = document.getElementById('reservation-form');
  const selectedDatesEl = document.getElementById('selected-dates');
  const bookingForm = document.getElementById('bookingForm');
  const processingMessage = document.getElementById('processingMessage');

  // Obtener las fechas reservadas desde el backend
  fetch('get_reservations.php')
    .then(response => response.json())
    .then(reservedDates => {
      const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        selectable: true,
        events: reservedDates, // Cargar las fechas reservadas desde la respuesta
        select: function (info) {
          const start = info.startStr;

          // Ajustar la fecha de finalización restando un día
          const endDate = new Date(info.end);
          endDate.setDate(endDate.getDate() - 1);
          const end = endDate.toISOString().split('T')[0];

          // Muestra las fechas seleccionadas en el formulario
          selectedDatesEl.textContent = `${start} a ${end}`;
          reservationForm.style.display = 'block';

          // Enviar el formulario de reserva
          bookingForm.onsubmit = function (event) {
            event.preventDefault();
            processingMessage.style.display = 'block';
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const whatsapp = document.getElementById('whatsapp').value;

            // Enviar los datos al backend y agregar al calendario si es exitoso
            fetch('reserve.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ start, end, name, email, whatsapp })
            })
            .then(response => response.json())
            .then(data => {
              Swal.fire({
                title: "Reserva realizada!",
                text: "Estaremos en contacto pronto y su reserva llegará vía correo!",
                icon: "success"
              });
              processingMessage.style.display = 'none';
              reservationForm.style.display = 'none';
              calendar.unselect();

              // Añadir las fechas reservadas al calendario
              calendar.addEvent({
                title: 'Reservado',
                start: start,
                end: info.endStr,
                color: 'green'
              });
            });
          };
        }
      });
      
      calendar.render();
    });
});
