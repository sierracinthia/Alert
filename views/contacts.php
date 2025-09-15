<h3>Contactos</h3>
<form id="addContactForm" action="index.php?page=add_contact" method="POST">
    <input type="text" name="name" placeholder="Nombre" required>
    <input type="email" name="email" placeholder="Correo" required>
    <button type="submit">Agregar Contacto</button>
</form>

<div id="contactsList">
    <?php if($contacts): ?>
        <ul>
        <?php foreach($contacts as $contact): ?>
            <li><?= htmlspecialchars($contact['name']) ?> (<?= htmlspecialchars($contact['contact_email']) ?>)
            <a href="index.php?page=delete_contact&id=<?= $contact['id_contact'] ?>" class="delete-btn">Eliminar</a>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No hay contactos aún.</p>
    <?php endif; ?>
</div>