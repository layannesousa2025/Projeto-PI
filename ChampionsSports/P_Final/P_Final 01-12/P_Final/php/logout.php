<?php
session_start(); // Inicia a sessão

session_unset(); // Remove todas as variáveis da sessão

session_destroy(); // Destrói a sessão

header("Location: ../index.php"); // Redireciona para a página principal na pasta raiz
exit;