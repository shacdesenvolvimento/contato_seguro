<?php

// Incluir a conexao com o banco de dados
include_once './conexao.php';

//Receber os dados da requisão
$dados_requisicao = $_REQUEST;

// Lista de colunas da tabela
$colunas = [
    0 => 'id',
    1 => 'nome',
    2 => 'salario',
    3 => 'idade'
];

// Obter a quantidade de registros no banco de dados
$query_qnt_usuarios = "SELECT COUNT(id) AS qnt_usuarios FROM usuarios";

// Acessa o IF quando ha paramentros de pesquisa   
if(!empty($dados_requisicao['search']['value'])) {
    $query_qnt_usuarios .= " WHERE id LIKE :id ";
    $query_qnt_usuarios .= " OR nome LIKE :nome ";
    $query_qnt_usuarios .= " OR salario LIKE :salario ";
    $query_qnt_usuarios .= " OR idade LIKE :idade ";
}
// Preparar a QUERY
$result_qnt_usuarios = $conn->prepare($query_qnt_usuarios);
// Acessa o IF quando ha paramentros de pesquisa   
if(!empty($dados_requisicao['search']['value'])) {
    $valor_pesq = "%" . $dados_requisicao['search']['value'] . "%";
    $result_qnt_usuarios->bindParam(':id', $valor_pesq, PDO::PARAM_STR);
    $result_qnt_usuarios->bindParam(':nome', $valor_pesq, PDO::PARAM_STR);
    $result_qnt_usuarios->bindParam(':salario', $valor_pesq, PDO::PARAM_STR);
    $result_qnt_usuarios->bindParam(':idade', $valor_pesq, PDO::PARAM_STR);
}
// Executar a QUERY responsável em retornar a quantidade de registros no banco de dados
$result_qnt_usuarios->execute();
$row_qnt_usuarios = $result_qnt_usuarios->fetch(PDO::FETCH_ASSOC);
//var_dump($row_qnt_usuarios);

// Recuperar os registros do banco de dados
$query_usuarios = "SELECT id, nome, salario, idade 
                    FROM usuarios ";

// Acessa o IF quando ha paramentros de pesquisa   
if(!empty($dados_requisicao['search']['value'])) {
    $query_usuarios .= " WHERE id LIKE :id ";
    $query_usuarios .= " OR nome LIKE :nome ";
    $query_usuarios .= " OR salario LIKE :salario ";
    $query_usuarios .= " OR idade LIKE :idade ";
}

// Ordenar os registros
$query_usuarios .= " ORDER BY " . $colunas[$dados_requisicao['order'][0]['column']] . " " . $dados_requisicao['order'][0]['dir'] . " LIMIT :inicio , :quantidade";

// Preparar a QUERY
$result_usuarios = $conn->prepare($query_usuarios);
$result_usuarios->bindParam(':inicio', $dados_requisicao['start'], PDO::PARAM_INT);
$result_usuarios->bindParam(':quantidade', $dados_requisicao['length'], PDO::PARAM_INT);

// Acessa o IF quando ha paramentros de pesquisa   
if(!empty($dados_requisicao['search']['value'])) {
    $valor_pesq = "%" . $dados_requisicao['search']['value'] . "%";
    $result_usuarios->bindParam(':id', $valor_pesq, PDO::PARAM_STR);
    $result_usuarios->bindParam(':nome', $valor_pesq, PDO::PARAM_STR);
    $result_usuarios->bindParam(':salario', $valor_pesq, PDO::PARAM_STR);
    $result_usuarios->bindParam(':idade', $valor_pesq, PDO::PARAM_STR);
}
// Executar a QUERY
$result_usuarios->execute();

// Ler os registros retornado do banco de dados e atribuir no array 
while ($row_usuario = $result_usuarios->fetch(PDO::FETCH_ASSOC)) {
    extract($row_usuario);
    $registro = [];
    $registro[] = $id;
    $registro[] = $nome;
    $registro[] = $salario;
    $registro[] = $idade;
    $registro[] = "<button type='button' id='$id' class='btn btn-outline-primary btn-sm' onclick='visUsuario($id)'>Visualizar</button> <button type='button' id='$id' class='btn btn-outline-warning btn-sm' onclick='editUsuario($id)'>Editar</button> <button type='button' id='$id' class='btn btn-outline-danger btn-sm' onclick='apagarUsuario($id)'>Apagar</button>";
    $dados[] = $registro;
}

//Cria o array de informações a serem retornadas para o Javascript
$resultado = [
    "draw" => intval($dados_requisicao['draw']), // Para cada requisição é enviado um número como parâmetro
    "recordsTotal" => intval($row_qnt_usuarios['qnt_usuarios']), // Quantidade de registros que há no banco de dados
    "recordsFiltered" => intval($row_qnt_usuarios['qnt_usuarios']), // Total de registros quando houver pesquisa
    "data" => $dados // Array de dados com os registros retornados da tabela usuarios
];

// Retornar os dados em formato de objeto para o JavaScript
echo json_encode($resultado);
