<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.html');
    exit;
}

$nome_usuario = $_SESSION['nome_usuario'] ?? 'Usuário';
$foto_usuario = $_SESSION['foto_usuario'] ?? 'imagens/perfil_icone.webp';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora Financeira</title>
    <link rel="stylesheet" href="css/calculadora.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="corpo-dashboard">

    <aside class="sidebar">
        <div class="logo-icon">
            <img src="imagens/athenaris_logo.png" alt="Logo Athenaris" style="width: 50px; height: 50px;">
        </div>
        <nav class="nav-vertical">
            <a href="home.php" class="nav-link" title="Início"><img src="imagens/home_icone.png"></a>
            <a href="orcamento.php" class="nav-link" title="Orçamento"><img src="imagens/moedas_icone.webp"></a>
            <a href="cursos.php" class="nav-link" title="Lições"><img src="imagens/livro_icone.png"></a>
            <a href="calculadora.php" class="nav-link ativo" title="Calculadora"><img src="imagens/calculadora_icone.png"></a>
            <a href="investimentos.php" class="nav-link" title="Investimentos"><img src="imagens/acoes_icone.png"></a>
        </nav>
    </aside>

    <header class="header">
        <div class="user-info">
            <span>Bem-vindo, <?php echo strtoupper(htmlspecialchars($nome_usuario)); ?>!</span>
            <a href="perfil.php" class="perfil-link"><img src="<?php echo htmlspecialchars($foto_usuario); ?>" class="perfil-foto"></a>
        </div>
    </header>

    <main class="main-content">
        <h2 class="titulo-principal">Calculadora Financeira</h2>

        <div class="abas">
            <button class="aba ativa" data-aba="juros">Juros Compostos</button>
            <button class="aba" data-aba="lucro">Lucro e Margem</button>
            <button class="aba" data-aba="meta">Meta Financeira</button>
        </div>

        <section id="aba-juros" class="conteudo-aba ativo">
            <h3>Simulação de Juros Compostos</h3>

            <div class="painel-calculadora">
                <form id="formJuros" class="form-calculadora">
                    <label>Valor Inicial (R$)</label>
                    <input type="number" step="0.01" id="valorInicial" required placeholder="Ex: 1000">

                    <label>Valor Adicionado por Mês (R$)</label>
                    <input type="number" step="0.01" id="valorMensal" value="0" placeholder="Ex: 100">

                    <label>Taxa de Juros (% ao mês)</label>
                    <input type="number" step="0.01" id="taxaJuros" required placeholder="Ex: 1.2">

                    <label>Tempo (meses)</label>
                    <input type="number" id="tempoMeses" required placeholder="Ex: 60">

                    <button type="submit" class="btn-calcular">Calcular</button>
                </form>

                <div class="resumo-e-grafico">
                    <div class="resultado-resumo">
                        <p><strong>Montante Final:</strong> <span id="montanteFinal">R$ 0,00</span></p>
                        <p><strong>Juros Ganhos:</strong> <span id="jurosGanhos">R$ 0,00</span></p>
                        <p><strong>Total Investido:</strong> <span id="investidoTotal">R$ 0,00</span></p>
                    </div>

                    <div class="grafico-card">
                        <canvas id="graficoJuros"></canvas>
                    </div>
                </div>
            </div>

            <div class="tabela-card">
                <h4>Detalhamento por período</h4>
                <table class="tabela-detalhes">
                    <thead>
                        <tr>
                            <th>Período</th>
                            <th>Meses</th>
                            <th>Investido</th>
                            <th>Juros</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaCorpo">
                        <tr><td colspan="5" class="vazia">Realize uma simulação para ver os dados.</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

     <section id="aba-lucro" class="conteudo-aba">
    <h3>Cálculo de Lucro e Margem</h3>

    <form id="formLucro" class="form-calculadora">
        <label>Custo (R$)</label>
        <input type="number" id="custo" step="0.01" required placeholder="Ex: 100">

        <label>Preço de Venda (R$)</label>
        <input type="number" id="precoVenda" step="0.01" required placeholder="Ex: 150">

        <button type="submit" class="btn-calcular">Calcular</button>
    </form>

    <div class="resultado-resumo">
        <p><strong>Lucro Bruto:</strong> <span id="lucroBruto">R$ 0,00</span></p>
        <p><strong>Margem de Lucro:</strong> <span id="margemLucro">0%</span></p>
    </div>
</section>

<section id="aba-meta" class="conteudo-aba">
    <h3>Planejamento de Meta Financeira</h3>

    <form id="formMeta" class="form-calculadora">
        <label>Valor Alvo (R$)</label>
        <input type="number" id="valorAlvo" step="0.01" required placeholder="Ex: 5000">

        <label>Tempo (meses)</label>
        <input type="number" id="tempoMeta" required placeholder="Ex: 12">

        <button type="submit" class="btn-calcular">Calcular</button>
    </form>

    <div class="resultado-resumo">
        <p><strong>Valor a Guardar por Mês:</strong> <span id="valorPorMes">R$ 0,00</span></p>
    </div>
</section>

    </main>

    <footer class="footer">
        | Athenaris - Educação Financeira | Desenvolvido por: Nicolas G.M. Porto, Eduardo E.C. Silva e Mateus F. de S. Santos
    </footer>

    <script src="js/calculadora.js"></script>
</body>
</html>
