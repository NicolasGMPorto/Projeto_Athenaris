<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$nome_usuario = $_SESSION['nome_usuario'] ?? 'Usuário';
$foto_usuario = $_SESSION['foto_usuario'] ?? 'imagens/perfil_icone.webp';

// Excluir transação
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $stmt = $conexao->prepare("DELETE FROM transacoes WHERE id = :id AND usuario_id = :usuario_id");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->execute();
    header("Location: orcamento.php");
    exit;
}

// Buscar transação para edição
$editando = null;
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $stmt = $conexao->prepare("SELECT * FROM transacoes WHERE id = :id AND usuario_id = :usuario_id");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->execute();
    $editando = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Inserir ou atualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $valor = floatval($_POST['valor'] ?? 0);
    $tipo = $_POST['tipo'] ?? '';

    if ($nome !== '' && $valor > 0 && in_array($tipo, ['receita', 'despesa'])) {
        if (!empty($_POST['id_edicao'])) {
            // Atualizar existente
            $stmt = $conexao->prepare("UPDATE transacoes SET nome = :nome, valor = :valor, tipo = :tipo WHERE id = :id AND usuario_id = :usuario_id");
            $stmt->bindParam(':id', $_POST['id_edicao']);
        } else {
            // Inserir novo
            $stmt = $conexao->prepare("INSERT INTO transacoes (usuario_id, nome, valor, tipo) VALUES (:usuario_id, :nome, :valor, :tipo)");
        }
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':valor', $valor);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->execute();
        header("Location: orcamento.php");
        exit;
    }
}

// Buscar todas as transações
$stmt = $conexao->prepare("SELECT * FROM transacoes WHERE usuario_id = :id ORDER BY data_registro DESC");
$stmt->bindParam(':id', $usuario_id);
$stmt->execute();
$transacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_receitas = array_sum(array_column(array_filter($transacoes, fn($t) => $t['tipo'] === 'receita'), 'valor'));
$total_despesas = array_sum(array_column(array_filter($transacoes, fn($t) => $t['tipo'] === 'despesa'), 'valor'));
$saldo = $total_receitas - $total_despesas;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orçamento</title>
    <link rel="stylesheet" href="CSS/home.css">
    <link rel="stylesheet" href="CSS/orcamento.css">
</head>
<body class="corpo-dashboard">

<aside class="sidebar">
    <div class="logo-icon">
        <img src="imagens/athenaris_logo.png" alt="Logo" style="width:50px;height:50px;">
    </div>
    <nav class="nav-vertical">
        <a href="home.php" class="nav-link" title="Início">
            <img src="imagens/home_icone.png" alt="Início">
        </a>
        <a href="orcamento.php" class="nav-link ativo" title="Orçamento">
            <img src="imagens/moedas_icone.webp" alt="Orçamento">
        </a>
        <a href="cursos.php" class="nav-link" title="Lições">
            <img src="imagens/livro_icone.png" alt="Lições">
        </a>
        <a href="calculadora.php" class="nav-link" title="Calculadora">
            <img src="imagens/calculadora_icone.png" alt="Calculadora">
        </a>
        <a href="investimentos.php" class="nav-link" title="Investimentos">
            <img src="imagens/acoes_icone.png" alt="Investimentos">
        </a>
    </nav>
</aside>

<header class="header">
    <div class="user-info">
        <span>Bem-vindo, <?= strtoupper(htmlspecialchars($nome_usuario)); ?>!</span>
        <a href="perfil.php" class="perfil-link" title="Acessar Perfil"> 
            <img src="<?= htmlspecialchars($foto_usuario); ?>" alt="Foto de perfil" class="perfil-foto">
        </a> 
    </div>
</header>

