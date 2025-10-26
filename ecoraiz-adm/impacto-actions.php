<?php
header('Content-Type: application/json');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Ativa erros detalhados

$conn = new mysqli("localhost", "root", "", "ecoraiz");
$conn->set_charset("utf8");

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {

    if ($action == 'save') {
        $id = $_POST['id_impacto'] ?? '';
        $qtd = $_POST['qtd_fertilizante_gerado'] ?? null;
        $medida = $_POST['medida_impacto'] ?? '';
        $descricao = $_POST['descricao_impacto'] ?? '';
        $id_func = $_POST['id_funcionario'] ?? null;
        $id_doacao = $_POST['id_doacao'] ?? null;

        // Validação básica
        if ($qtd === null || $medida === '' || $descricao === '' || $id_func === null || $id_doacao === null) {
            throw new Exception("Preencha todos os campos obrigatórios.");
        }

        $data_atual = date('Y-m-d');

        if ($id) { // Atualizar
            $stmt = $conn->prepare("
                UPDATE impacto 
                SET qtd_fertilizante_gerado=?, medida_impacto=?, descricao_impacto=?, data=?, id_funcionario=?, id_doacao=? 
                WHERE id_impacto=?
            ");
            $stmt->bind_param("dsssiii", $qtd, $medida, $descricao, $data_atual, $id_func, $id_doacao, $id);
            if(!$stmt->execute()){
                throw new Exception($stmt->error);
            }
            echo json_encode(['success' => true, 'message' => 'Impacto atualizado com sucesso.']);
        } else { // Inserir
            $stmt = $conn->prepare("
                INSERT INTO impacto (qtd_fertilizante_gerado, medida_impacto, descricao_impacto, data, id_funcionario, id_doacao) 
                VALUES (?,?,?,?,?,?)
            ");
            $stmt->bind_param("dsssii", $qtd, $medida, $descricao, $data_atual, $id_func, $id_doacao);
            if(!$stmt->execute()){
                throw new Exception($stmt->error);
            }
            echo json_encode(['success' => true, 'message' => 'Impacto cadastrado com sucesso.']);
        }
        exit;
    }

    if ($action == 'delete') {
        $id = $_POST['id_impacto'] ?? null;
        if(!$id) throw new Exception("ID inválido.");

        $stmt = $conn->prepare("DELETE FROM impacto WHERE id_impacto=?");
        $stmt->bind_param("i", $id);
        if(!$stmt->execute()){
            throw new Exception($stmt->error);
        }
        echo json_encode(['success' => true, 'message' => 'Impacto excluído com sucesso.']);
        exit;
    }

    if ($action == 'get') {
        $id = $_GET['id_impacto'] ?? null;
        if(!$id) throw new Exception("ID inválido.");

        $stmt = $conn->prepare("SELECT * FROM impacto WHERE id_impacto=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        echo json_encode($data ?: []);
        exit;
    }

    throw new Exception("Ação inválida.");

} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();
