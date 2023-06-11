
function verificarDatos(){
    document.getElementById("alertDiv").innerHTML = "";

    let nombre = document.getElementById("idNombre").value;
    let cedula = document.getElementById("idCedula").value;
    let notaMatematicas = document.getElementById("idMath").value;
    let notaFisica = document.getElementById("idFisica").value;
    let notaProgramacion = document.getElementById("idProgramacion").value;

    if(nombre == "" || cedula == "" || notaMatematicas == "" || notaFisica == "" || notaProgramacion == ""){
        document.getElementById("alertForm").innerHTML = `<div class="alert alert-warning" role="alert">
            <span>Todos los campos deben estar completados.</span>
        </div>`;
    } else if( (notaMatematicas < 0 || notaMatematicas > 20 ) || (notaFisica < 0 || notaFisica > 20 ) || (notaProgramacion < 0 || notaProgramacion > 20 )){
        document.getElementById("alertForm").innerHTML = `<div class="alert alert-danger" role="alert">
        <span>Las notas deben tener un valor entre 0 y 20</span>
        </div>`;
    } else {
        document.getElementById("alertForm").innerHTML = "";

        let params = {
            'Nombre': nombre,
            'Cedula': cedula,
            'notaMatematicas': notaMatematicas,
            'notaFisica': notaFisica,
            'notaProgramacion': notaProgramacion
        };

        let data = JSON.stringify(params);

        console.log(params);
        $.ajax({
            type: "POST",
            url: "assets/php/insertar.php",
            dataType: "json",
            data: data, 
            beforeSend: function (){
                console.log("Enviando datos al Backend")
            },
            success: function(resp){
                console.log(resp);

                if(resp[0].status == "success"){
                    document.getElementById("alertDiv").innerHTML = `<div class="alert alert-success d-flex justify-content-between" role="alert">
                    <span>${resp[0].message}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`;

                    document.getElementById("RegistroForm").reset();
                    
                    // Ejecutar función de actualización de registros en la tabla
                    actualizarRegistros(resp[1]);

                    // Actualizar tarjeta de información solicitada
                    document.getElementById("promedioMath").innerHTML = `<span>${resp[2].promedioMatematicas}</span>`;
                    document.getElementById("aprobadosMath").innerHTML = `<span>${resp[2].aprobadosMatematicas}</span>`;
                    document.getElementById("aplazadosMath").innerHTML = `<span>${resp[2].aplazadosMatematicas}</span>`;
                    document.getElementById("notaMaxMath").innerHTML = `<span>${resp[2].notaMaxMatematicas}</span>`;
                    
                    document.getElementById("promedioFisica").innerHTML = `<span>${resp[2].promedioFisica}</span>`;
                    document.getElementById("aprobadosFisica").innerHTML = `<span>${resp[2].aprobadosFisica}</span>`;
                    document.getElementById("aplazadosFisica").innerHTML = `<span>${resp[2].aplazadosFisica}</span>`;
                    document.getElementById("notaMaxFisica").innerHTML = `<span>${resp[2].notaMaxFisica}</span>`;
                    
                    document.getElementById("promedioProgramacion").innerHTML = `<span>${resp[2].promedioProgramacion}</span>`;
                    document.getElementById("aprobadosProgramacion").innerHTML = `<span>${resp[2].aprobadosProgramacion}</span>`;
                    document.getElementById("aplazadosProgramacion").innerHTML = `<span>${resp[2].aplazadosProg}</span>`;
                    document.getElementById("notaMaxProgramacion").innerHTML = `<span>${resp[2].notaMaxProgramacion}</span>`;
                
                    document.getElementById("aprobadosTodas").innerHTML = `<span>${resp[2].aprobaronTodas}</span>`;
                    document.getElementById("aprobadosUna").innerHTML = `<span>${resp[2].aprobaronUna}</span>`;
                    document.getElementById("aprobadosDos").innerHTML = `<span>${resp[2].aprobaronDos}</span>`;
                }
                if(resp[0].status == "error"){
                    document.getElementById("alertDiv").innerHTML = `<div class="alert alert-danger d-flex justify-content-between" role="alert">
                    <span>${resp[0].message}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`;
                }
            },
            fail: function (jqXHR, textStatus, errorThown) {
                console.log("Fail AJAX", textStatus, errorThown);
            },
            error: function (jqXHR, textStatus, errorThown) {
                console.log("Error AJAX",textStatus, errorThown);
            }
        });
    }
};

function actualizarRegistros(array){
    let cantRegistros = 0;
    let table = `<table class="tabla-registros table table-striped table-hover">
                    <thead class="table-header">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">N° de Cédula</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Nota de Matemáticas</th>
                        <th scope="col">Nota de Física</th>
                        <th scope="col">Nota de Programación</th>
                    </tr>
                    </thead>
    <tbody>`;    
    array.forEach(estudiante => {
        cantRegistros += 1;
        table += `<tr>
            <th scope="row">${cantRegistros}</th>
            <td>${estudiante.cedula}</td>
            <td>${estudiante.nombre}</td>
            <td>${estudiante.notaMatematicas}</td>
            <td>${estudiante.notaFisica}</td>
            <td>${estudiante.notaProgramacion}</td>
        </tr>`;
    });
    table += `</tbody>
    </table>`;

    $("#tablaRegistros").html(table);
};

function limpiarAlerta(){
    document.getElementById("alertDiv").innerHTML = "";
};

function resetDatos(){
    $.ajax({
        type: "POST",
        url: "assets/php/reset.php",
        dataType: "text",
        data: "true",
        beforeSend: function(){
            console.log("Procesando solicitud para eliminar registros")
        },
        success: function(resp){
            console.log(resp);
        },
        fail: function(jqXHR, textStatus, errorThown){
            console.log("Fail", textStatus, errorThown);
        },
        error: function(jqXHR, textStatus, errorThown){
            console.log("Error", textStatus, errorThown);
        },
    });
}