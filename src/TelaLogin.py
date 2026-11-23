"""
Sistema de Autenticação e Controle de Acesso (Atualização Champions)
-------------------------------------------------------------------
- Gerenciamento de usuários com armazenamento JSON (usuarios.json)
- Criptografia com SHA-256 e Salt individual por usuário
- Telas padronizadas (Login, Cadastro e Painel Principal)
- Troca dinâmica de frames em janela única (evita travamentos e múltiplos mainloops)
"""

import hashlib
import json
import os
import re
import secrets
import tkinter as tk
from tkinter import messagebox, ttk
from ChampionsDB import ChampionsDB

from PIL import Image, ImageTk


# ======================================================================
# Paleta de Cores do Sistema
# ======================================================================
class Cores:
    VERMELHO        = "#E63946"
    VERMELHO_ESCURO = "#B71C2C"
    LARANJA         = "#F77F00"
    LARANJA_ESCURO  = "#D66A00"
    BRANCO          = "#FFFFFF"
    CINZA_TEXTO     = "#5A5A5A"
    CINZA_CLARO     = "#FFF3E0"
    ERRO            = "#C62828"
    OK              = "#2E7D32"


# ======================================================================
# Banco de Dados de Usuários (JSON + Hash + Salt)
# ======================================================================
class BancoUsuarios:
    def __init__(self):
        self.db = ChampionsDB()

    def existe(self, usuario):
        try:
            return self.db.existe_usuario(usuario)
        except Exception as erro:
            messagebox.showerror("Erro de banco", str(erro))
            return False

    def cadastrar(self, usuario, senha, nome, dados=None):
        try:
            return self.db.cadastrar_usuario(usuario, senha, nome, dados)
        except Exception as erro:
            return False, f"Erro de banco: {erro}"

    def autenticar(self, usuario, senha):
        try:
            return self.db.autenticar(usuario, senha) is not None
        except Exception as erro:
            messagebox.showerror(
                "Erro de banco",
                "Não foi possível conectar ao MySQL:\n" + str(erro),
            )
            return False

    def obter_nome(self, usuario):
        try:
            return self.db.obter_nome(usuario)
        except Exception:
            return usuario


