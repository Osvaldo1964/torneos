const api_url = app_config.api_url;
const token = app_config.token;

document.addEventListener('DOMContentLoaded', () => {
    fntLoadTorneos();

    document.getElementById('selectTorneoNomina').addEventListener('change', (e) => {
        const idTorneo = e.target.value;
        if (idTorneo) {
            fntLoadEquiposInscritos(idTorneo);
            document.getElementById('selectEquipoNomina').disabled = false;
        } else {
            document.getElementById('selectEquipoNomina').disabled = true;
            document.getElementById('containerNomina').classList.add('d-none');
            document.getElementById('placeholderNomina').classList.remove('d-none');
        }
    });

    document.getElementById('selectEquipoNomina').addEventListener('change', (e) => {
        const idEquipo = e.target.value;
        const idTorneo = document.getElementById('selectTorneoNomina').value;
        if (idEquipo) {
            document.getElementById('containerNomina').classList.remove('d-none');
            document.getElementById('placeholderNomina').classList.add('d-none');
            fntLoadNominaReal(idTorneo, idEquipo);
        } else {
            document.getElementById('containerNomina').classList.add('d-none');
            document.getElementById('placeholderNomina').classList.remove('d-none');
        }
    });
});

async function fntLoadTorneos() {
    try {
        const response = await fetch(`${api_url}Torneos/getTorneos`, {
            headers: { "Authorization": "Bearer " + token }
        });
        const result = await response.json();
        let html = '<option value="">Seleccione Torneo</option>';
        result.data.forEach(t => {
            html += `<option value="${t.id_torneo}">${t.nombre} (${t.categoria})</option>`;
        });
        document.getElementById('selectTorneoNomina').innerHTML = html;
    } catch (error) { }
}

async function fntLoadEquiposInscritos(idTorneo) {
    try {
        const response = await fetch(`${api_url}Torneos/getInscritos/${idTorneo}`, {
            headers: { "Authorization": "Bearer " + token }
        });
        const result = await response.json();
        let html = '<option value="">Seleccione Equipo</option>';
        result.data.forEach(e => {
            html += `<option value="${e.id_equipo}">${e.nombre}</option>`;
        });
        document.getElementById('selectEquipoNomina').innerHTML = html;
    } catch (error) { }
}

async function fntLoadNominaReal(idTorneo, idEquipo) {
    try {
        console.log("Cargando nómina para Torneo:", idTorneo, "Equipo:", idEquipo);

        // Disponibles para ESTE torneo (que no estén en ningún equipo de este torneo)
        const resDisp = await fetch(`${api_url}Jugadores/getDisponiblesNomina/${idTorneo}`, {
            headers: { "Authorization": "Bearer " + token }
        });
        const disp = await resDisp.json();
        console.log("Disponibles:", disp);

        // Nómina de este equipo para este torneo
        const resInsc = await fetch(`${api_url}Jugadores/getNomina/${idTorneo}/${idEquipo}`, {
            headers: { "Authorization": "Bearer " + token }
        });
        const insc = await resInsc.json();
        console.log("Inscritos:", insc);

        let htmlDisp = "";
        if (disp.status && Array.isArray(disp.data)) {
            disp.data.forEach(p => {
                htmlDisp += `
                    <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                        <div class="d-flex align-items-center">
                            <img src="assets/images/jugadores/${p.foto}" class="rounded-circle me-3" style="width:40px; height:40px; object-fit:cover;">
                            <div>
                                <p class="mb-0 fw-bold text-dark">${p.nombres} ${p.apellidos}</p>
                                <span class="text-muted small">Disponible para vincular</span>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-primary border-0" onclick="fntInscribirEnNomina(${p.id_jugador})" title="Vincular a Nómina">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                `;
            });
        }
        document.getElementById('listDisponiblesNomina').innerHTML = htmlDisp || '<div class="p-4 text-center text-muted">No hay jugadores disponibles.</div>';

        let htmlOficial = "";
        if (insc.status && Array.isArray(insc.data)) {
            insc.data.forEach(p => {
                htmlOficial += `
                    <div class="list-group-item d-flex justify-content-between align-items-center p-3 border-start border-primary border-4">
                        <div class="d-flex align-items-center">
                            <img src="assets/images/jugadores/${p.foto}" class="rounded-circle me-3" style="width:40px; height:40px; object-fit:cover;">
                            <div>
                                <p class="mb-0 fw-bold text-dark">${p.nombres} ${p.apellidos}</p>
                                <span class="badge bg-light text-primary border border-primary-subtle">Dorsal: ${p.dorsal || 'N/A'}</span>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-outline-danger border-0" onclick="fntRetirarDeNomina(${p.id_jugador})" title="Retirar de Nómina">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                `;
            });
        }
        document.getElementById('listOficialNomina').innerHTML = htmlOficial || '<div class="p-4 text-center text-muted">Nómina vacía. Vincule jugadores desde la izquierda.</div>';

    } catch (error) {
        console.error("Error cargando nómina:", error);
        Swal.fire("Error", "Error al cargar los datos de la nómina", "error");
    }
}

async function fntInscribirEnNomina(idJugador) {
    const { value: dorsal } = await Swal.fire({
        title: 'Asignar Dorsal',
        input: 'number',
        inputLabel: 'Número de camiseta para este torneo',
        inputValue: '',
        showCancelButton: true,
        inputValidator: (value) => {
            if (!value) return 'Debes asignar un número de dorsal';
        }
    });

    if (dorsal) {
        const idTorneo = document.getElementById('selectTorneoNomina').value;
        const idEquipo = document.getElementById('selectEquipoNomina').value;

        try {
            const response = await fetch(`${api_url}Jugadores/setNomina`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify({
                    id_torneo: idTorneo,
                    id_equipo: idEquipo,
                    id_jugador: idJugador,
                    dorsal: dorsal
                })
            });
            const result = await response.json();
            if (result.status) {
                fntLoadNominaReal(idTorneo, idEquipo);
            } else {
                Swal.fire("Error", result.msg, "error");
            }
        } catch (error) { }
    }
}

async function fntRetirarDeNomina(idJugador) {
    const idTorneo = document.getElementById('selectTorneoNomina').value;
    const idEquipo = document.getElementById('selectEquipoNomina').value;

    try {
        const response = await fetch(`${api_url}Jugadores/delNomina`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify({
                id_torneo: idTorneo,
                id_equipo: idEquipo,
                id_jugador: idJugador
            })
        });
        const result = await response.json();
        if (result.status) {
            fntLoadNominaReal(idTorneo, idEquipo);
        }
    } catch (error) { }
}
