function openModalTipoUsuario(id_tipousuario) {
    const modalId = `modal-tipo-usuario-${id_tipousuario}`;
    const modal = document.getElementById(modalId);
    if (!modal) {
        console.error(`El modal con ID ${modalId} no se encuentra en el DOM.`);
        return;
    }

    const form = modal.querySelector('form');
    if (!form) {
        console.error(`No se encontró un formulario dentro del modal con ID ${modalId}.`);
        return;
    }

    const nombreInput = form.querySelector('input[name="nombre-reg"]');
    const estadoSelect = form.querySelector('select[name="estado-reg"]');

    if (nombreInput && modal.dataset.nombre) {
        nombreInput.value = modal.dataset.nombre;
    } else {
        console.warn('No se encontró el campo "nombre-reg" en el formulario o no se tiene el dataset "nombre" en el modal');
    }

    if (estadoSelect && modal.dataset.estado) {
        estadoSelect.value = modal.dataset.estado;
    } else {
        console.warn('No se encontró el campo "estado-reg" en el formulario o no se tiene el dataset "estado" en el modal');
    }

    modal.style.display = "block";
}


function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) {
        console.error(`El modal con ID ${modalId} no se encuentra en el DOM.`);
        return;
    }

    modal.style.display = "none";
}
