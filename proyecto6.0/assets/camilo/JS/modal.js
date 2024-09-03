const exampleModal = document.getElementById('exampleModal');
exampleModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget; // Botón que abre el modal
    const id = button.getAttribute('data-id'); // Obtener el ID del producto
    const nombre = button.getAttribute('data-nombre');
    const descripcion = button.getAttribute('data-descripcion');
    const precio = button.getAttribute('data-precio');
    const cantidad = button.getAttribute('data-cantidad');

    // Llenar los campos del modal
    document.getElementById('productId').value = id;
    document.getElementById('nombre').value = nombre;
    document.getElementById('descripcion').value = descripcion;
    document.getElementById('precio').value = precio;
    document.getElementById('cantidad').value = cantidad;
});