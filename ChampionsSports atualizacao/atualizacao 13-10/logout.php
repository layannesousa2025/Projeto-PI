<?php
session_start(); // Inicia a sessão

session_unset(); // Remove todas as variáveis da sessão

session_destroy(); // Destrói a sessão

header("Location: telaPrincipal.php"); // Redireciona para a página principal (agora .php)
exit;