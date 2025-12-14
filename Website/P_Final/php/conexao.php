<?php
// conexao.php
$servername = "127.0.0.1";
$username   = "root";
$password   = "";
$dbname     = "champions_sport";
$port       = 3306;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($servername, $username, $password, $dbname, $port);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("❌ Falha na conexão com o banco de dados: " . $e->getMessage());
}
?>
