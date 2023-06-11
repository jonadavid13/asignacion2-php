
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
                    document.getElementById("span-mujeres").innerHTML = `<span>${resp[2].totalMujeres}</span>`;
                    document.getElementById("span-hombres").innerHTML = `<span>${resp[2].hombresCasados2500}</span>`;
                    document.getElementById("span-viudas").innerHTML = `<span>${resp[2].viudasMas1000}</span>`;
                    document.getElementById("span-promedio-hombres").innerHTML = `<span>${resp[2].edadesPromedioHombres}</span>`;
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
                            <th scope="col">Nombre y Apellido</th>
                            <th scope="col">Edad</th>
                            <th scope="col">Estado Civil</th>
                            <th scope="col">Sexo</th>
                            <th scope="col">Sueldo</th>
                        </tr>
                    </thead>
    <tbody>`;    
    array.forEach(empleado => {
        cantRegistros += 1;
        table += `<tr>
            <th scope="row">${cantRegistros}</th>
            <td>${empleado.nombre}</td>
            <td>${empleado.edad}</td>
            <td>${empleado.estadoCivil}</td>
            <td>${empleado.sexo}</td>
            <td>${empleado.sueldo}</td>
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