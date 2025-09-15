<?php
// session_start() ya debe estar en index.php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistema de Alertas</title>
    <link rel="stylesheet" href="style.css">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS (opcional si quieres componentes interactivos) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body>
    <h2>Bienvenido, <?= htmlspecialchars($_SESSION['username']) ?></h2>

    <!-- Pestañas -->
    <div class="dashboard-tabs">
        <button onclick="showTab('map')">Mapa y Alerta</button>
        <button onclick="showTab('contacts')">Contactos</button>
        <button onclick="showTab('alerts')">Historial de Alertas</button>
    </div>

    <!-- Secciones -->
<section id="map" class="tab-section">
    <button id="sendAlertBtn">📍 Enviar Alerta</button>
    <div id="mapid" style="height: 400px; width: 100%; margin-top:10px;"></div>

    <script>
        var map = L.map('mapid').setView([-34.6037, -58.3816], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Agregar alertas como marcadores
        <?php if(!empty($alerts)): ?>
            <?php foreach($alerts as $alert): ?>
                L.marker([<?= $alert['latitude'] ?>, <?= $alert['longitude'] ?>])
                 .addTo(map)
                 .bindPopup('Alerta enviada: <?= $alert['sent_at'] ?>');
            <?php endforeach; ?>
        <?php endif; ?>

        // Botón enviar alerta
        document.getElementById('sendAlertBtn').addEventListener('click', function() {
            if (navigator.geolocation) {
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
                            alert('Alerta enviada correctamente');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(err => console.error("Error al enviar alerta:", err));
                }, function(err){
                    alert('No se pudo obtener tu ubicación.');
                });
            } else {
                alert('Tu navegador no soporta geolocalización.');
            }
        });

    </script>
</section>


    <section id="contacts" class="tab-section" style="display:none;">
        <?php include __DIR__ . '/contacts.php'; ?>
    </section>

    <section id="alerts" class="tab-section" style="display:none;">
        <h3>Historial de Alertas</h3>
        <?php if(!empty($alerts)): ?>
            <table>
                <tr><th>Latitud</th><th>Longitud</th><th>Enviada</th></tr>
                <?php foreach($alerts as $alert): ?>
                    <tr>
                        <td><?= htmlspecialchars($alert['latitude']) ?></td>
                        <td><?= htmlspecialchars($alert['longitude']) ?></td>
                        <td><?= htmlspecialchars($alert['sent_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No se han enviado alertas aún.</p>
        <?php endif; ?>
    </section>

    <script>
    function showTab(tabId) {
        // Ocultar todas las secciones
        document.querySelectorAll('.tab-section').forEach(sec => sec.style.display = 'none');
        document.getElementById(tabId).style.display = 'block';

        // Actualizar botón activo
        document.querySelectorAll('.dashboard-tabs button').forEach(btn => btn.classList.remove('active'));
        document.querySelector(`.dashboard-tabs button[onclick="showTab('${tabId}')"]`).classList.add('active');
    }

    // Inicializar mostrando la primera pestaña
    document.addEventListener('DOMContentLoaded', () => {
        showTab('map');
    });
    </script>
</body>
<button id="logoutBtn">🚪 Cerrar sesión</button>

<script>
document.getElementById('logoutBtn').addEventListener('click', function() {
    fetch('index.php?page=logout', {
        method: 'POST',
        credentials: 'include' // para enviar cookies si usás sesiones
    })
    .then(res => {
        if (res.ok) {
            window.location.href = 'index.php?page=login'; // o tu página de inicio
        } else {
            alert('Error al cerrar sesión');
        }
    })
    .catch(err => console.error('Error en logout:', err));
});
</script>

</html>
