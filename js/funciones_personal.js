function openModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal === null) return; // El modal no existe, no hacemos nada

    var form = modal.querySelector('form');
    
    // Obtener los campos de entrada del formulario
    var dniInput = form.querySelector('input[name="dni-reg"]');
    var nombreInput = form.querySelector('input[name="nombre-reg"]');
    var apellidoPaternoInput = form.querySelector('input[name="apellidopaterno-reg"]');
    var apellidoMaternoInput = form.querySelector('input[name="apellidomaterno-reg"]');
    var celularInput = form.querySelector('input[name="celular-reg"]');
    var direccionInput = form.querySelector('input[name="direccion-reg"]');
    var cargoInput = form.querySelector('select[name="idcargo-reg"]');
    var estadoInput = form.querySelector('select[name="estado-reg"]');
    
    // Asignar los valores a los campos de formulario usando los atributos data-* del modal
    if (dniInput !== null) dniInput.value = modal.dataset.dniPersonal || '';
    if (nombreInput !== null) nombreInput.value = modal.dataset.nombrePersonal || '';
    if (apellidoPaternoInput !== null) apellidoPaternoInput.value = modal.dataset.apellidoPaternoPersonal || '';
    if (apellidoMaternoInput !== null) apellidoMaternoInput.value = modal.dataset.apellidoMaternoPersonal || '';
    if (celularInput !== null) celularInput.value = modal.dataset.celularPersonal || '';
    if (direccionInput !== null) direccionInput.value = modal.dataset.direccionPersonal || '';
    if (cargoInput !== null) cargoInput.value = modal.dataset.idcargoPersonal || '';
    if (estadoInput !== null) {
        for (var i = 0; i < estadoInput.options.length; i++) {
            if (estadoInput.options[i].value === modal.dataset.estadoPersonal) {
                estadoInput.options[i].selected = true;
                break;
            }
        }
    }
    // Mostrar el modal
    modal.style.display = "block";
}