# ======================================================================
# Tela de Login
# ======================================================================
class TelaLogin(tk.Frame):
    def __init__(self, master, banco, usuario_preenchido=None):
        super().__init__(master, bg=Cores.VERMELHO)
        self.master = master
        self.banco = banco
        self.senha_visivel = False

        self.criar_widgets()

        if usuario_preenchido:
            self.entry_usuario.insert(0, usuario_preenchido)
            self.entry_senha.focus_set()
        else:
            self.entry_usuario.focus_set()

        self.master.bind("<Return>", lambda e: self.fazer_login())

    def criar_widgets(self):
        self.configure(bg="#FFFFFF")

        caminho_logo = os.path.join(
            os.path.dirname(os.path.dirname(__file__)),
            "assets",
            "logo_login_transparente.png",
        )
        if not os.path.exists(caminho_logo):
            caminho_logo = os.path.join(
                os.path.dirname(__file__), "logo_login_transparente.png"
            )

        if os.path.exists(caminho_logo):
            imagem_logo = Image.open(caminho_logo)
            imagem_logo = imagem_logo.resize((310, 230), Image.Resampling.LANCZOS)
            self.imagem_logo_tk = ImageTk.PhotoImage(imagem_logo)

            tk.Label(
                self,
                image=self.imagem_logo_tk,
                bg="#FFFFFF",
                bd=0,
                highlightthickness=0,
            ).pack(pady=(0, 5))
        else:
            tk.Label(
                self,
                text="🔐 Login",
                font=("Arial", 24, "bold"),
                bg="#FFFFFF",
                fg="#111111",
            ).pack(pady=(20, 10))

        form = tk.Frame(self, bg="#FFFFFF")
        form.pack(padx=100, pady=(5, 0), fill="x")

        tk.Label(
            form, text="Login:", font=("Arial", 15, "bold"),
            bg="#FFFFFF", fg="#111111", anchor="w"
        ).pack(fill="x", pady=(0, 3))

        self.entry_usuario = tk.Entry(
            form, font=("Arial", 12),
            bg="#FFFFFF", fg="#111111",
            insertbackground="#111111",
            relief="groove", bd=1, highlightthickness=1,
            highlightbackground="#7A7A7A",
        )
        self.entry_usuario.pack(fill="x", ipady=4)

        tk.Label(
            form, text="Senha:", font=("Arial", 15, "bold"),
            bg="#FFFFFF", fg="#111111", anchor="w"
        ).pack(fill="x", pady=(12, 3))

        self.entry_senha = tk.Entry(
            form, font=("Arial", 12),
            bg="#FFFFFF", fg="#111111",
            insertbackground="#111111",
            relief="groove", bd=1, highlightthickness=1,
            highlightbackground="#7A7A7A",
            show="•",
        )
        self.entry_senha.pack(fill="x", ipady=4)
        self.btn_olho = None

        tk.Button(
            form, text="Entrar", font=("Arial", 10, "bold"),
            bg="#5B2A86", fg="#FFFFFF",
            activebackground="#7042A0", activeforeground="#FFFFFF",
            relief="flat", bd=0, width=13, pady=7,
            cursor="hand2", command=self.fazer_login,
        ).pack(pady=(20, 0))

        tk.Button(
            form, text="Criar conta", font=("Arial", 11, "bold"),
            bg="#FFFFFF", fg="#111111",
            activebackground="#EFEFEF", activeforeground="#111111",
            relief="flat", bd=0, cursor="hand2",
            command=self.master.mostrar_cadastro,
        ).pack(pady=(12, 0))

        tk.Button(
            form, text="Sair do Sistema", font=("Arial", 13, "bold"),
            bg="#FFFFFF", fg="#111111",
            activebackground="#EFEFEF", activeforeground="#111111",
            relief="flat", bd=0, cursor="hand2",
            command=self.master.destroy,
        ).pack(pady=(10, 0))

    def alternar_senha(self):
        self.senha_visivel = not self.senha_visivel
        self.entry_senha.config(show="" if self.senha_visivel else "•")
        self.btn_olho.config(text="🙈" if self.senha_visivel else "👁")

    def fazer_login(self):
        usuario = self.entry_usuario.get().strip()
        senha = self.entry_senha.get()

        if not usuario or not senha:
            messagebox.showwarning("Aviso", "Preencha usuário e senha.")
            return

        if self.banco.autenticar(usuario, senha):
            nome = self.banco.obter_nome(usuario)
            messagebox.showinfo("Sucesso", f"Bem-vindo, {nome}!")
            self.master.mostrar_principal(nome)
        else:
            messagebox.showerror("Erro", "Usuário ou senha inválidos.")
            self.entry_senha.delete(0, tk.END)
            self.entry_senha.focus_set()

    def esqueci_senha(self):
        messagebox.showinfo(
            "Recuperar senha",
            "Entre em contato com o suporte:\nsuporte@exemplo.com"
        )


