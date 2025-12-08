Readme 
Projeto Android - Champions Sports, Desktop, Web e Mobile com MySQL 


Projeto - Champions Sports, Desktop. 

##Aplicativo utilizado:
NetBeans
Linguagem Java
 MySQL 

##Funcionalidades: 
Cadastro e login de usuários (com recuperação de senha).
Visualização e edição de perfil (TelaPrincipal, Meus Favoritos, Adicionar Contato, Sair).
Gestão de Admin (CRUD:Pesquisar ,Alterar,Excluir usuario).
Menu de navegação: Início, Categorias, Eventos, Sobre, Contato.
Busca e filtragem por categorias e eventos de e-sports.
Inserção, edição e exclusão de dados via interface Desktop.
Suporte a chat-bot para dúvidas.
Localização de eventos via links de mapa.

##Tecnologias utilizadas 
Java
Apache NetBeans + MySQL 



##Boas práticas adotadas 
Nomes claros  variáveis e funções;
Boa estrutura para manutenção do sistema.
Cadastramento eficas do Admin.
Validações no lado servidor.
 
##Pré-requisitos:
Antes de rodar o projeto, instale e configure o ambiente:com 
Plugins no NetBeans
JTattooDemo.jar 
jcalendar-1.4.jar
mysql-connector-j-8.4.0.jar
JDK 11 (Default) 
ConexaoMysql.java
Certifique-se de que o MySQL está em execução e com acesso . 

##Importando o banco de dados:
Abra o phpMyAdmin (geralmente http://localhost/phpmyadmin).
Crie um banco de dados (ou importe diretamente): p_final.
Importe o arquivo SQL do projeto (p_final.sql) pelo botão Importar do NetBeans.
Alternativamente, na linha de comando 

mysql -u root -p p_final < p_final.sql 

##Configuração da conexão com o banco(Ex: NetBeans / MySQl).
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
public class ConexaoMysql { 
private static final String DRIVER = "com.mysql.cj.jdbc.Driver";
private static final String URL = "jdbc:mysql://localhost:3307/p_final?useSSL=false&serverTimezone=UTC";
private static final String USER = "root";
private static final String PASS = "1234";
public static Connection conexaoBanco() throws SQLException {
try {
Class.forName(DRIVER);
return DriverManager.getConnection(URL, USER, PASS); 

##Como executar o projeto
Abra o seu  Apache NetBeans e MySQL.
No NetBeans abra a pasta  OpenProject,copie a pasta do projeto /ChampionsSports para o diretório htdocs.
Verifique o arquivo ConexaoMysql.php e ajuste host/porta/usuário/senha conforme seu ambiente.
Verifique os plugins do NetBeans.
Teste rotas principais: página inicial, tela de login, cadastro e administração. 