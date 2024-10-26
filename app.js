document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('calendar');
  const reservationForm = document.getElementById('reservation-form');
  const selectedDatesEl = document.getElementById('selected-dates');
  const bookingForm = document.getElementById('bookingForm');

  // Ejemplo de fechas reservadas preexistentes
  const reservedDates = [
    {
      id: 'a',
      title: 'Reservado',
      start: '2024-11-07',
      end: '2024-12-01', // El final es exclusivo, así que el 30 estará incluido
      color: 'green'
    }
  ];

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    selectable: true,
    events: reservedDates,
    select: function (info) {
      const start = info.startStr;
      
      // Ajustar la fecha de finalización restando un día
      const endDate = new Date(info.end);
      endDate.setDate(endDate.getDate() - 1);
      const end = endDate.toISOString().split('T')[0]; // Formatear la fecha como YYYY-MM-DD

      // Muestra las fechas seleccionadas en el formulario
      selectedDatesEl.textContent = `${start} a ${end}`;
      reservationForm.style.display = 'block';

      // Enviar el formulario de reserva
      bookingForm.onsubmit = function (event) {
        event.preventDefault();

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

          // Oculta el formulario y actualiza el calendario
          reservationForm.style.display = 'none';
          calendar.unselect();
          
          // Añadir las fechas reservadas al calendario
          calendar.addEvent({
            title: 'Reservado',
            start: start,
            end: info.endStr, // Pasamos el valor original a FullCalendar
            color: 'green'
          });
        });
      };
    }
  });

  calendar.render();
});
