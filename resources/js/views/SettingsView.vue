<template>
  <!-- Container Raiz Canônico Obrigatório (py-8 w-full space-y-6) -->
  <div class="py-8 w-full space-y-6 font-sans">
    <!-- Topo Canônico da Página: BasePageHeader -->
    <BasePageHeader
      :breadcrumb-items="[
        { label: 'Início', href: '#people' },
        { label: 'Configurações', href: '#settings' },
        { label: activeTab === 'profile' ? 'Meu Perfil' : 'Negócio & Marca' },
      ]"
      title="Configurações do Sistema"
      description="Gerenciamento de perfil individual, credenciais de acesso e identidade corporativa da organização."
      :icon="Settings"
      icon-color-class="bg-orange-50 dark:bg-[#FC6714]/15 border border-orange-200 dark:border-[#FC6714]/30 text-[#FC6714]"
      :badge-text="activeTab === 'profile' ? 'Meu Perfil' : 'Negócio & Marca'"
      badge-variant="primary"
    >
      <template #breadcrumb-right>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
          Tenant: {{ tenantName }}
        </span>
      </template>
    </BasePageHeader>

    <!-- Layout Dual-Pane: Submenu Lateral Interno + Painel de Conteúdo -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
      <!-- Coluna da Esquerda: Submenu Lateral Interno de Configurações (4 cols no desktop) -->
      <aside class="md:col-span-3 space-y-4">
        <div class="p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-1">
          <!-- Grupo: Pessoal -->
          <div class="px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
            Conta & Preferências
          </div>

          <!-- Item Meu Perfil -->
          <button
            type="button"
            @click="setTab('profile')"
            :class="activeTab === 'profile'
              ? 'bg-[#FC6714]/15 text-[#FC6714] font-semibold border-l-2 border-[#FC6714]'
              : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 font-medium border-l-2 border-transparent'"
            class="w-full flex items-center justify-between px-3 py-2 text-xs rounded-lg transition-colors cursor-pointer text-left focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
          >
            <div class="flex items-center gap-2.5">
              <User class="w-4 h-4 shrink-0" :class="activeTab === 'profile' ? 'text-[#FC6714]' : 'text-slate-400'" />
              <span>Meu Perfil</span>
            </div>
            <ChevronRight class="w-3.5 h-3.5 opacity-60" />
          </button>

          <!-- Grupo: Organização -->
          <div class="px-3 pt-3 pb-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
            Organização
          </div>

          <!-- Item Negócio (Tenant) -->
          <button
            type="button"
            @click="setTab('business')"
            :class="activeTab === 'business'
              ? 'bg-[#FC6714]/15 text-[#FC6714] font-semibold border-l-2 border-[#FC6714]'
              : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 font-medium border-l-2 border-transparent'"
            class="w-full flex items-center justify-between px-3 py-2 text-xs rounded-lg transition-colors cursor-pointer text-left focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
          >
            <div class="flex items-center gap-2.5">
              <Building2 class="w-4 h-4 shrink-0" :class="activeTab === 'business' ? 'text-[#FC6714]' : 'text-slate-400'" />
              <span>Negócio & Marca</span>
            </div>
            <span
              v-if="isSuperAdmin || isAdmin"
              class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-orange-100 dark:bg-[#FC6714]/20 text-[#FC6714]"
            >
              Admin
            </span>
          </button>

          <!-- Grupo: Recursos Futuros -->
          <div class="px-3 pt-3 pb-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
            Avançado
          </div>

          <div class="flex items-center justify-between px-3 py-2 text-xs font-normal text-slate-400 dark:text-slate-500 rounded-lg cursor-not-allowed">
            <div class="flex items-center gap-2.5">
              <Shield class="w-4 h-4 opacity-50 shrink-0" />
              <span>Segurança & Sessões</span>
            </div>
            <span class="text-[9px] uppercase px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-400 font-semibold">Em breve</span>
          </div>

          <div class="flex items-center justify-between px-3 py-2 text-xs font-normal text-slate-400 dark:text-slate-500 rounded-lg cursor-not-allowed">
            <div class="flex items-center gap-2.5">
              <Bell class="w-4 h-4 opacity-50 shrink-0" />
              <span>Notificações</span>
            </div>
            <span class="text-[9px] uppercase px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-400 font-semibold">Em breve</span>
          </div>
        </div>

        <!-- Card de Resumo do Usuário / Tenant -->
        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400 space-y-2">
          <div class="flex items-center justify-between text-[11px]">
            <span class="font-bold text-slate-700 dark:text-slate-300">Ambiente</span>
            <span class="font-mono text-emerald-600 dark:text-emerald-400 font-bold">Produção SaaS</span>
          </div>
          <div class="flex items-center justify-between text-[11px]">
            <span class="font-bold text-slate-700 dark:text-slate-300">Subdomínio</span>
            <span class="font-mono text-slate-700 dark:text-slate-300">{{ currentTenant?.subdomain || 'matriz' }}</span>
          </div>
          <div class="flex items-center justify-between text-[11px]">
            <span class="font-bold text-slate-700 dark:text-slate-300">Status</span>
            <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">Ativo</span>
          </div>
        </div>
      </aside>

      <!-- Coluna da Direita: Área de Conteúdo Ativo (9 cols no desktop) -->
      <section class="md:col-span-9 space-y-6">
        <!-- ========================================================================= -->
        <!-- ABA 1: MEU PERFIL (PROFILE)                                              -->
        <!-- ========================================================================= -->
        <div v-if="activeTab === 'profile'" class="space-y-6">
          <!-- Card 1: Avatar & Identificação do Usuário -->
          <div class="p-5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-4">
            <h3 class="text-sm font-bold font-heading text-[#172554] dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
              <Camera class="w-4 h-4 text-[#FC6714]" />
              <span>Foto de Perfil & Identificação</span>
            </h3>

            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 pt-1">
              <!-- Avatar Preview -->
              <div class="relative group">
                <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-[#FC6714]/40 bg-[#FC6714]/15 flex items-center justify-center text-[#FC6714] shadow-md">
                  <img
                    v-if="currentUser?.avatar_url"
                    :src="currentUser.avatar_url"
                    alt="Foto de Perfil"
                    class="w-full h-full object-cover"
                  />
                  <span v-else class="text-2xl font-bold font-heading">
                    {{ (currentUser?.name || 'M').charAt(0).toUpperCase() }}
                  </span>
                </div>
              </div>

              <!-- Controles de Upload de Foto -->
              <div class="space-y-2 flex-1 text-center sm:text-left">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5">
                  <input
                    ref="avatarInputRef"
                    type="file"
                    accept="image/png,image/jpeg,image/webp,image/svg+xml"
                    class="hidden"
                    @change="onAvatarFileSelected"
                  />
                  <button
                    type="button"
                    @click="triggerAvatarUpload"
                    :disabled="isUploadingAvatar"
                    class="inline-flex items-center gap-1.5 h-8.5 px-3 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] text-white text-xs font-semibold shadow-2xs transition cursor-pointer disabled:opacity-50"
                  >
                    <Upload class="w-3.5 h-3.5" :class="{ 'animate-spin': isUploadingAvatar }" />
                    <span>{{ isUploadingAvatar ? 'Enviando...' : 'Carregar Nova Foto' }}</span>
                  </button>

                  <button
                    v-if="currentUser?.avatar_url"
                    type="button"
                    @click="handleRemoveAvatar"
                    :disabled="isUploadingAvatar"
                    class="inline-flex items-center gap-1.5 h-8.5 px-3 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-red-500 dark:hover:text-red-400 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-semibold transition cursor-pointer"
                  >
                    <Trash2 class="w-3.5 h-3.5" />
                    <span>Remover</span>
                  </button>
                </div>

                <p class="text-[11px] text-slate-400 leading-normal">
                  Formatos aceitos: JPG, PNG, WEBP ou SVG até 2 MB. Um editor de corte quadrado (1:1) abrirá ao selecionar a foto.
                </p>

                <!-- Badges de Cargo e Privilégios -->
                <div class="flex items-center gap-2 pt-1 flex-wrap">
                  <span
                    v-if="currentUser?.is_super_admin"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800"
                  >
                    <Crown class="w-3 h-3 text-amber-600" />
                    <span>Super Admin (Acesso Livre)</span>
                  </span>
                  <span
                    v-else-if="currentUser?.role"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800"
                  >
                    <ShieldCheck class="w-3 h-3 text-blue-600" />
                    <span>Papel: {{ currentUser.role.name }}</span>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 2: Dados Pessoais do Usuário -->
          <div class="p-5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-4">
            <h3 class="text-sm font-bold font-heading text-[#172554] dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
              <UserCheck class="w-4 h-4 text-[#FC6714]" />
              <span>Informações Pessoais</span>
            </h3>

            <form @submit.prevent="handleSaveProfile" class="space-y-4 pt-1">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Nome Completo <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="profileForm.name"
                    type="text"
                    required
                    class="w-full h-10 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs font-medium focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 transition"
                    placeholder="Seu nome completo"
                  />
                </div>

                <div>
                  <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    E-mail Corporativo <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="profileForm.email"
                    type="email"
                    required
                    class="w-full h-10 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs font-medium focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 transition"
                    placeholder="seu.email@redepronta.com"
                  />
                </div>
              </div>

              <div class="flex justify-end pt-2">
                <button
                  type="submit"
                  :disabled="isSavingProfile"
                  class="inline-flex items-center gap-2 h-9 px-4 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white text-xs font-semibold shadow-2xs transition cursor-pointer disabled:opacity-50"
                >
                  <Save class="w-3.5 h-3.5" :class="{ 'animate-spin': isSavingProfile }" />
                  <span>{{ isSavingProfile ? 'Salvando...' : 'Salvar Alterações' }}</span>
                </button>
              </div>
            </form>
          </div>

          <!-- Card 3: Alteração de Senha -->
          <div class="p-5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-4">
            <h3 class="text-sm font-bold font-heading text-[#172554] dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
              <KeyRound class="w-4 h-4 text-[#FC6714]" />
              <span>Segurança & Senha de Acesso</span>
            </h3>

            <form @submit.prevent="handleSavePassword" class="space-y-4 pt-1">
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Senha Atual <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="passwordForm.current_password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="w-full h-10 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs font-medium focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 transition"
                    placeholder="••••••••"
                  />
                </div>

                <div>
                  <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Nova Senha <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="passwordForm.password"
                    type="password"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    class="w-full h-10 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs font-medium focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 transition"
                    placeholder="Mínimo 8 caracteres"
                  />
                </div>

                <div>
                  <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Confirmar Nova Senha <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="passwordForm.password_confirmation"
                    type="password"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    class="w-full h-10 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs font-medium focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 transition"
                    placeholder="Repita a nova senha"
                  />
                </div>
              </div>

              <div class="flex justify-end pt-2">
                <button
                  type="submit"
                  :disabled="isSavingPassword"
                  class="inline-flex items-center gap-2 h-9 px-4 rounded-lg bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-semibold shadow-2xs transition cursor-pointer disabled:opacity-50"
                >
                  <Lock class="w-3.5 h-3.5" :class="{ 'animate-spin': isSavingPassword }" />
                  <span>{{ isSavingPassword ? 'Atualizando...' : 'Atualizar Senha' }}</span>
                </button>
              </div>
            </form>
          </div>

          <!-- Card 4: Preferências de Interface do Usuário -->
          <div class="p-5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-4">
            <h3 class="text-sm font-bold font-heading text-[#172554] dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
              <SlidersHorizontal class="w-4 h-4 text-[#FC6714]" />
              <span>Preferências de Interface & Ergonomia</span>
            </h3>

            <div class="space-y-4 pt-1">
              <!-- Tema Padrão -->
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                  <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Tema do ERP</h4>
                  <p class="text-[11px] text-slate-400">Escolha o visual padrão da sua sessão.</p>
                </div>
                <div class="inline-flex items-center p-1 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 shadow-2xs">
                  <button
                    type="button"
                    @click="setPreferenceTheme('light')"
                    :class="preferences.theme === 'light'
                      ? 'bg-[#FC6714] text-white font-bold shadow-2xs'
                      : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="h-7 px-2.5 rounded-md text-xs transition flex items-center gap-1.5 cursor-pointer"
                  >
                    <Sun class="w-3.5 h-3.5" />
                    <span>Claro</span>
                  </button>
                  <button
                    type="button"
                    @click="setPreferenceTheme('dark')"
                    :class="preferences.theme === 'dark'
                      ? 'bg-[#FC6714] text-white font-bold shadow-2xs'
                      : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="h-7 px-2.5 rounded-md text-xs transition flex items-center gap-1.5 cursor-pointer"
                  >
                    <Moon class="w-3.5 h-3.5" />
                    <span>Escuro</span>
                  </button>
                  <button
                    type="button"
                    @click="setPreferenceTheme('system')"
                    :class="preferences.theme === 'system'
                      ? 'bg-[#FC6714] text-white font-bold shadow-2xs'
                      : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="h-7 px-2.5 rounded-md text-xs transition flex items-center gap-1.5 cursor-pointer"
                  >
                    <Laptop class="w-3.5 h-3.5" />
                    <span>Sistema</span>
                  </button>
                </div>
              </div>

              <!-- Densidade de Tabelas -->
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                  <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Densidade de Listas & Tabelas</h4>
                  <p class="text-[11px] text-slate-400">Espaçamento vertical das linhas de dados do sistema.</p>
                </div>
                <div class="inline-flex items-center p-1 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 shadow-2xs">
                  <button
                    type="button"
                    @click="preferences.table_density = 'comfortable'; savePreferencesDebounced()"
                    :class="preferences.table_density === 'comfortable'
                      ? 'bg-[#FC6714] text-white font-bold shadow-2xs'
                      : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="h-7 px-2.5 rounded-md text-xs transition flex items-center gap-1.5 cursor-pointer"
                  >
                    <span>Confortável</span>
                  </button>
                  <button
                    type="button"
                    @click="preferences.table_density = 'compact'; savePreferencesDebounced()"
                    :class="preferences.table_density === 'compact'
                      ? 'bg-[#FC6714] text-white font-bold shadow-2xs'
                      : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="h-7 px-2.5 rounded-md text-xs transition flex items-center gap-1.5 cursor-pointer"
                  >
                    <span>Compacta</span>
                  </button>
                </div>
              </div>

              <!-- Sons de Notificação -->
              <div class="flex items-center justify-between gap-3">
                <div>
                  <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Efeitos Sonoros</h4>
                  <p class="text-[11px] text-slate-400">Tocar sinal sonoro em novas tarefas recebidas e avisos críticos.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer select-none">
                  <input
                    type="checkbox"
                    v-model="preferences.sound_enabled"
                    @change="savePreferencesDebounced"
                    class="sr-only peer"
                  />
                  <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-800 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#FC6714]"></div>
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- ========================================================================= -->
        <!-- ABA 2: NEGÓCIO & MARCA (BUSINESS / TENANT)                                 -->
        <!-- ========================================================================= -->
        <div v-else-if="activeTab === 'business'" class="space-y-6">
          <!-- Bloqueio de Acesso para não-admins -->
          <div
            v-if="!isSuperAdmin && !isAdmin"
            class="p-6 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200 space-y-2 text-center"
          >
            <ShieldAlert class="w-8 h-8 mx-auto text-amber-500" />
            <h3 class="text-sm font-bold">Acesso Restrito ao Gestor do Negócio</h3>
            <p class="text-xs max-w-md mx-auto">
              Apenas administradores ou Super Admins têm permissão para alterar os dados cadastrais, logotipo e favicon da organização.
            </p>
          </div>

          <template v-else>
            <!-- Card 1: Identidade Visual (Logo & Favicon) -->
            <div class="p-5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-5">
              <h3 class="text-sm font-bold font-heading text-[#172554] dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                <Palette class="w-4 h-4 text-[#FC6714]" />
                <span>Identidade Visual do Negócio (Branding)</span>
              </h3>

              <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-1">
                <!-- Seção 1.1: Logotipo Institucional da Empresa -->
                <div class="space-y-3 p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40">
                  <div class="flex items-center justify-between">
                    <div>
                      <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Logotipo Institucional</h4>
                      <p class="text-[11px] text-slate-400">Exibido no topo da Sidebar e nos relatórios.</p>
                    </div>
                  </div>

                  <!-- Preview da Logo -->
                  <div class="h-24 rounded-lg border-2 border-dashed border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 flex items-center justify-center p-3 overflow-hidden">
                    <img
                      v-if="tenantLogo"
                      :src="tenantLogo"
                      alt="Logotipo da Empresa"
                      class="max-h-full max-w-full object-contain"
                    />
                    <div v-else class="flex items-center gap-2.5 text-slate-400">
                      <div class="w-9 h-9 rounded-lg bg-[#FC6714] text-white flex items-center justify-center font-heading font-bold text-sm">
                        RP
                      </div>
                      <span class="text-xs italic">Logo padrão RP ativa</span>
                    </div>
                  </div>

                  <!-- Ações da Logo -->
                  <div class="flex items-center gap-2">
                    <input
                      ref="logoInputRef"
                      type="file"
                      accept="image/png,image/svg+xml,image/jpeg,image/webp"
                      class="hidden"
                      @change="onLogoFileSelected"
                    />
                    <button
                      type="button"
                      @click="triggerLogoUpload"
                      :disabled="isUploadingLogo"
                      class="flex-1 inline-flex items-center justify-center gap-1.5 h-8.5 px-3 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] text-white text-xs font-semibold shadow-2xs transition cursor-pointer disabled:opacity-50"
                    >
                      <Upload class="w-3.5 h-3.5" :class="{ 'animate-spin': isUploadingLogo }" />
                      <span>{{ isUploadingLogo ? 'Enviando...' : 'Carregar Logo' }}</span>
                    </button>

                    <button
                      v-if="tenantLogo"
                      type="button"
                      @click="handleRemoveLogo"
                      :disabled="isUploadingLogo"
                      class="inline-flex items-center justify-center h-8.5 px-3 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-red-500 dark:hover:text-red-400 hover:bg-white dark:hover:bg-slate-800 text-xs font-semibold transition cursor-pointer"
                    >
                      <Trash2 class="w-3.5 h-3.5" />
                    </button>
                  </div>
                  <p class="text-[10px] text-slate-400">PNG, SVG ou JPG até 3 MB com fundo transparente. Editor de corte (4:1) abre ao selecionar.</p>
                </div>

                <!-- Seção 1.2: Favicon da Aba do Navegador -->
                <div class="space-y-3 p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40">
                  <div class="flex items-center justify-between">
                    <div>
                      <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Favicon da Aba</h4>
                      <p class="text-[11px] text-slate-400">Ícone que aparece na aba do navegador.</p>
                    </div>
                  </div>

                  <!-- Simulação de Aba do Navegador -->
                  <div class="h-24 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-200/70 dark:bg-slate-950 p-2 flex flex-col justify-end">
                    <div class="bg-white dark:bg-slate-900 rounded-t-lg px-3 py-1.5 flex items-center gap-2 border-t border-x border-slate-300 dark:border-slate-700 shadow-xs max-w-[240px]">
                      <div class="w-4 h-4 rounded shrink-0 flex items-center justify-center overflow-hidden">
                        <img
                          v-if="tenantFavicon"
                          :src="tenantFavicon"
                          alt="Favicon"
                          class="w-full h-full object-contain"
                        />
                        <div v-else class="w-3.5 h-3.5 rounded bg-[#FC6714] text-[8px] text-white font-bold flex items-center justify-center">
                          RP
                        </div>
                      </div>
                      <span class="text-[11px] font-medium text-slate-700 dark:text-slate-300 truncate">
                        {{ tenantForm.trading_name || tenantName }} • ERP
                      </span>
                    </div>
                  </div>

                  <!-- Ações do Favicon -->
                  <div class="flex items-center gap-2">
                    <input
                      ref="faviconInputRef"
                      type="file"
                      accept="image/x-icon,image/png,image/svg+xml"
                      class="hidden"
                      @change="onFaviconFileSelected"
                    />
                    <button
                      type="button"
                      @click="triggerFaviconUpload"
                      :disabled="isUploadingFavicon"
                      class="flex-1 inline-flex items-center justify-center gap-1.5 h-8.5 px-3 rounded-lg bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-semibold shadow-2xs transition cursor-pointer disabled:opacity-50"
                    >
                      <Upload class="w-3.5 h-3.5" :class="{ 'animate-spin': isUploadingFavicon }" />
                      <span>{{ isUploadingFavicon ? 'Enviando...' : 'Carregar Favicon' }}</span>
                    </button>

                    <button
                      v-if="tenantFavicon"
                      type="button"
                      @click="handleRemoveFavicon"
                      :disabled="isUploadingFavicon"
                      class="inline-flex items-center justify-center h-8.5 px-3 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-red-500 dark:hover:text-red-400 hover:bg-white dark:hover:bg-slate-800 text-xs font-semibold transition cursor-pointer"
                    >
                      <Trash2 class="w-3.5 h-3.5" />
                    </button>
                  </div>
                  <p class="text-[10px] text-slate-400">ICO, PNG ou SVG. Editor de corte quadrado (1:1) abre ao selecionar.</p>
                </div>
              </div>

              <!-- Seção 1.3: Cor Primária da Marca -->
              <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                  <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Cor Institucional Primária</h4>
                  <p class="text-[11px] text-slate-400">Tom de destaque oficial do negócio no ERP.</p>
                </div>
                <div class="flex items-center gap-2">
                  <div
                    v-for="color in brandPresets"
                    :key="color"
                    @click="tenantForm.brand_color = color"
                    :style="{ backgroundColor: color }"
                    class="w-6 h-6 rounded-md cursor-pointer transition transform hover:scale-110 shadow-2xs border border-white/20 flex items-center justify-center"
                  >
                    <Check v-if="tenantForm.brand_color?.toLowerCase() === color.toLowerCase()" class="w-3.5 h-3.5 text-white" />
                  </div>
                  <div class="flex items-center gap-1.5 pl-2 border-l border-slate-200 dark:border-slate-800">
                    <input
                      type="color"
                      v-model="tenantForm.brand_color"
                      class="w-7 h-7 rounded border-0 cursor-pointer p-0 bg-transparent"
                    />
                    <span class="font-mono text-xs text-slate-600 dark:text-slate-300 font-semibold">{{ tenantForm.brand_color }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card 2: Informações Cadastrais da Empresa -->
            <div class="p-5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-4">
              <h3 class="text-sm font-bold font-heading text-[#172554] dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                <Building class="w-4 h-4 text-[#FC6714]" />
                <span>Dados Corporativos do Negócio</span>
              </h3>

              <form @submit.prevent="handleSaveTenant" class="space-y-4 pt-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                      Razão Social <span class="text-red-500">*</span>
                    </label>
                    <input
                      v-model="tenantForm.name"
                      type="text"
                      required
                      class="w-full h-10 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs font-medium focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 transition"
                      placeholder="Ex: Rede Pronta Telecomunicações Ltda"
                    />
                  </div>

                  <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                      Nome Fantasia (Marca do Sistema)
                    </label>
                    <input
                      v-model="tenantForm.trading_name"
                      type="text"
                      class="w-full h-10 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs font-medium focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 transition"
                      placeholder="Ex: Rede Pronta"
                    />
                  </div>

                  <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                      CNPJ / Documento
                    </label>
                    <input
                      v-model="tenantForm.document"
                      type="text"
                      class="w-full h-10 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs font-medium focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 transition"
                      placeholder="00.000.000/0001-00"
                    />
                  </div>

                  <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                      Subdomínio do Tenant (Fixo)
                    </label>
                    <input
                      :value="currentTenant?.subdomain || 'matriz'"
                      type="text"
                      disabled
                      class="w-full h-10 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 text-slate-500 font-mono text-xs cursor-not-allowed"
                    />
                  </div>

                  <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                      E-mail de Contato Principal
                    </label>
                    <input
                      v-model="tenantForm.email"
                      type="email"
                      class="w-full h-10 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs font-medium focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 transition"
                      placeholder="contato@redepronta.com"
                    />
                  </div>

                  <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                      Telefone / Central
                    </label>
                    <input
                      v-model="tenantForm.phone"
                      type="text"
                      class="w-full h-10 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs font-medium focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 transition"
                      placeholder="(11) 4000-0000"
                    />
                  </div>
                </div>

                <div>
                  <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Slogan / Subtítulo Institucional
                  </label>
                  <input
                    v-model="tenantForm.slogan"
                    type="text"
                    class="w-full h-10 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs font-medium focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 transition"
                    placeholder="Ex: ERP & Field Service Management"
                  />
                </div>

                <div class="flex justify-end pt-2">
                  <button
                    type="submit"
                    :disabled="isSavingTenant"
                    class="inline-flex items-center gap-2 h-9 px-4 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white text-xs font-semibold shadow-2xs transition cursor-pointer disabled:opacity-50"
                  >
                    <Save class="w-3.5 h-3.5" :class="{ 'animate-spin': isSavingTenant }" />
                    <span>{{ isSavingTenant ? 'Salvando...' : 'Salvar Configurações do Negócio' }}</span>
                  </button>
                </div>
              </form>
            </div>
          </template>
        </div>
      </section>
    </div>

    <!-- Toast Notification Canônico -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="transform translate-y-2 opacity-0"
      enter-to-class="transform translate-y-0 opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="transform translate-y-0 opacity-100"
      leave-to-class="transform translate-y-2 opacity-0"
    >
      <div
        v-if="toastMessage"
        class="fixed bottom-12 right-6 z-50 flex items-center gap-2.5 px-4 py-3 rounded-xl shadow-xl text-xs font-semibold text-white"
        :class="toastType === 'success' ? 'bg-emerald-600' : 'bg-red-600'"
      >
        <CheckCircle2 v-if="toastType === 'success'" class="w-4 h-4 shrink-0" />
        <AlertCircle v-else class="w-4 h-4 shrink-0" />
        <span>{{ toastMessage }}</span>
      </div>
    </Transition>

    <!-- Modal Canônico de Recorte de Imagem (Logo / Favicon / Avatar) -->
    <ImageCropperModal
      v-model="isCropperOpen"
      :image-src="cropperImageSrc"
      :aspect-ratio="cropperAspectRatio"
      :crop-type="cropperType"
      :title="cropperTitle"
      :description="cropperDescription"
      :is-processing="isUploadingCropped"
      @crop="onCropperFinished"
      @cancel="onCropperCancelled"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import axios from 'axios';
import {
  Settings,
  User,
  Building2,
  Shield,
  Bell,
  Camera,
  Upload,
  Trash2,
  Crown,
  ShieldCheck,
  ShieldAlert,
  UserCheck,
  Save,
  KeyRound,
  Lock,
  SlidersHorizontal,
  Sun,
  Moon,
  Laptop,
  Palette,
  Check,
  Building,
  ChevronRight,
  CheckCircle2,
  AlertCircle,
} from 'lucide-vue-next';
import BasePageHeader from '../components/common/BasePageHeader.vue';
import ImageCropperModal from '../components/common/ImageCropperModal.vue';
import { useAuth } from '../composables/useAuth';
import { useTenant } from '../composables/useTenant';
import { useTheme } from '../composables/useTheme';

type SettingsTab = 'profile' | 'business';

const { currentUser, isSuperAdmin, setCurrentUser, fetchCurrentUser } = useAuth();
const {
  currentTenant,
  tenantName,
  tenantLogo,
  tenantFavicon,
  fetchTenant,
  setTenantData,
  applyFavicon,
} = useTenant();
const { theme, toggleTheme } = useTheme();

const isAdmin = computed(() => {
  return isSuperAdmin.value || (currentUser.value?.role && ['admin', 'administrador'].includes(currentUser.value.role.slug));
});

// Leitura da aba via URL Hash ou localStorage
function getInitialTab(): SettingsTab {
  try {
    const hash = window.location.hash || '';
    const params = new URLSearchParams(hash.split('?')[1] || '');
    const tabParam = params.get('tab') as SettingsTab;
    if (tabParam && ['profile', 'business'].includes(tabParam)) {
      return tabParam;
    }
    const saved = localStorage.getItem('rp_settings_active_tab') as SettingsTab;
    if (saved && ['profile', 'business'].includes(saved)) {
      return saved;
    }
  } catch (e) {}
  return 'profile';
}

const activeTab = ref<SettingsTab>(getInitialTab());

function setTab(tab: SettingsTab) {
  activeTab.value = tab;
  try {
    localStorage.setItem('rp_settings_active_tab', tab);
    history.replaceState(null, '', `#settings?tab=${tab}`);
  } catch (e) {}
}

// Toast
const toastMessage = ref('');
const toastType = ref<'success' | 'error'>('success');
let toastTimer: any = null;

const showToast = (msg: string, type: 'success' | 'error' = 'success') => {
  toastMessage.value = msg;
  toastType.value = type;
  if (toastTimer) clearTimeout(toastTimer);
  toastTimer = setTimeout(() => {
    toastMessage.value = '';
  }, 4000);
};

// ===========================================================================
// PERFIL DO USUÁRIO
// ===========================================================================
const profileForm = reactive({
  name: '',
  email: '',
});

const passwordForm = reactive({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const preferences = reactive({
  theme: 'system' as 'system' | 'light' | 'dark',
  table_density: 'comfortable' as 'comfortable' | 'compact',
  sound_enabled: true,
});

const isSavingProfile = ref(false);
const isSavingPassword = ref(false);
const isUploadingAvatar = ref(false);
const avatarInputRef = ref<HTMLInputElement | null>(null);

// Estado e Ações do Cropper de Imagens
const isCropperOpen = ref(false);
const cropperImageSrc = ref('');
const cropperAspectRatio = ref(4);
const cropperType = ref<'logo' | 'favicon' | 'avatar'>('logo');
const cropperTitle = ref('');
const cropperDescription = ref('');
const isUploadingCropped = ref(false);

function openImageCropper(file: File, type: 'logo' | 'favicon' | 'avatar') {
  const reader = new FileReader();
  reader.onload = (e) => {
    cropperImageSrc.value = e.target?.result as string;
    cropperType.value = type;
    if (type === 'logo') {
      cropperAspectRatio.value = 4; // 4:1 proporção retangular para a Sidebar
      cropperTitle.value = 'Enquadramento do Logotipo Institucional';
      cropperDescription.value = 'Ajuste a marca na proporção 4:1 para exibição perfeita no topo da Sidebar do ERP.';
    } else if (type === 'favicon') {
      cropperAspectRatio.value = 1; // 1:1 quadrado
      cropperTitle.value = 'Enquadramento do Favicon';
      cropperDescription.value = 'Recorte o ícone quadrado (1:1) para exibição na aba do navegador.';
    } else {
      cropperAspectRatio.value = 1; // 1:1 quadrado
      cropperTitle.value = 'Enquadramento da Foto de Perfil';
      cropperDescription.value = 'Enquadre sua foto de perfil quadrada (1:1).';
    }
    isCropperOpen.value = true;
  };
  reader.readAsDataURL(file);
}

function onCropperCancelled() {
  cropperImageSrc.value = '';
}

async function onCropperFinished(payload: { blob: Blob; file: File; dataUrl: string }) {
  isUploadingCropped.value = true;
  try {
    if (cropperType.value === 'logo') {
      const formData = new FormData();
      formData.append('logo', payload.file);
      isUploadingLogo.value = true;
      const res = await axios.post('/api/v1/settings/tenant/logo', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      if (res.data?.data?.logo_url && currentTenant.value) {
        setTenantData({
          ...currentTenant.value,
          logo_url: res.data.data.logo_url,
        });
        showToast('Logotipo atualizado e aplicado na Sidebar!', 'success');
      }
      isUploadingLogo.value = false;
    } else if (cropperType.value === 'favicon') {
      const formData = new FormData();
      formData.append('favicon', payload.file);
      isUploadingFavicon.value = true;
      const res = await axios.post('/api/v1/settings/tenant/favicon', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      if (res.data?.data?.favicon_url && currentTenant.value) {
        setTenantData({
          ...currentTenant.value,
          favicon_url: res.data.data.favicon_url,
        });
        applyFavicon(res.data.data.favicon_url);
        showToast('Favicon atualizado na aba do navegador!', 'success');
      }
      isUploadingFavicon.value = false;
    } else if (cropperType.value === 'avatar') {
      const formData = new FormData();
      formData.append('avatar', payload.file);
      isUploadingAvatar.value = true;
      const res = await axios.post('/api/v1/settings/profile/avatar', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      if (res.data?.data?.avatar_url && currentUser.value) {
        setCurrentUser({
          ...currentUser.value,
          avatar_url: res.data.data.avatar_url,
        });
        showToast('Foto de perfil atualizada com sucesso!', 'success');
      }
      isUploadingAvatar.value = false;
    }
    isCropperOpen.value = false;
    cropperImageSrc.value = '';
  } catch (err: any) {
    showToast(err.response?.data?.message || 'Erro ao processar e salvar imagem recortada.', 'error');
  } finally {
    isUploadingCropped.value = false;
    isUploadingLogo.value = false;
    isUploadingFavicon.value = false;
    isUploadingAvatar.value = false;
  }
}

function triggerAvatarUpload() {
  avatarInputRef.value?.click();
}

function onAvatarFileSelected(event: Event) {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  if (!file) return;
  openImageCropper(file, 'avatar');
  target.value = '';
}

async function handleRemoveAvatar() {
  if (!confirm('Deseja realmente remover sua foto de perfil?')) return;
  isUploadingAvatar.value = true;
  try {
    await axios.delete('/api/v1/settings/profile/avatar');
    if (currentUser.value) {
      setCurrentUser({
        ...currentUser.value,
        avatar_url: null,
      });
    }
    showToast('Foto de perfil removida com sucesso.', 'success');
  } catch (err: any) {
    showToast('Erro ao remover foto de perfil.', 'error');
  } finally {
    isUploadingAvatar.value = false;
  }
}

async function handleSaveProfile() {
  isSavingProfile.value = true;
  try {
    const res = await axios.put('/api/v1/settings/profile', {
      name: profileForm.name,
      email: profileForm.email,
      preferences: preferences,
    });
    if (currentUser.value) {
      setCurrentUser({
        ...currentUser.value,
        name: profileForm.name,
        email: profileForm.email,
        preferences: preferences,
      });
    }
    showToast('Dados de perfil salvos com sucesso!', 'success');
  } catch (err: any) {
    showToast(err.response?.data?.message || 'Erro ao salvar perfil.', 'error');
  } finally {
    isSavingProfile.value = false;
  }
}

async function handleSavePassword() {
  if (passwordForm.password !== passwordForm.password_confirmation) {
    showToast('A confirmação da nova senha não confere.', 'error');
    return;
  }
  isSavingPassword.value = true;
  try {
    await axios.put('/api/v1/settings/profile/password', passwordForm);
    passwordForm.current_password = '';
    passwordForm.password = '';
    passwordForm.password_confirmation = '';
    showToast('Senha alterada com sucesso!', 'success');
  } catch (err: any) {
    showToast(err.response?.data?.message || 'Erro ao alterar senha.', 'error');
  } finally {
    isSavingPassword.value = false;
  }
}

function setPreferenceTheme(mode: 'system' | 'light' | 'dark') {
  preferences.theme = mode;
  if (mode === 'dark') {
    document.documentElement.classList.add('dark');
  } else if (mode === 'light') {
    document.documentElement.classList.remove('dark');
  }
  savePreferencesDebounced();
}

let prefDebounce: any = null;
function savePreferencesDebounced() {
  clearTimeout(prefDebounce);
  prefDebounce = setTimeout(async () => {
    try {
      await axios.put('/api/v1/settings/profile', {
        name: profileForm.name || currentUser.value?.name,
        email: profileForm.email || currentUser.value?.email,
        preferences: preferences,
      });
      showToast('Preferências salvas!', 'success');
    } catch (e) {}
  }, 500);
}

// ===========================================================================
// NEGÓCIO & MARCA (TENANT)
// ===========================================================================
const brandPresets = ['#FC6714', '#2563EB', '#059669', '#7C3AED', '#0F172A'];

const tenantForm = reactive({
  name: '',
  trading_name: '',
  document: '',
  email: '',
  phone: '',
  brand_color: '#FC6714',
  slogan: '',
});

const isSavingTenant = ref(false);
const isUploadingLogo = ref(false);
const isUploadingFavicon = ref(false);
const logoInputRef = ref<HTMLInputElement | null>(null);
const faviconInputRef = ref<HTMLInputElement | null>(null);

function triggerLogoUpload() {
  logoInputRef.value?.click();
}

function triggerFaviconUpload() {
  faviconInputRef.value?.click();
}

function onLogoFileSelected(event: Event) {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  if (!file) return;
  openImageCropper(file, 'logo');
  target.value = '';
}

async function handleRemoveLogo() {
  if (!confirm('Deseja realmente remover o logotipo da empresa?')) return;
  isUploadingLogo.value = true;
  try {
    await axios.delete('/api/v1/settings/tenant/logo');
    if (currentTenant.value) {
      setTenantData({
        ...currentTenant.value,
        logo_url: null,
      });
    }
    showToast('Logotipo removido. O sistema voltou ao padrão.', 'success');
  } catch (err: any) {
    showToast('Erro ao remover logotipo.', 'error');
  } finally {
    isUploadingLogo.value = false;
  }
}

function onFaviconFileSelected(event: Event) {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  if (!file) return;
  openImageCropper(file, 'favicon');
  target.value = '';
}

async function handleRemoveFavicon() {
  if (!confirm('Deseja realmente remover o favicon da empresa?')) return;
  isUploadingFavicon.value = true;
  try {
    await axios.delete('/api/v1/settings/tenant/favicon');
    if (currentTenant.value) {
      setTenantData({
        ...currentTenant.value,
        favicon_url: null,
      });
    }
    applyFavicon(null);
    showToast('Favicon removido.', 'success');
  } catch (err: any) {
    showToast('Erro ao remover favicon.', 'error');
  } finally {
    isUploadingFavicon.value = false;
  }
}

async function handleSaveTenant() {
  isSavingTenant.value = true;
  try {
    const payload = {
      name: tenantForm.name,
      trading_name: tenantForm.trading_name,
      document: tenantForm.document,
      email: tenantForm.email,
      phone: tenantForm.phone,
      settings: {
        trading_name: tenantForm.trading_name,
        brand_color: tenantForm.brand_color,
        slogan: tenantForm.slogan,
      },
    };
    const res = await axios.put('/api/v1/settings/tenant', payload);
    if (res.data?.data && currentTenant.value) {
      setTenantData({
        ...currentTenant.value,
        ...res.data.data,
      });
    }
    showToast('Configurações do negócio salvas com sucesso!', 'success');
  } catch (err: any) {
    showToast(err.response?.data?.message || 'Erro ao salvar negócio.', 'error');
  } finally {
    isSavingTenant.value = false;
  }
}

// Sincronização inicial de dados
const loadData = () => {
  if (currentUser.value) {
    profileForm.name = currentUser.value.name || '';
    profileForm.email = currentUser.value.email || '';
    if (currentUser.value.preferences) {
      Object.assign(preferences, currentUser.value.preferences);
    }
  }

  if (currentTenant.value) {
    tenantForm.name = currentTenant.value.name || '';
    tenantForm.trading_name = currentTenant.value.trading_name || currentTenant.value.name || '';
    tenantForm.document = currentTenant.value.document || '';
    tenantForm.email = currentTenant.value.email || '';
    tenantForm.phone = currentTenant.value.phone || '';
    tenantForm.brand_color = currentTenant.value.brand_color || '#FC6714';
    tenantForm.slogan = currentTenant.value.slogan || '';
  }
};

onMounted(async () => {
  await Promise.all([fetchCurrentUser(), fetchTenant()]);
  loadData();
});
</script>
