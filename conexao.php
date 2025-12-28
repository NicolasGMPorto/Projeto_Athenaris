<?php
// Configurações do Banco de Dados (ALTERE ESTES VALORES)
$host = 'localhost'; // Geralmente 'localhost' em ambientes de desenvolvimento
$db_name = 'athenaris_db'; // Nome do banco de dados que você criou
$username = 'root'; // Seu usuário do MySQL/MariaDB
$password = ''; // Sua senha do MySQL/MariaDB (vazio se for XAMPP/WAMP padrão)

try {
    // Cria a instância de conexão PDO
    $conexao = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    
    // Configura o PDO para lançar exceções em caso de erro (melhor para debug)
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Se a conexão for bem-sucedida, $conexao pode ser usado nos outros arquivos PHP
    
} catch (PDOException $e) {
    // Em caso de falha na conexão, exibe uma mensagem de erro (desligar em produção)
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}