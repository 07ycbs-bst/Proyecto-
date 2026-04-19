let servicioActivo = null;

// CARGAR DATOS DEL SERVICIO (Imagen, Precio, Descripción)
async function inicializarPaginaServicio(nombreServicio) {
    console.log("Cargando servicio:", nombreServicio);
    try {
        const response = await fetch(`api/obtener_servicio_por_nombre.php?nombre=${nombreServicio}`);
        const resultado = await response.json();
        console.log(resultado.datos)
        if (resultado && resultado.datos) {
            const datos = resultado.datos;
            servicioActivo = nombreServicio; 

            // Rellenar la página con los datos de la BD
            if (document.getElementById('imagen-servicio')) {
                document.getElementById('imagen-servicio').src = `img/${datos.imagen_url}.jpg`;
            }
            if (document.getElementById('precio-servicio')) {
                document.getElementById('precio-servicio').innerText = datos.precio_base;
            }
            if (document.getElementById('descripcion-servicio')) {
                document.getElementById('descripcion-servicio').innerText = datos.descripcion;
            }

            // Cargar la tabla de registros
            cargarServiciosSolicitados(nombreServicio);
        } else {
            console.error("Error desde PHP:", resultado.msg);
            alert("No se pudieron cargar los datos: " + resultado.msg);
        }
    } catch (error) {
        console.error("Error crítico de conexión:", error);
    }
}

// CARGAR TABLA DE SERVICIOS SOLICITADOS
function cargarServiciosSolicitados(tipo) {
    const usuarioId = localStorage.getItem('usuario_id');
    const contenedor = document.getElementById('lista-servicios');

    if (!usuarioId) return;

    fetch(`api/obtener_servicios.php?tipo=${tipo}&cliente_id=${usuarioId}`)
    .then(res => res.json())
    .then(data => {
        if (!contenedor) return;
        contenedor.innerHTML = '';

        if (!Array.isArray(data) || data.length === 0) {
            contenedor.innerHTML = '<div class="alert alert-info text-center">Aún no tienes solicitudes en esta categoría.</div>';
            return;
        }

        // Usamos una sola columna (row-cols-1) para que el texto tenga todo el ancho disponible
        let html = '<div class="row row-cols-1 g-3">'; 

        data.forEach(s => {
            // Limpieza profunda de caracteres invisibles
            const descripcion = s.descripcion.replace(/[\r\n]/g, ' ').trim();
            const detalle = (s.detalle || '').replace(/[\r\n]/g, ' ').trim();
            const precio = parseFloat(s.precio).toLocaleString('es-VE', { minimumFractionDigits: 2 });

           // Dentro de la función cargarServiciosSolicitados, en el bucle data.forEach:
html += `
    <div class="col">
        <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-primary mb-0">${descripcion}</h5>
                    <span class="badge bg-light text-success fs-6 border border-success">$${precio}</span>
                </div>
                <p class="card-text text-dark mb-3">${detalle || 'Sin detalles.'}</p>
                
                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <button class="btn btn-outline-primary btn-sm" onclick="abrirModalComentarios(${s.id}, '${descripcion}')">
                        <i class="fa-regular fa-comments me-1"></i> Comentarios
                    </button>
                    <span class="text-muted small">${new Date(s.fecha).toLocaleDateString()}</span>
                </div>
            </div>
        </div>
    </div>`;

        });

        html += '</div>';
        contenedor.innerHTML = html;
    })
    .catch(err => console.error("Error cargando solicitudes:", err));
}




// GUARDAR NUEVA SOLICITUD
function guardarServicio() {
    const descripcion = document.getElementById('descripcion').value.trim();
    const precio = document.getElementById('precio').value.trim();
    const detalle = document.getElementById('detalle').value.trim();

    if (!descripcion || !precio) {
        alert('Complete los campos obligatorios');
        return;
    }

    fetch('api/guardar_servicio.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            tipo_servicio: servicioActivo,
            descripcion: descripcion,
            detalle: detalle,
            precio: precio
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.ok) {
            alert("Guardado correctamente");
            bootstrap.Modal.getInstance(document.getElementById('subModal')).hide();
            cargarServiciosSolicitados(servicioActivo);
        } else {
            alert('Error: ' + data.msg);
        }
    });
}

function abrirSubModal() {
    new bootstrap.Modal(document.getElementById('subModal')).show();
}




let idServicioActual = null;

function abrirModalComentarios(idServicio, titulo) {
    idServicioActual = idServicio;
    document.getElementById('tituloModalComentarios').innerText = "Comentarios: " + titulo;
    
    // Limpiar el campo de texto
    document.getElementById('nuevo-comentario').value = '';
    
    // Cargar comentarios
    cargarComentarios(idServicio);
    
    // Mostrar modal
    new bootstrap.Modal(document.getElementById('modalComentarios')).show();
}

function cargarComentarios(idServicio) {
    const contenedor = document.getElementById('lista-comentarios');
    contenedor.innerHTML = '<div class="text-center p-3"><div class="spinner-border spinner-border-sm text-primary"></div></div>';

    fetch(`api/obtener_comentarios.php?servicio_id=${idServicio}`)
    .then(res => res.json())
    .then(data => {
        contenedor.innerHTML = '';
        if (data.length === 0) {
            contenedor.innerHTML = '<p class="text-muted text-center py-4">No hay mensajes aún. ¡Sé el primero!</p>';
            return;
        }
        console.log(data)
        data.forEach(c => {
            // Estilo de burbuja para que la información se vea completa y clara
            contenedor.innerHTML += `
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-bold small text-primary">${c.nombre_usuario}</span>
                        <span class="text-muted" style="font-size: 0.7rem;">${new Date(c.fecha).toLocaleString()}</span>
                    </div>
                    <div class="p-2 bg-light rounded shadow-sm border-start border-3 border-primary">
                        <p class="mb-0 small text-dark" style="white-space: pre-wrap;">${c.comentario}</p>
                    </div>
                </div>`;
        });
        // Auto-scroll al final para ver el último comentario
        contenedor.scrollTop = contenedor.scrollHeight;
    });
}

document.getElementById('btnGuardarComentario').onclick = function() {
    const texto = document.getElementById('nuevo-comentario').value.trim();
    const usuarioId = localStorage.getItem('usuario_id'); //

    if (!texto) return alert("El comentario no puede estar vacío");
    console.log(idServicioActual);
    fetch('api/guardar_comentario.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            servicio_id: idServicioActual,
            cliente_id: usuarioId,
            comentario: texto
        })
    })
    .then(res => res.json())
    .then(res => {
        if (res.ok) {
            document.getElementById('nuevo-comentario').value = '';
            cargarComentarios(idServicioActual);
        } else {
            alert("Error: " + res.msg);
        }
    });
};