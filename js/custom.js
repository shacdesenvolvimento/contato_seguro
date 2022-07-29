$(document).ready(function() {
    $('#listar-usuario').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "listar_usuarios.php",
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json"
        }
    });
});

const formNewUser = document.getElementById("form-cad-usuario");
const fecharModalCad = new bootstrap.Modal(document.getElementById("cadUsuarioModal"));
if (formNewUser) {
    formNewUser.addEventListener("submit", async(e) => {
        e.preventDefault();
        const dadosForm = new FormData(formNewUser);
        //console.log(dadosForm);

        const dados = await fetch("cadastrar.php", {
            method: "POST",
            body: dadosForm
        });
        const resposta = await dados.json();
        //console.log(resposta);

        if (resposta['status']) {
            document.getElementById("msgAlertErroCad").innerHTML = "";
            document.getElementById("msgAlerta").innerHTML = resposta['msg'];
            formNewUser.reset();
            fecharModalCad.hide();
            listarDataTables = $('#listar-usuario').DataTable();
            listarDataTables.draw();
        } else {
            document.getElementById("msgAlertErroCad").innerHTML = resposta['msg'];
        }
    });
}

async function visUsuario(id) {
    //console.log("Acessou: " + id);
    const dados = await fetch('visualizar.php?id=' + id);
    const resposta = await dados.json();
    //console.log(resposta);

    if (resposta['status']) {
        const visModal = new bootstrap.Modal(document.getElementById("visUsuarioModal"));
        visModal.show();

        document.getElementById("idUsuario").innerHTML = resposta['dados'].id;
        document.getElementById("nomeUsuario").innerHTML = resposta['dados'].nome;
        document.getElementById("salarioUsuario").innerHTML = resposta['dados'].salario;
        document.getElementById("idadeUsuario").innerHTML = resposta['dados'].idade;

        document.getElementById("msgAlerta").innerHTML = "";
    } else {
        document.getElementById("msgAlerta").innerHTML = resposta['msg'];
    }
}

const editModal = new bootstrap.Modal(document.getElementById("editUsuarioModal"));
async function editUsuario(id) {
    //console.log("Editar: " + id);

    const dados = await fetch('visualizar.php?id=' + id);
    const resposta = await dados.json();
    //console.log(resposta);

    if (resposta['status']) {
        document.getElementById("msgAlertErroEdit").innerHTML = "";

        document.getElementById("msgAlerta").innerHTML = "";
        editModal.show();

        document.getElementById("editid").value = resposta['dados'].id;
        document.getElementById("editnome").value = resposta['dados'].nome;
        document.getElementById("editsalario").value = resposta['dados'].salario;
        document.getElementById("editidade").value = resposta['dados'].idade;
    } else {
        document.getElementById("msgAlerta").innerHTML = resposta['msg'];
    }
}

const formEditUser = document.getElementById("form-edit-usuario");
if (formEditUser) {
    formEditUser.addEventListener("submit", async(e) => {
        e.preventDefault();
        const dadosForm = new FormData(formEditUser);

        const dados = await fetch("editar.php", {
            method: "POST",
            body: dadosForm
        });

        const resposta = await dados.json();

        if (resposta['status']) {
            // Manter a janela modal aberta
            //document.getElementById("msgAlertErroEdit").innerHTML = resposta['msg'];

            // Fechar a janela modal
            document.getElementById("msgAlerta").innerHTML = resposta['msg'];
            document.getElementById("msgAlertErroEdit").innerHTML = "";
            // Limpar o formulario
            formEditUser.reset();
            editModal.hide();

            // Atualizar a lista de registros
            listarDataTables = $('#listar-usuario').DataTable();
            listarDataTables.draw();
        } else {
            document.getElementById("msgAlertErroEdit").innerHTML = resposta['msg'];
        }
    });
}

async function apagarUsuario(id) {
    //console.log("Acessou: " + id);

    var confirmar = confirm("Tem certeza que deseja excluir o registro selecionado?");

    if (confirmar) {
        const dados = await fetch("apagar.php?id=" + id);
        const resposta = await dados.json();
        //console.log(resposta);

        if (resposta['status']) {
            document.getElementById("msgAlerta").innerHTML = resposta['msg'];

            // Atualizar a lista de registros
            listarDataTables = $('#listar-usuario').DataTable();
            listarDataTables.draw();
        } else {
            document.getElementById("msgAlerta").innerHTML = resposta['msg'];
        }
    }
}