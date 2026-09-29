<template>
  <div class="min-h-screen w-screen flex flex-col items-center justify-center p-4 sm:p-6 bg-slate-100 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 font-sans selection:bg-[#FC6714] selection:text-white transition-colors duration-300">
    <!-- Card Principal de Login com Split Layout Institucional -->
    <main class="w-full max-w-[920px] min-h-[520px] rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl flex flex-col md:flex-row overflow-hidden relative transition-all duration-300">
      
      <!-- Coluna Esquerda: Institucional / Identidade Rede Pronta -->
      <section class="w-full md:w-[46%] bg-gradient-to-br from-[#06064D] via-[#04043b] to-[#020224] text-white p-8 sm:p-10 flex flex-col justify-between relative overflow-hidden select-none">
        <!-- Elementos Luminosos de Ambientação de Fundo -->
        <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full bg-[#FC6714]/15 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-20 w-64 h-64 rounded-full bg-blue-600/10 blur-3xl pointer-events-none"></div>

        <!-- Topo: Marca / Logotipo Oficial do ERP -->
        <div class="relative z-10">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#FC6714] flex items-center justify-center text-white font-heading font-bold text-lg shadow-md shadow-[#FC6714]/30 ring-2 ring-white/10">
              RP
            </div>
            <div>
              <h1 class="text-base font-bold font-heading tracking-tight text-white uppercase leading-none">
                Rede Pronta
              </h1>
              <p class="text-[11px] text-slate-300 font-medium mt-0.5 tracking-wider uppercase">
                ERP & Field Service
              </p>
            </div>
          </div>
        </div>

        <!-- Mensagem de Boas-vindas e Contexto -->
        <div class="relative z-10 my-8 sm:my-auto">
          <span class="inline-block px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider bg-white/10 text-orange-300 border border-white/10 mb-3 backdrop-blur-xs">
            Portal Unificado
          </span>
          <h2 class="text-2xl sm:text-3xl font-bold font-heading text-white leading-tight mb-3">
            Olá, bem-vindo!
          </h2>
          <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-sm font-normal">
            Acesse seu painel integrado para gerenciar operações, contratos, pessoas, suprimentos, depósitos e ordens de serviço com máxima segurança.
          </p>

          <div class="mt-6 flex items-center gap-3">
            <a
              href="#developer"
              class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-white/30 text-white hover:bg-white/10 text-xs font-bold tracking-wider uppercase transition cursor-pointer hover:border-white/60 focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
            >
              Ambiente Sandbox
            </a>
            <span class="text-[11px] text-slate-400">v2.4 Enterprise</span>
          </div>
        </div>

        <!-- Rodapé da Coluna Esquerda -->
        <div class="relative z-10 pt-4 border-t border-white/10 text-[11px] text-slate-400 flex items-center justify-between">
          <span>Segurança SSL / TLS</span>
          <span>RedePronta Tech</span>
        </div>
      </section>

      <!-- Coluna Direita: Formulário de Autenticação -->
      <section class="w-full md:w-[54%] bg-white dark:bg-slate-900 p-8 sm:p-10 flex flex-col justify-between">
        <div class="w-full max-w-md mx-auto">
          <header class="mb-6">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-semibold text-[#FC6714] uppercase tracking-wider">Acesso ao Sistema</span>
              <button
                type="button"
                @click="toggleTheme"
                class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                :title="theme === 'dark' ? 'Mudar para tema claro' : 'Mudar para tema escuro'"
              >
                <Sun v-if="theme === 'dark'" class="w-4 h-4 text-amber-400" />
                <Moon v-else class="w-4 h-4 text-slate-500" />
              </button>
            </div>
            <h2 class="text-2xl font-bold font-heading text-slate-900 dark:text-slate-100 tracking-tight">
              Portal do Usuário
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
              Informe seu e-mail e senha para acessar os módulos
            </p>
          </header>

          <!-- Alerta de Erro de Autenticação -->
          <div
            v-if="errorMessage"
            class="mb-5 p-3 rounded-lg bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900/60 text-red-700 dark:text-red-300 text-xs flex items-start gap-2.5 animate-in fade-in slide-in-from-top-1"
          >
            <AlertCircle class="w-4 h-4 text-red-600 dark:text-red-400 shrink-0 mt-0.5" />
            <div class="flex-1">
              <p class="font-semibold">Erro de autenticação</p>
              <p class="text-[11px] opacity-90 mt-0.5">{{ errorMessage }}</p>
            </div>
          </div>

          <!-- Formulário -->
          <form @submit.prevent="handleLogin" class="space-y-4">
            <!-- Campo E-mail / Usuário -->
            <div>
              <label for="login_email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                E-mail ou Usuário <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                  <Mail class="w-4 h-4" />
                </div>
                <input
                  id="login_email"
                  v-model="email"
                  type="text"
                  required
                  autocomplete="username"
                  placeholder="admin@redepronta.com"
                  class="w-full pl-9 pr-3.5 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#FC6714] focus:border-transparent transition-all shadow-2xs"
                />
              </div>
            </div>

            <!-- Campo Senha -->
            <div>
              <div class="flex items-center justify-between mb-1.5">
                <label for="login_password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                  Senha <span class="text-red-500">*</span>
                </label>
                <a
                  href="#login"
                  @click.prevent="alertForgotPassword"
                  class="text-[11px] text-[#FC6714] hover:underline font-medium transition cursor-pointer"
                >
                  Esqueceu a senha?
                </a>
              </div>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                  <Lock class="w-4 h-4" />
                </div>
                <input
                  id="login_password"
                  v-model="password"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  autocomplete="current-password"
                  placeholder="••••••••"
                  class="w-full pl-9 pr-10 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#FC6714] focus:border-transparent transition-all shadow-2xs"
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition cursor-pointer"
                  tabindex="-1"
                  :title="showPassword ? 'Ocultar senha' : 'Exibir senha'"
                >
                  <EyeOff v-if="showPassword" class="w-4 h-4" />
                  <Eye v-else class="w-4 h-4" />
                </button>
              </div>
            </div>

            <!-- Lembrar de mim -->
            <div class="flex items-center justify-between pt-1">
              <label class="flex items-center gap-2 cursor-pointer select-none text-xs text-slate-600 dark:text-slate-400">
                <input
                  type="checkbox"
                  v-model="remember"
                  class="w-4 h-4 rounded-sm border-slate-300 text-[#FC6714] focus:ring-[#FC6714]"
                />
                <span>Lembrar minhas credenciais</span>
              </label>
            </div>

            <!-- Botão de Submit ENTRAR -->
            <button
              type="submit"
              :disabled="isLoading"
              class="w-full mt-3 py-3 px-4 rounded-lg bg-[#FC6714] hover:bg-[#e05609] active:scale-[0.99] text-white font-bold text-sm tracking-wider uppercase transition shadow-md shadow-[#FC6714]/25 flex items-center justify-center gap-2 cursor-pointer disabled:opacity-75 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FC6714]"
            >
              <Loader2 v-if="isLoading" class="w-4 h-4 animate-spin" />
              <span>{{ isLoading ? 'Entrando no Sistema...' : 'Entrar' }}</span>
              <ArrowRight v-if="!isLoading" class="w-4 h-4" />
            </button>
          </form>

          <!-- Dica Rápida de Acesso em Desenvolvimento -->
          <div class="mt-5 p-3 rounded-lg bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 text-[11px] text-slate-500 dark:text-slate-400">
            <span class="font-bold text-slate-700 dark:text-slate-300">Acesso Matriz Padrão:</span>
            <div class="mt-1 flex items-center justify-between text-slate-600 dark:text-slate-400">
              <code>admin@redepronta.com</code>
              <button
                type="button"
                @click="fillAdminCredentials"
                class="text-[#FC6714] font-semibold hover:underline cursor-pointer"
              >
                Preencher
              </button>
            </div>
          </div>
        </div>

        <!-- Rodapé com Copyright e Créditos -->
        <footer class="mt-8 pt-4 border-t border-slate-100 dark:border-slate-800 text-center text-[11px] text-slate-400 dark:text-slate-500">
          <p class="font-medium text-slate-600 dark:text-slate-400">RedePronta Tecnologia da Informação</p>
          <p>© 2000-2026 • Todos os direitos reservados</p>
        </footer>
      </section>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Mail, Lock, Eye, EyeOff, AlertCircle, ArrowRight, Loader2, Sun, Moon } from 'lucide-vue-next';
import { useAuth } from '../composables/useAuth';
import { useNavigation } from '../composables/useNavigation';
import { useTheme } from '../composables/useTheme';

const { login } = useAuth();
const { setView } = useNavigation();
const { theme, toggleTheme } = useTheme();

const email = ref('admin@redepronta.com');
const password = ref('');
const remember = ref(true);
const showPassword = ref(false);
const isLoading = ref(false);
const errorMessage = ref('');

const fillAdminCredentials = () => {
  email.value = 'admin@redepronta.com';
  password.value = 'password';
};

const alertForgotPassword = () => {
  window.alert('Para redefinir sua senha, solicite suporte ao Administrador Geral da sua empresa ou contate suporte@redepronta.com.');
};

const handleLogin = async () => {
  if (!email.value || !password.value) {
    errorMessage.value = 'Por favor, preencha todos os campos obrigatórios.';
    return;
  }

  isLoading.value = true;
  errorMessage.value = '';

  const res = await login({
    email: email.value.trim(),
    password: password.value,
    remember: remember.value,
  });

  isLoading.value = false;

  if (res.success) {
    // Redireciona para o ERP
    setView('people');
  } else {
    errorMessage.value = res.message || 'Credenciais inválidas.';
  }
};
</script>
