function openModal(modalId) {
    var modal = document.getElementById(modalId);
    var form = modal.querySelector('form');
    var nombreInput = form.querySelector('input[name="nombre-reg"]');
    var estadoInput = form.querySelector('select[name="estado-reg"]');
    
    // Asigna el valor al campo de nombre y estado (ajusta esto según cómo recibas los valores)
    nombreInput.value = modal.dataset.nombreCargo || '';


    modal.style.display = "block";
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = "none";
}

// Cierra el modal si el usuario hace clic fuera de él
window.onclick = function(event) {
    var modals = document.getElementsByClassName('custom-modal');
    for (var i = 0; i < modals.length; i++) {
        if (event.target == modals[i]) {
            modals[i].style.display = "none";
        }
    }
}


function openErrorModal(modalId) {
    document.getElementById(modalId).style.display = "block";
}

function closeErrorModal(modalId) {
    document.getElementById(modalId).style.display = "none";
}

// Si hay un mensaje de error en la URL, abre el modal automáticamente
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error')) {
        openErrorModal('errorModal'); // Usamos el nombre único 'errorModal'
    }
};
