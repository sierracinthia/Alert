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
    <?php include __DIR__ . '/map.php'; ?>
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

        document.querySelectorAll('.tab-section').forEach(sec => sec.style.display = 'none');
        document.getElementById(tabId).style.display = 'block';

        document.querySelectorAll('.dashboard-tabs button').forEach(btn => btn.classList.remove('active'));
        document.querySelector(`.dashboard-tabs button[onclick="showTab('${tabId}')"]`).classList.add('active');
    }

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
        credentials: 'include' 
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
