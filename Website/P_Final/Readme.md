# 🏆 ChampionsSports — Website (MySQL + XAMPP)

## 📌 Resumo

Projeto web desenvolvido em **Visual Studio Code** que conecta a um banco de dados **MySQL** (rodando localmente via XAMPP) para cadastro, autenticação e gerenciamento de usuários, eventos e categorias.

A aplicação utiliza **PHP no backend** e segue boas práticas básicas de organização e segurança para facilitar manutenção e colaboração.

---

## 🚀 Principais Funcionalidades

- Cadastro e login de usuários (com recuperação de senha)
- Visualização e edição de perfil:
  - Tela Principal
  - Meus Favoritos
  - Adicionar Contato
  - Sair
- Gestão de usuários (CRUD: criar, atualizar, deletar, editar)
- Menu de navegação:
  - Início
  - Categorias
  - Eventos
  - Sobre
  - Contato
- Busca e filtragem por categorias e eventos esportivos
- Inserção, edição e exclusão de dados via interface web
- Suporte a chatbot para dúvidas
- Localização de eventos via links de mapa

---

## 🛠️ Tecnologias Utilizadas

- **Frontend / Ambiente:**
  - HTML
  - CSS
  - JavaScript
  - Visual Studio Code

- **Backend:**
  - PHP (recomenda-se uso de PDO para maior segurança)

- **Banco de Dados:**
  - MySQL

- **Servidor Local:**
  - XAMPP (Apache + MySQL)

---

## ⚙️ Pré-requisitos

Antes de rodar o projeto, instale e configure:

- Visual Studio Code
- XAMPP (ou WAMP) com Apache + MySQL
- PHP (compatível com a versão do XAMPP)

> Certifique-se de que o **MySQL está em execução** no painel do XAMPP.

---

## 🗄️ Importando o Banco de Dados

1. Acesse o phpMyAdmin:


2. Crie o banco de dados:


http://localhost/phpmyadmin


2. Crie o banco de dados:

champions_sport


3. Clique em **Importar**

4. Selecione o arquivo:

champions_sport.sql


---

## 🔌 Configuração da Conexão com o Banco

Crie ou edite o arquivo:


ConexaoMysql.php


### Exemplo:

```php
<?php
// conexao.php

$servername = "127.0.0.1";
$username   = "root";
$password   = "";
$dbname     = "champions_sport";
$port       = 3306;

// Ativa relatório de erros do MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Cria conexão
    $conn = new mysqli($servername, $username, $password, $dbname, $port);

    // Define charset para evitar problemas com acentuação
    $conn->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {
    die("❌ Falha na conexão com o banco de dados: " . $e->getMessage());
}
?>

Observação:
Caso não consiga alterar a porta, verifique o IP da sua máquina para configurar a conexão corretamente.

▶️ Como Executar o Projeto

Inicie o XAMPP e ative:

Apache

MySQL

Copie a pasta do projeto para:

C:\xampp\htdocs\ChampionsSports\P_Final

Acesse no navegador:

http://localhost/ChampionsSports

ou, se estiver usando outra porta:

http://localhost:porta/ChampionsSports

Verifique o arquivo ConexaoMysql.php e ajuste:

Host

Porta

Usuário

Senha

Teste as principais rotas:

Página inicial

Login

Cadastro

Área administrativa