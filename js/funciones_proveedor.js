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
    var rutInput = form.querySelector('input[name="rut-reg"]');

    // Verifica si los elementos existen antes de asignarles valores
    if (nombreInput && modal.dataset.nombre) {
        nombreInput.value = modal.dataset.nombre || '';
    } else {
        console.warn('No se encontró el campo "nombre-reg" o "nombre" en el dataset');
    }

    if (rutInput && modal.dataset.rut) {
        rutInput.value = modal.dataset.rut || '';
    } else {
        console.warn('No se encontró el campo "rut-reg" o "rut" en el dataset');
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

