<h3>Mapa y Envío de Alerta</h3>

<!-- Botones -->
<button id="sendAlertBtn">📍 Enviar Alerta desde navegador</button>
<button id="sendDeviceAlertBtn">🔴 Enviar Alerta desde dispositivo</button>

<div id="mapid" style="height: 400px; width: 100%; margin-top:10px;"></div>

<script>
// Inicializar mapa
var map = L.map('mapid').setView([-34.6037, -58.3816], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Mostrar alertas existentes
<?php if(!empty($alerts)): ?>
    <?php foreach($alerts as $alert): ?>
        L.marker([<?= $alert['latitude'] ?>, <?= $alert['longitude'] ?>])
         .addTo(map)
         .bindPopup('Alerta enviada: <?= $alert['sent_at'] ?>');
    <?php endforeach; ?>
<?php endif; ?>

// --- Enviar alerta desde navegador ---
document.getElementById('sendAlertBtn').addEventListener('click', function() {
    if (!navigator.geolocation) {
        alert('Tu navegador no soporta geolocalización.');
        return;
    }

    navigator.geolocation.getCurrentPosition(function(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;

        fetch('index.php?page=alert', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({latitude: lat, longitude: lng})
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'ok'){
                L.marker([lat, lng]).addTo(map)
                 .bindPopup('Alerta enviada desde navegador').openPopup();
                alert(data.message);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => console.error("Error al enviar alerta:", err));
    }, function(){
        alert('No se pudo obtener tu ubicación.');
    });
});

// --- Enviar alerta desde botón físico ---
document.getElementById('sendDeviceAlertBtn').addEventListener('click', function() {
    fetch('index.php?page=alert', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({}) // vacío → backend obtiene GPS físico
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'ok'){
            // Mostrar marcador del GPS físico en el mapa
            L.marker([data.latitude, data.longitude]).addTo(map)
             .bindPopup('Alerta enviada desde dispositivo').openPopup();
            alert(data.message);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => console.error("Error al enviar alerta:", err));
});
</script>
