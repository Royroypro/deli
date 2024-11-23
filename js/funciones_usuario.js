function openModal(modalId) {
    var modal = document.getElementById(modalId);
    if (!modal) {
        console.error(`El modal con ID ${modalId} no se encuentra en el DOM.`);
        return;
    }
    
    var form = modal.querySelector('form');
    if (!form) {
        console.error(`No se encontró un formulario dentro del modal con ID ${modalId}.`);
        return;
    }
    
    var nombreInput = form.querySelector('input[name="nombre-reg"]');
    var idPersonalInput = form.querySelector('input[name="id_personal-reg"]');
    var idTipoUsuarioSelect = form.querySelector('select[name="id_tipousuario-reg"]');
    var estadoSelect = form.querySelector('select[name="estado-reg"]');

    // Verifica si los elementos existen antes de asignarles valores
    if (nombreInput && modal.dataset.nombre) {
        nombreInput.value = modal.dataset.nombre || '';
    } else {
        console.warn('No se encontró el campo "nombre-reg" o "nombre" en el dataset');
    }

    if (idPersonalInput && modal.dataset.idPersonal) {
        idPersonalInput.value = modal.dataset.idPersonal || '';
    } else {
        console.warn('No se encontró el campo "id_personal-reg" o "idPersonal" en el dataset');
    }

    if (idTipoUsuarioSelect && modal.dataset.idTipoUsuario) {
        idTipoUsuarioSelect.value = modal.dataset.idTipoUsuario || '';
    } else {
        console.warn('No se encontró el campo "id_tipousuario-reg" o "idTipoUsuario" en el dataset');
    }

    if (estadoSelect && modal.dataset.estado) {
        estadoSelect.value = modal.dataset.estado || '';
    } else {
        console.warn('No se encontró el campo "estado-reg" o "estado" en el dataset');
    }

    modal.style.display = "block";

    // Agregar funcionalidad para cerrar la ventana modal
    var closeButton = modal.querySelector('.close');
    if (closeButton) {
        closeButton.onclick = function() {
            closeModal(modalId);
        };
    } else {
        console.warn('No se encontró el botón de cierre en el modal');
    }
}

function closeModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = "none";
    } else {
        console.error(`El modal con ID ${modalId} no se encuentra en el DOM.`);
    }
}