# ======================================================================
# Tela de Cadastro
# ======================================================================
class TelaCadastro(tk.Frame):
    def __init__(self, master, banco):
        super().__init__(master, bg=Cores.VERMELHO)
        self.master = master
        self.banco = banco

        self.senha_visivel = False
        self.confirmar_visivel = False

        self.criar_widgets()
        self.entry_nome.focus_set()
        self.master.bind("<Return>", lambda e: self.cadastrar())

    def criar_widgets(self):
        fundo = "#FFFFFF"
        roxo = "#5B2A86"
        texto = "#202124"
        cinza = "#6B7280"
        borda = "#D4D8E1"
        self.configure(bg=fundo)

        tk.Label(
            self, text="CADASTRO DE USUARIO",
            font=("Segoe UI", 16, "bold"),
            bg=roxo, fg="#FFFFFF", height=2,
        ).pack(fill="x")

        form = tk.Frame(self, bg=fundo)
        form.pack(fill="both", expand=True, padx=24, pady=18)

        form.columnconfigure(0, weight=1)
        form.columnconfigure(1, weight=1)

        def campo(pai, linha, coluna, rotulo, largura=None, mostrar=None):
            bloco = tk.Frame(pai, bg=fundo)
            bloco.grid(row=linha, column=coluna, sticky="ew",
                       padx=(0, 12) if coluna == 0 else (12, 0), pady=7)
            tk.Label(
                bloco, text=rotulo, font=("Segoe UI", 9, "bold"),
                bg=fundo, fg=texto, anchor="w",
            ).pack(fill="x", pady=(0, 4))
            entrada = tk.Entry(
                bloco, font=("Segoe UI", 10),
                bg="#FFFFFF", fg=texto,
                relief="solid", bd=1, highlightthickness=0,
                show=mostrar,
            )
            entrada.pack(fill="x", ipady=5)
            return entrada

        self.entry_codigo = campo(form, 0, 0, "Código")
        self.entry_codigo.insert(0, "3")
        self.entry_usuario = campo(form, 0, 1, "Usuário")

        self.entry_nome = campo(form, 1, 0, "Nome completo")
        self.entry_cpf = campo(form, 1, 1, "CPF")

        self.entry_nascimento = campo(form, 2, 0, "Data de nascimento")
        self.entry_telefone = campo(form, 2, 1, "Telefone")

        self.entry_email = campo(form, 3, 0, "Email")
        self.entry_senha = campo(form, 3, 1, "Senha", mostrar="•")
        self.entry_confirmar = campo(form, 4, 0, "Confirmar senha", mostrar="•")

        deficiencia_bloco = tk.Frame(form, bg=fundo)
        deficiencia_bloco.grid(row=4, column=1, sticky="ew",
                               padx=(12, 0), pady=7)
        tk.Label(
            deficiencia_bloco, text="Possui alguma deficiência?",
            font=("Segoe UI", 9, "bold"), bg=fundo, fg=texto, anchor="w",
        ).pack(fill="x", pady=(0, 4))
        self.deficiencia = tk.StringVar(value="nao")
        opcoes = tk.Frame(deficiencia_bloco, bg=fundo)
        opcoes.pack(anchor="w")
        tk.Radiobutton(
            opcoes, text="Sim", variable=self.deficiencia, value="sim",
            bg=fundo, fg=texto, activebackground=fundo,
            font=("Segoe UI", 9),
        ).pack(side="left", padx=(0, 20))
        tk.Radiobutton(
            opcoes, text="Não", variable=self.deficiencia, value="nao",
            bg=fundo, fg=texto, activebackground=fundo,
            font=("Segoe UI", 9),
        ).pack(side="left")

        self.situacao = tk.StringVar(value="ativo")
        situacao_bloco = tk.Frame(form, bg=fundo)
        situacao_bloco.grid(row=5, column=0, sticky="w",
                            pady=(10, 0), columnspan=2)
        tk.Label(
            situacao_bloco, text="Situação:", font=("Segoe UI", 9, "bold"),
            bg=fundo, fg=texto,
        ).pack(side="left", padx=(0, 12))
        tk.Radiobutton(
            situacao_bloco, text="Ativo", variable=self.situacao,
            value="ativo", bg=fundo, fg=texto, activebackground=fundo,
            font=("Segoe UI", 9),
        ).pack(side="left", padx=(0, 16))
        tk.Radiobutton(
            situacao_bloco, text="Inativo", variable=self.situacao,
            value="inativo", bg=fundo, fg=texto, activebackground=fundo,
            font=("Segoe UI", 9),
        ).pack(side="left")

        botoes = tk.Frame(form, bg=fundo)
        botoes.grid(row=6, column=0, columnspan=2, sticky="ew", pady=(24, 0))

        tk.Button(
            botoes, text="Cadastrar usuário",
            font=("Segoe UI", 10, "bold"),
            bg=roxo, fg="#FFFFFF",
            activebackground="#7042A0", activeforeground="#FFFFFF",
            relief="flat", bd=0, padx=18, pady=9,
            cursor="hand2", command=self.cadastrar,
        ).pack(side="left")

        tk.Button(
            botoes, text="Voltar ao login",
            font=("Segoe UI", 9),
            bg=fundo, fg=cinza,
            activebackground="#F3F4F6", activeforeground=texto,
            relief="flat", bd=0, padx=10, pady=9,
            cursor="hand2", command=self.master.mostrar_login,
        ).pack(side="right")

    def _criar_label(self, pai, texto):
        tk.Label(
            pai, text=texto, font=("Segoe UI", 10, "bold"),
            bg=Cores.LARANJA, fg=Cores.BRANCO, anchor="w",
        ).pack(fill="x", padx=20, pady=(10, 3))

    def _criar_entry(self, pai):
        e = tk.Entry(
            pai, font=("Segoe UI", 12),
            bg=Cores.BRANCO, fg=Cores.CINZA_TEXTO,
            insertbackground=Cores.LARANJA, relief="flat", bd=0,
        )
        e.pack(fill="x", padx=20, ipady=7)
        return e

    def alternar_visao(self, qual):
        if qual == 1:
            self.senha_visivel = not self.senha_visivel
            self.entry_senha.config(show="" if self.senha_visivel else "•")
            self.btn_olho1.config(text="🙈" if self.senha_visivel else "👁")
        else:
            self.confirmar_visivel = not self.confirmar_visivel
            self.entry_confirmar.config(show="" if self.confirmar_visivel else "•")
            self.btn_olho2.config(text="🙈" if self.confirmar_visivel else "👁")

    def atualizar_forca(self, event=None):
        senha = self.entry_senha.get()
        self.barra_forca.delete("all")

        if not senha:
            self.lbl_forca.config(text="")
            return

        pontos = 0
        if len(senha) >= 6:
            pontos += 1
        if len(senha) >= 10:
            pontos += 1
        if re.search(r"[A-Z]", senha):
            pontos += 1
        if re.search(r"[0-9]", senha):
            pontos += 1
        if re.search(r"[!@#$%^&*(),.?\":{}|<>_\-]", senha):
            pontos += 1

        if pontos <= 2:
            cor, texto, largura = "#E53935", "Fraca", 0.33
        elif pontos <= 4:
            cor, texto, largura = "#FB8C00", "Média", 0.66
        else:
            cor, texto, largura = "#43A047", "Forte", 1.0

        self.barra_forca.update_idletasks()
        w = self.barra_forca.winfo_width() or 300
        self.barra_forca.create_rectangle(
            0, 0, w * largura, 5, fill=cor, outline=""
        )
        self.lbl_forca.config(text=f"Força da senha: {texto}", fg=cor)

    def cadastrar(self):
        nome = self.entry_nome.get().strip()
        usuario = self.entry_usuario.get().strip()
        senha = self.entry_senha.get()
        confirmar = self.entry_confirmar.get()

        # Validações dos campos
        if not nome:
            messagebox.showwarning("Aviso", "Informe seu nome completo.")
            self.entry_nome.focus_set()
            return

        if len(nome.split()) < 2:
            messagebox.showwarning("Aviso", "Informe nome e sobrenome.")
            self.entry_nome.focus_set()
            return

        if not usuario:
            messagebox.showwarning("Aviso", "Informe um nome de usuário.")
            self.entry_usuario.focus_set()
            return

        if not re.match(r"^[a-zA-Z0-9._]{3,20}$", usuario):
            messagebox.showwarning(
                "Usuário inválido",
                "O usuário deve ter entre 3 e 20 caracteres\n"
                "e conter apenas letras, números, ponto ou underline."
            )
            self.entry_usuario.focus_set()
            return

        if self.banco.existe(usuario):
            messagebox.showerror("Erro", "Este usuário já está cadastrado.")
            self.entry_usuario.focus_set()
            return

        if len(senha) < 6:
            messagebox.showwarning("Senha fraca", "A senha deve ter pelo menos 6 caracteres.")
            self.entry_senha.focus_set()
            return

        if senha != confirmar:
            messagebox.showerror("Erro", "As senhas não coincidem.")
            self.entry_confirmar.delete(0, tk.END)
            self.entry_confirmar.focus_set()
            return

        # Cadastro no banco
        dados_cadastro = {
            "codigo": self.entry_codigo.get().strip(),
            "deficiencia": self.deficiencia.get(),
            "cpf": self.entry_cpf.get().strip(),
            "nascimento": self.entry_nascimento.get().strip(),
            "telefone": self.entry_telefone.get().strip(),
            "email": self.entry_email.get().strip(),
            "situacao": self.situacao.get(),
        }
        ok, msg = self.banco.cadastrar(usuario, senha, nome, dados_cadastro)
        if ok:
            messagebox.showinfo("Sucesso", msg + "\n\nAgora faça login.")
            self.master.mostrar_login(usuario_preenchido=usuario)
        else:
            messagebox.showerror("Erro", msg)