<main class="main-content-orcamento">
    <h2 class="titulo"><?= $editando ? 'Editar Transação' : 'Orçamento Pessoal'; ?></h2>

    <div class="resumo">
        <div class="box receita"><h3>Receitas</h3><p>R$ <?= number_format($total_receitas, 2, ',', '.') ?></p></div>
        <div class="box despesa"><h3>Despesas</h3><p>R$ <?= number_format($total_despesas, 2, ',', '.') ?></p></div>
        <div class="box saldo <?= $saldo >= 0 ? 'positivo' : 'negativo' ?>"><h3>Saldo</h3><p>R$ <?= number_format($saldo, 2, ',', '.') ?></p></div>
    </div>

    <form method="POST" class="form-transacao">
        <?php if ($editando): ?>
            <input type="hidden" name="id_edicao" value="<?= $editando['id'] ?>">
        <?php endif; ?>
        <input type="text" name="nome" placeholder="Nome da fonte (ex: Mercado, Salário)" value="<?= htmlspecialchars($editando['nome'] ?? '') ?>" required>
        <input type="number" name="valor" placeholder="Valor (ex: 2000)" step="0.01" value="<?= htmlspecialchars($editando['valor'] ?? '') ?>" required>
        <select name="tipo" required>
            <option value="">Selecione o tipo</option>
            <option value="receita" <?= (isset($editando['tipo']) && $editando['tipo'] === 'receita') ? 'selected' : '' ?>>Receita</option>
            <option value="despesa" <?= (isset($editando['tipo']) && $editando['tipo'] === 'despesa') ? 'selected' : '' ?>>Despesa</option>
        </select>
        <button type="submit"><?= $editando ? 'Atualizar' : 'Adicionar'; ?></button>
        <?php if ($editando): ?>
            <a href="orcamento.php" class="cancelar-edicao">Cancelar</a>
        <?php endif; ?>
    </form>

    <table class="tabela-transacoes">
        <thead>
            <tr><th>Nome</th><th>Tipo</th><th>Valor</th><th>Data</th><th>Ações</th></tr>
        </thead>
        <tbody>
            <?php if (empty($transacoes)): ?>
                <tr><td colspan="5" class="sem-dados">Nenhuma transação registrada.</td></tr>
            <?php else: ?>
                <?php foreach ($transacoes as $t): ?>
                    <tr class="<?= $t['tipo'] ?>">
                        <td><?= htmlspecialchars($t['nome']) ?></td>
                        <td><?= ucfirst($t['tipo']) ?></td>
                        <td>R$ <?= number_format($t['valor'], 2, ',', '.') ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($t['data_registro'])) ?></td>
                        <td>
                            <a href="?editar=<?= $t['id'] ?>" class="botao-editar">✏️</a>
                            <a href="?excluir=<?= $t['id'] ?>" class="botao-excluir" onclick="return confirm('Tem certeza que deseja excluir esta transação?')">🗑️</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="avisos-container">
        <h3>Avisos Financeiros</h3>
        <ul>
        <?php foreach ($transacoes as $t):
            if ($t['tipo'] === 'despesa'):
                $nome = strtolower($t['nome']);
                $valor = number_format($t['valor'], 2, ',', '.');
                $aviso = "Avalie o gasto \"{$t['nome']}\" e veja se há formas de economizar.";
                if (str_contains($nome, 'mercado')) $aviso = "Você gastou R$$valor em mercado. Considere buscar promoções ou comprar em menor quantidade.";
                elseif (str_contains($nome, 'luz')) $aviso = "Conta de luz alta? Use aparelhos fora do horário de pico e reduza o consumo.";
                elseif (str_contains($nome, 'água')) $aviso = "Economize água tomando banhos curtos e evitando desperdícios.";
                elseif (str_contains($nome, 'internet')) $aviso = "Verifique se o plano de internet é o ideal — pode haver opções mais baratas.";
                elseif (str_contains($nome, 'transporte')) $aviso = "Gasto com transporte? Tente caronas ou transporte público.";
                elseif (str_contains($nome, 'lazer')) $aviso = "Gasto alto com lazer — busque opções gratuitas ou reduza a frequência.";
                echo "<li>$aviso</li>";
            endif;
        endforeach; ?>
        </ul>
    </div>
</main>

<footer class="footer">
    | Athenaris - Educação Financeira | Desenvolvido por: Nicolas G.M. Porto, Eduardo E.C. Silva e Mateus F. de S. Santos
</footer>

<script>
  const form = document.getElementById("form-orcamento");
  const tabela = document.querySelector("#tabela-orcamento tbody");
  const totalReceitasEl = document.getElementById("total-receitas");
  const totalDespesasEl = document.getElementById("total-despesas");
  const balancoFinalEl = document.getElementById("balanco-final");
  const avisosLista = document.getElementById("avisos-lista");

  let dados = JSON.parse(localStorage.getItem("orcamento_dados")) || [];

  const gerarAviso = (nome, valor) => {
    nome = nome.toLowerCase();
    if (nome.includes("mercado"))
      return `Você gastou R$${valor} em mercado. Considere procurar promoções ou reduzir quantidades.`;
    if (nome.includes("luz"))
      return `Conta de luz alta? Tente usar aparelhos fora do horário de pico ou reduzir consumo.`;
    if (nome.includes("água"))
      return `Economize água tomando banhos mais curtos e evitando desperdícios.`;
    if (nome.includes("internet"))
      return `Verifique se o plano de internet está adequado ao uso. Pode haver opções mais baratas.`;
    if (nome.includes("transporte"))
      return `Gasto com transporte? Veja se vale usar transporte público ou dividir caronas.`;
    if (nome.includes("lazer"))
      return `Gastos com lazer altos — tente reduzir a frequência ou buscar opções gratuitas.`;
    return `Avalie o gasto "${nome}" e veja se há formas de economizar.`;
  };

  const atualizar = () => {
    let receitas = 0;
    let despesas = 0;
    tabela.innerHTML = "";
    avisosLista.innerHTML = "";

    dados.forEach(d => {
      const row = document.createElement("tr");
      row.innerHTML = `
        <td>${d.nome}</td>
        <td>${d.valor.toFixed(2)}</td>
        <td class="${d.tipo}">${d.tipo}</td>
      `;
      tabela.appendChild(row);

      if (d.tipo === "receita") receitas += d.valor;
      else despesas += d.valor;

      if (d.tipo === "despesa") {
        const li = document.createElement("li");
        li.textContent = gerarAviso(d.nome, d.valor);
        avisosLista.appendChild(li);
      }
    });

    totalReceitasEl.textContent = receitas.toFixed(2);
    totalDespesasEl.textContent = despesas.toFixed(2);

    const balanco = receitas - despesas;
    balancoFinalEl.textContent = `R$ ${balanco.toFixed(2)}`;
    balancoFinalEl.style.color = balanco >= 0 ? "#28a745" : "#dc3545";

    localStorage.setItem("orcamento_dados", JSON.stringify(dados));
  };

  form.addEventListener("submit", e => {
    e.preventDefault();
    const nome = document.getElementById("nome-fonte").value.trim();
    const valor = parseFloat(document.getElementById("valor-fonte").value);
    const tipo = document.getElementById("tipo-fonte").value;
    if (!nome || isNaN(valor)) return;

    dados.push({ nome, valor, tipo });
    form.reset();
    atualizar();
  });

  atualizar();
</script>

<footer class="footer">
    | Athenaris - Educação Financeira | Desenvolvido por: Nicolas G.M. Porto, Eduardo E.C. Silva e Mateus F. de S. Santos
</footer>

</body>
</html>