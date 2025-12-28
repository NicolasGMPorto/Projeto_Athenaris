<?php
session_start();
header('Content-Type: application/json');

// Helper: tenta garantir uma conexão mysqli ($conn) ou PDO ($pdo)
function ensure_db_connection() {
    // já incluído?
    if (isset($GLOBALS['conn']) && $GLOBALS['conn'] instanceof mysqli) return ['type'=>'mysqli','handle'=>$GLOBALS['conn']];
    if (isset($GLOBALS['mysqli']) && $GLOBALS['mysqli'] instanceof mysqli) { $GLOBALS['conn'] = $GLOBALS['mysqli']; return ['type'=>'mysqli','handle'=>$GLOBALS['conn']]; }
    if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) return ['type'=>'pdo','handle'=>$GLOBALS['pdo']];

    // tentar incluir conexao.php se existir
    if (file_exists(__DIR__ . '/conexao.php')) {
        require_once __DIR__ . '/conexao.php';
        if (isset($conn) && $conn instanceof mysqli) return ['type'=>'mysqli','handle'=>$conn];
        if (isset($mysqli) && $mysqli instanceof mysqli) { $conn = $mysqli; return ['type'=>'mysqli','handle'=>$conn]; }
        if (isset($pdo) && $pdo instanceof PDO) return ['type'=>'pdo','handle'=>$pdo];
    }

    // fallback automático (Laragon / XAMPP padrão)
    $host = '127.0.0.1';
    $user = 'root';
    $pass = '';
    $db   = 'athenaris_db';
    $m = @new mysqli($host,$user,$pass,$db);
    if (!$m->connect_error) {
        $GLOBALS['conn'] = $m;
        return ['type'=>'mysqli','handle'=>$m];
    }

    return null;
}

$db = ensure_db_connection();
if (!$db) {
    http_response_code(500);
    echo json_encode(['success'=>false,'msg'=>'Não foi possível conectar ao banco. Verifique conexao.php ou as credenciais.']);
    exit;
}

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['success'=>false,'msg'=>'Usuário não autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success'=>false,'msg'=>'Método inválido']);
    exit;
}

// aceitar JSON ou form-urlencoded
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

$usuario_id = (int) $_SESSION['usuario_id'];
$curso = isset($input['curso']) ? trim($input['curso']) : '';
$topico = isset($input['topico']) ? (int)$input['topico'] : 0;
$concluido = isset($input['concluido']) ? (int)$input['concluido'] : 0;

if ($curso === '' || $topico <= 0) {
    http_response_code(400);
    echo json_encode(['success'=>false,'msg'=>'Dados inválidos']);
    exit;
}

// Usar mysqli ou pdo conforme disponível
if ($db['type'] === 'mysqli') {
    /** @var mysqli $conn */
    $conn = $db['handle'];
    // checar se existe
    $stmt = $conn->prepare("SELECT id FROM progresso_cursos WHERE usuario_id = ? AND curso = ? AND topico = ?");
    $stmt->bind_param('isi',$usuario_id,$curso,$topico);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        $upd = $conn->prepare("UPDATE progresso_cursos SET concluido = ?, atualizado_em = NOW() WHERE usuario_id = ? AND curso = ? AND topico = ?");
        $upd->bind_param('iisi',$concluido,$usuario_id,$curso,$topico);
        $ok = $upd->execute();
        $upd->close();
        echo json_encode(['success' => (bool)$ok]);
        exit;
    } else {
        $stmt->close();
        $ins = $conn->prepare("INSERT INTO progresso_cursos (usuario_id, curso, topico, concluido) VALUES (?,?,?,?)");
        $ins->bind_param('isii',$usuario_id,$curso,$topico,$concluido);
        $ok = $ins->execute();
        $ins->close();
        echo json_encode(['success' => (bool)$ok]);
        exit;
    }
} else {
    /** @var PDO $pdo */
    $pdo = $db['handle'];
    $q = $pdo->prepare("SELECT id FROM progresso_cursos WHERE usuario_id = ? AND curso = ? AND topico = ?");
    $q->execute([$usuario_id,$curso,$topico]);
    $row = $q->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $u = $pdo->prepare("UPDATE progresso_cursos SET concluido = ?, atualizado_em = NOW() WHERE usuario_id = ? AND curso = ? AND topico = ?");
        $ok = $u->execute([$concluido,$usuario_id,$curso,$topico]);
        echo json_encode(['success' => (bool)$ok]);
        exit;
    } else {
        $i = $pdo->prepare("INSERT INTO progresso_cursos (usuario_id, curso, topico, concluido) VALUES (?,?,?,?)");
        $ok = $i->execute([$usuario_id,$curso,$topico,$concluido]);
        echo json_encode(['success' => (bool)$ok]);
        exit;
    }
}