# ======================================================================
# Tela Principal (Dashboard pós-login)
# ======================================================================
class TelaPrincipal(tk.Frame):
    def __init__(self, master, nome):
        super().__init__(master, bg="#5B2A86")
        self.master = master
        self.nome = nome

        # Remove o atalho de Enter para não disparar submissões indesejadas
        self.master.unbind("<Return>")
        self.criar_widgets()

    def criar_widgets(self):
        tk.Label(
            self, text="✅", font=("Segoe UI", 48),
            bg="#5B2A86", fg=Cores.BRANCO
        ).pack(pady=(35, 0))

        tk.Label(
            self, text="Bem-vindo, ChampionsSports!", font=("Segoe UI", 22, "bold"),
            bg="#5B2A86", fg=Cores.BRANCO
        ).pack(pady=(8, 4))

        tk.Label(
            self, text="Login realizado com sucesso ao sistema.", font=("Segoe UI", 11),
            bg="#5B2A86", fg="#E9D5FF"
        ).pack(pady=(0, 25))

        # Botão para abrir o Gerenciador de Tarefas integrado
        btn_tarefas = tk.Button(
            self,
            text="📋 Abrir Gerenciador de Tarefas",
            font=("Segoe UI", 11, "bold"),
            bg=Cores.BRANCO,
            fg="#5B2A86",
            activebackground="#F3E8FF",
            activeforeground="#7042A0",
            relief="flat",
            bd=0,
            padx=20,
            pady=10,
            cursor="hand2",
            command=self.abrir_gerenciador_tarefas,
        )
        btn_tarefas.pack(pady=6)

        # Botão Sair
        btn_sair = tk.Button(
            self,
            text="🚪 Desconectar / Sair",
            font=("Segoe UI", 10, "bold"),
            bg="#7042A0",
            fg=Cores.BRANCO,
            activebackground="#8B5FBF",
            activeforeground=Cores.BRANCO,
            relief="flat",
            bd=0,
            padx=18,
            pady=8,
            cursor="hand2",
            command=self.master.mostrar_login,
        )
        btn_sair.pack(pady=10)

    def abrir_gerenciador_tarefas(self):
        try:
            import TelaGerenciadorTarefa
            janela_tarefas = tk.Toplevel(self.master)
            TelaGerenciadorTarefa.GerenciadorTarefas(
                janela_tarefas,
                ao_sair=janela_tarefas.destroy,
            )
        except Exception as e:
            messagebox.showinfo(
                "Aviso",
                f"Não foi possível abrir o gerenciador de tarefas:\n{e}"
            )


