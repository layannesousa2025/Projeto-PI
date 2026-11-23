# 🏆 ChampionsSports – Sports is Life 

## 📝 Descrição Geral

O **ChampionsSports** é um ecossistema de aplicações integradas — **Web, Mobile e Desktop** — desenvolvido para oferecer aos usuários uma experiência completa e centralizada no universo esportivo.

A plataforma possibilita:

* ✅ Seleção de esportes favoritos
* 📂 Visualização de categorias e eventos esportivos
* 📍 Localização inteligente de atividades esportivas próximas
* 📅 Acesso a treinos, eventos futuros e informações relevantes

O projeto contempla todo o fluxo do usuário, desde a navegação e pesquisa até a interação com funcionalidades personalizadas, proporcionando uma experiência **intuitiva, eficiente e centralizada**.

---

## 🏆 Principais Funcionalidades

### 🔐 Autenticação e Gestão de Usuários

* Cadastro de usuários
* Login seguro
* Recuperação de conta
* Edição de dados pessoais

### 🔎 Navegação e Descoberta

* Sistema de busca com filtros
* Gerenciamento de esportes e eventos favoritos
* Visualização de categorias esportivas
* Localizador de atividades esportivas próximas

---

## ⚠️ Problema Identificado

Devido à rotina acelerada e à falta de centralização de informações esportivas, muitos usuários enfrentam dificuldades para encontrar **atividades, eventos e torneios adequados**.

O **ChampionsSports – Sports is Life** resolve esse problema ao oferecer:

* 📍 Localização automática de eventos próximos
* 🗂️ Informações organizadas em um único ambiente
* ⚡ Uma experiência rápida, intuitiva e personalizada

---

## 🎯 Objetivos do Projeto

* Facilitar a escolha de esportes
* Simplificar a busca por eventos esportivos
* Dar visibilidade a torneios e competições locais
* Aproximar atletas, equipes e organizadores

---

## 🛠️ Desenvolvimento

### 🔧 Backend

* Gerenciamento de usuários e conteúdos
* Integração com banco de dados **MySQL**
* Implementação de regras de negócio
* Segurança e controle de acesso

### 🎨 Frontend

Interfaces intuitivas e responsivas desenvolvidas para:

* 🌐 Web
* 📱 Mobile
* 🖥️ Desktop

---

## 🚀 Implantação e Manutenção

* Desenvolvimento Mobile realizado com **Android Studio**
* Atualmente, o projeto encontra-se disponível nas versões:

  * 🌐 Web
  * 🖥️ Desktop

---

## " 🛠️🔧Versão Atualizada... "

## Versão Desktop

A aplicação desktop está organizada na pasta [`Desktop/`](Desktop):

- `Desktop/src/` — código-fonte da interface desktop
- `Desktop/assets/` — imagens da aplicação
- `Desktop/db_config.example.json` — modelo de configuração do banco
- `Desktop/iniciar_sistema.bat` — inicializador no Windows
- `Desktop/requirements.txt` — dependências Python

### Preparar e entrar no Desktop

1. Instale Python e MySQL.
2. Crie o banco e as tabelas executando `Web/banco_de_dados/script_banco.sql` no MySQL.
3. Na pasta `Desktop/`, copie `db_config.example.json` para `db_config.json` e informe os dados de conexão do seu MySQL.
4. No terminal, dentro de `Desktop/`, instale as dependências e inicie o sistema:

   ```powershell
   py -m venv .venv
   .\.venv\Scripts\python.exe -m pip install -r requirements.txt
   .\iniciar_sistema.bat
   ```

5. Na tela inicial, clique em **Criar conta** para cadastrar um usuário. Depois, entre com o **nome de usuário** e a **senha** cadastrados. A senha deve ter pelo menos 6 caracteres.

## Versão Web

A aplicação web está organizada na pasta [`Web/`](Web):

- `Web/index.php` — página principal
- `Web/php/` — páginas e integrações PHP
- `Web/Js/` — scripts JavaScript
- `Web/img/` — imagens da interface web
- `Web/banco_de_dados/` — scripts SQL

### Preparar e entrar no Web

1. Instale PHP com suporte a MySQL e inicie o servidor MySQL.
2. Crie o banco e as tabelas executando `Web/banco_de_dados/script_banco.sql` no MySQL.
3. Copie `Web/php/config.example.php` para `Web/php/config.local.php` e preencha as credenciais do banco. Não publique esse arquivo.
4. Inicie o servidor PHP a partir da pasta `Web/`:

   ```sh
   php -S localhost:8000
   ```

5. Acesse `http://localhost:8000` no navegador. Se ainda não tiver uma conta, escolha **Criar conta**; depois, entre com o **e-mail cadastrado** (ou o nome completo) e a **senha**.

As versões Web e Desktop podem usar as mesmas contas quando estiverem configuradas para o mesmo banco de dados. No Web, o login aceita o identificador da conta (e-mail no cadastro Web) ou o nome completo. No Desktop, use o identificador da conta cadastrado: nome de usuário para contas criadas no Desktop ou e-mail para contas criadas no Web.