# ======================================================================
# Aplicação Principal (Gerenciador de Janela e Navegação)
# ======================================================================
class App(tk.Tk):
    def __init__(self):
        super().__init__()
        self.banco = BancoUsuarios()
        self.configure(bg="#FFFFFF")
        self.resizable(False, False)

        self.configurar_estilos()
        self.frame_atual = None
        self.mostrar_login()

    def configurar_estilos(self):
        style = ttk.Style()
        style.theme_use("clam")

        style.configure(
            "Acao.TButton",
            font=("Segoe UI", 11, "bold"),
            padding=(14, 10),
            background=Cores.BRANCO,
            foreground=Cores.VERMELHO,
            borderwidth=0,
            relief="flat",
            focusthickness=0,
        )
        style.map(
            "Acao.TButton",
            background=[("active", Cores.CINZA_CLARO), ("pressed", "#FFE0B2")],
            foreground=[("active", Cores.VERMELHO_ESCURO)],
        )

    def mudar_tela(self, classe_tela, geometry, titulo, **kwargs):
        if self.frame_atual is not None:
            self.frame_atual.destroy()

        self.title(titulo)
        self.centralizar(geometry)

        self.frame_atual = classe_tela(self, **kwargs)
        self.frame_atual.pack(fill="both", expand=True)

    def centralizar(self, geometry):
        largura, altura = map(int, geometry.split("x"))
        self.update_idletasks()
        pos_x = (self.winfo_screenwidth() - largura) // 2
        pos_y = (self.winfo_screenheight() - altura) // 2
        self.geometry(f"{largura}x{altura}+{pos_x}+{pos_y}")

    def mostrar_login(self, usuario_preenchido=None):
        self.mudar_tela(
            TelaLogin,
            geometry="420x570",
            titulo="Login - Sistema",
            banco=self.banco,
            usuario_preenchido=usuario_preenchido,
        )

    def mostrar_cadastro(self):
        self.mudar_tela(
            TelaCadastro,
            geometry="620x650",
            titulo="Cadastro - Sistema",
            banco=self.banco,
        )

    def mostrar_principal(self, nome):
        self.mudar_tela(
            TelaPrincipal,
            geometry="500x380",
            titulo="Painel Principal",
            nome=nome,
        )


# ======================================================================
# Ponto de Entrada
# ======================================================================
if __name__ == "__main__":
    app = App()
    app.mainloop()
