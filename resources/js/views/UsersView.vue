<template>
  <!-- Container Raiz Canônico Obrigatório (py-8 w-full space-y-6) -->
  <div class="py-8 w-full space-y-6 font-sans">
    <!-- Linha 1 Canônica Obrigatória: BaseBreadcrumb em Box 100% -->
    <BaseBreadcrumb
      :items="[
        { label: 'Início', href: '#people' },
        { label: 'Segurança & Governança', href: '#users' },
        { label: 'Usuários, Papéis & Permissões' },
      ]"
    >
      <template #right>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-900">
          Controle de Acesso (RBAC)
        </span>
      </template>
    </BaseBreadcrumb>

    <!-- Linha 2 Canônica: Cabeçalho da Página com Título H1, Badges e Botões de Ação -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-xl bg-indigo-600/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-200/60 dark:border-indigo-800">
          <ShieldCheck class="w-6 h-6" />
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h1 class="text-xl sm:text-2xl font-bold font-heading text-slate-900 dark:text-slate-100 tracking-tight">
              Gestão de Usuários & Acessos
            </h1>
            <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
              {{ stats.users_total }} Usuários
            </span>
          </div>
          <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
            Administração de operadores, perfis de papéis, permissões granulares e acesso livre para Super Admin.
          </p>
        </div>
      </div>

      <!-- Botões de Ação Rápida -->
      <div class="flex items-center gap-2.5 flex-wrap">
        <button
          type="button"
          @click="openNewRoleModal"
          class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold shadow-2xs transition cursor-pointer"
        >
          <Plus class="w-3.5 h-3.5 text-indigo-600" />
          <span>Novo Papel (Role)</span>
        </button>

        <button
          type="button"
          @click="openNewUserModal"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#FC6714] hover:bg-[#e05609] active:scale-[0.99] text-white text-xs font-bold tracking-wide transition shadow-xs cursor-pointer"
        >
          <UserPlus class="w-4 h-4" />
          <span>Novo Usuário</span>
        </button>
      </div>
    </div>

    <!-- Linha 3: Cards de Indicadores / Estatísticas Rápidas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Total de Usuários -->
      <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between">
        <div>
          <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Total de Usuários</p>
          <p class="text-2xl font-bold font-heading text-slate-900 dark:text-slate-100 mt-0.5">
            {{ stats.users_total }}
          </p>
          <p class="text-[11px] text-slate-400 mt-1">
            {{ stats.users_active }} ativos • {{ stats.users_inactive }} inativos
          </p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center">
          <Users class="w-5 h-5" />
        </div>
      </div>

      <!-- Super Admins Livres -->
      <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between">
        <div>
          <div class="flex items-center gap-1.5">
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Super Admins</p>
            <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300">
              Livre
            </span>
          </div>
          <p class="text-2xl font-bold font-heading text-amber-600 dark:text-amber-400 mt-0.5">
            {{ stats.users_super_admin }}
          </p>
          <p class="text-[11px] text-slate-400 mt-1">
            Acesso irrestrito a todas as rotas
          </p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center">
          <Crown class="w-5 h-5" />
        </div>
      </div>

      <!-- Papéis Cadastrados -->
      <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between">
        <div>
          <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Papéis de Acesso</p>
          <p class="text-2xl font-bold font-heading text-slate-900 dark:text-slate-100 mt-0.5">
            {{ rolesList.length }}
          </p>
          <p class="text-[11px] text-slate-400 mt-1">
            Perfis com matriz RBAC
          </p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center">
          <Layers class="w-5 h-5" />
        </div>
      </div>

      <!-- Permissões Catalogadas -->
      <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between">
        <div>
          <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Permissões do Sistema</p>
          <p class="text-2xl font-bold font-heading text-slate-900 dark:text-slate-100 mt-0.5">
            {{ stats.permissions_total }}
          </p>
          <p class="text-[11px] text-slate-400 mt-1">
            Ações auditáveis e mapeadas
          </p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
          <KeyRound class="w-5 h-5" />
        </div>
      </div>
    </div>

    <!-- Navegação por Abas do Módulo -->
    <div class="border-b border-slate-200 dark:border-slate-800 flex items-center gap-2 select-none">
      <button
        type="button"
        @click="currentTab = 'users'"
        :class="currentTab === 'users'
          ? 'border-[#FC6714] text-[#FC6714] font-bold border-b-2'
          : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 font-medium'"
        class="px-4 py-2.5 border-b-2 transition flex items-center gap-2 text-sm cursor-pointer"
      >
        <Users class="w-4 h-4" />
        <span>Usuários do Sistema</span>
        <span class="px-2 py-0.2 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
          {{ stats.users_total }}
        </span>
      </button>

      <button
        type="button"
        @click="currentTab = 'roles'"
        :class="currentTab === 'roles'
          ? 'border-[#FC6714] text-[#FC6714] font-bold border-b-2'
          : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 font-medium'"
        class="px-4 py-2.5 border-b-2 transition flex items-center gap-2 text-sm cursor-pointer"
      >
        <ShieldCheck class="w-4 h-4" />
        <span>Papéis de Usuários (Roles)</span>
        <span class="px-2 py-0.2 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
          {{ rolesList.length }}
        </span>
      </button>

      <button
        type="button"
        @click="currentTab = 'permissions'"
        :class="currentTab === 'permissions'
          ? 'border-[#FC6714] text-[#FC6714] font-bold border-b-2'
          : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 font-medium'"
        class="px-4 py-2.5 border-b-2 transition flex items-center gap-2 text-sm cursor-pointer"
      >
        <KeyRound class="w-4 h-4" />
        <span>Matriz de Permissões</span>
        <span class="px-2 py-0.2 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
          {{ stats.permissions_total }}
        </span>
      </button>
    </div>

    <!-- ============================================================== -->
    <!-- ABA 1: LISTAGEM DE USUÁRIOS DO SISTEMA                        -->
    <!-- ============================================================== -->
    <div v-show="currentTab === 'users'" class="space-y-4">
      <!-- Barra de Filtros e Busca Rápida -->
      <div class="p-3.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 shadow-2xs">
        <div class="flex-1 relative">
          <Search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
          <input
            v-model="filters.search"
            @input="debounceSearch"
            type="text"
            placeholder="Buscar usuário por nome, e-mail ou documento da pessoa..."
            class="w-full pl-9 pr-3.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
          />
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
          <!-- Filtro por Papel -->
          <select
            v-model="filters.role_id"
            @change="loadUsers"
            class="py-1.5 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
          >
            <option :value="null">Todos os Papéis</option>
            <option v-for="r in rolesList" :key="r.id" :value="r.id">
              {{ r.name }}
            </option>
          </select>

          <!-- Filtro por Status -->
          <select
            v-model="filters.status"
            @change="loadUsers"
            class="py-1.5 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
          >
            <option value="">Todos os Status</option>
            <option value="active">Ativos</option>
            <option value="inactive">Inativos</option>
          </select>

          <!-- Filtro Super Admin -->
          <select
            v-model="filters.is_super_admin"
            @change="loadUsers"
            class="py-1.5 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
          >
            <option value="">Todos os Tipos</option>
            <option value="true">Apenas Super Admin (Livre)</option>
            <option value="false">Usuários Regulares</option>
          </select>

          <button
            type="button"
            @click="loadUsers"
            class="p-2 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition cursor-pointer"
            title="Atualizar listagem"
          >
            <RotateCcw class="w-3.5 h-3.5" :class="{ 'animate-spin': isLoadingUsers }" />
          </button>
        </div>
      </div>

      <!-- Tabela de Usuários -->
      <div class="rounded-xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-slate-50 dark:bg-slate-950/70 border-b border-slate-200/80 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider text-[11px]">
              <tr>
                <th class="py-3 px-4">Usuário / Operador</th>
                <th class="py-3 px-4">Papel Atribuído</th>
                <th class="py-3 px-4">Pessoa Vinculada</th>
                <th class="py-3 px-4 text-center">Tipo de Acesso</th>
                <th class="py-3 px-4 text-center">Status</th>
                <th class="py-3 px-4 text-right">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr v-if="isLoadingUsers">
                <td colspan="6" class="py-8 text-center text-slate-500">
                  <div class="inline-flex items-center gap-2">
                    <Loader2 class="w-4 h-4 animate-spin text-[#FC6714]" />
                    <span>Carregando usuários...</span>
                  </div>
                </td>
              </tr>
              <tr v-else-if="usersList.length === 0">
                <td colspan="6" class="py-10 text-center text-slate-500">
                  <UserX class="w-8 h-8 mx-auto text-slate-300 dark:text-slate-600 mb-2" />
                  <p class="font-medium text-slate-700 dark:text-slate-300">Nenhum usuário encontrado</p>
                  <p class="text-[11px] text-slate-400 mt-0.5">Tente ajustar seus filtros ou cadastre um novo usuário.</p>
                </td>
              </tr>
              <tr
                v-for="u in usersList"
                :key="u.id"
                class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors"
              >
                <!-- Avatar & Identificação -->
                <td class="py-3 px-4">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 flex items-center justify-center font-bold text-xs shrink-0">
                      {{ u.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="min-w-0">
                      <p class="font-semibold text-slate-900 dark:text-slate-100 truncate">
                        {{ u.name }}
                      </p>
                      <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                        {{ u.email }}
                      </p>
                    </div>
                  </div>
                </td>

                <!-- Papel Atribuído -->
                <td class="py-3 px-4">
                  <span
                    v-if="u.role"
                    class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-medium bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-900"
                  >
                    <ShieldCheck class="w-3 h-3 text-purple-600" />
                    {{ u.role.name }}
                  </span>
                  <span v-else class="text-slate-400 italic text-[11px]">
                    Sem papel definido
                  </span>
                </td>

                <!-- Pessoa Vinculada -->
                <td class="py-3 px-4">
                  <div v-if="u.person" class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-900">
                      <User class="w-3 h-3" />
                      {{ u.person.name }}
                    </span>
                  </div>
                  <span v-else class="text-slate-400 text-[11px]">
                    Não associado
                  </span>
                </td>

                <!-- Tipo de Acesso (Super Admin vs Regular) -->
                <td class="py-3 px-4 text-center">
                  <span
                    v-if="u.is_super_admin"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-900 shadow-2xs"
                  >
                    <Crown class="w-3 h-3 text-amber-500" />
                    Super Admin (Livre)
                  </span>
                  <span
                    v-else
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300"
                  >
                    Padronizado (RBAC)
                  </span>
                </td>

                <!-- Status -->
                <td class="py-3 px-4 text-center">
                  <span
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                    :class="u.status === 'active'
                      ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900'
                      : 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-900'"
                  >
                    <span class="w-1.5 h-1.5 rounded-full" :class="u.status === 'active' ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                    {{ u.status === 'active' ? 'Ativo' : 'Inativo' }}
                  </span>
                </td>

                <!-- Ações -->
                <td class="py-3 px-4 text-right">
                  <div class="inline-flex items-center gap-1">
                    <button
                      type="button"
                      @click="openEditUserModal(u)"
                      class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                      title="Editar usuário"
                    >
                      <Pencil class="w-3.5 h-3.5" />
                    </button>

                    <button
                      type="button"
                      @click="toggleUserStatus(u)"
                      class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                      :title="u.status === 'active' ? 'Inativar usuário' : 'Ativar usuário'"
                    >
                      <Power class="w-3.5 h-3.5" :class="u.status === 'active' ? 'text-emerald-600' : 'text-slate-400'" />
                    </button>

                    <button
                      type="button"
                      @click="deleteUser(u)"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer"
                      title="Excluir usuário"
                    >
                      <Trash2 class="w-3.5 h-3.5" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginação -->
        <TablePagination
          v-if="pagination.total > pagination.per_page"
          :current-page="pagination.current_page"
          :last-page="pagination.last_page"
          :total="pagination.total"
          :per-page="pagination.per_page"
          @change-page="changeUserPage"
        />
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- ABA 2: PAPÉIS DE USUÁRIO (ROLES)                              -->
    <!-- ============================================================== -->
    <div v-show="currentTab === 'roles'" class="space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="r in rolesList"
          :key="r.id"
          class="p-5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs hover:shadow-sm transition-all flex flex-col justify-between"
        >
          <div>
            <div class="flex items-start justify-between gap-3 mb-2">
              <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-950 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800 flex items-center justify-center font-bold text-xs">
                  <ShieldCheck class="w-4 h-4" />
                </div>
                <div>
                  <h3 class="font-bold text-sm text-slate-900 dark:text-slate-100">
                    {{ r.name }}
                  </h3>
                  <code class="text-[10px] text-slate-400">{{ r.slug }}</code>
                </div>
              </div>

              <span
                v-if="r.is_system"
                class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400"
              >
                Sistema
              </span>
              <span
                v-else
                class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400"
              >
                Customizado
              </span>
            </div>

            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mt-2 leading-relaxed">
              {{ r.description || 'Perfil com configurações e permissões específicas para o tenant.' }}
            </p>

            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-600 dark:text-slate-400">
              <span class="inline-flex items-center gap-1.5 font-medium">
                <Users class="w-3.5 h-3.5 text-slate-400" />
                {{ r.users_count }} {{ r.users_count === 1 ? 'usuário' : 'usuários' }}
              </span>
              <span class="inline-flex items-center gap-1.5 font-medium">
                <KeyRound class="w-3.5 h-3.5 text-indigo-500" />
                {{ r.permissions_count }} permissões
              </span>
            </div>
          </div>

          <div class="mt-5 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
            <button
              type="button"
              @click="openEditRoleModal(r)"
              class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-semibold transition cursor-pointer"
            >
              Editar Papel
            </button>
            <button
              v-if="!r.is_system"
              type="button"
              @click="deleteRole(r)"
              class="p-1.5 rounded-lg border border-red-200 dark:border-red-900/60 text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 text-xs transition cursor-pointer"
              title="Excluir papel"
            >
              <Trash2 class="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- ABA 3: MATRIZ DE PERMISSÕES DO ERP                            -->
    <!-- ============================================================== -->
    <div v-show="currentTab === 'permissions'" class="space-y-6">
      <div
        v-for="(modulePerms, moduleName) in permissionsGrouped"
        :key="moduleName"
        class="p-5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-3"
      >
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-[#FC6714]"></span>
            <h3 class="font-bold text-sm text-slate-900 dark:text-slate-100 uppercase tracking-wide">
              Módulo: {{ formatModuleName(moduleName) }}
            </h3>
          </div>
          <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
            {{ modulePerms.length }} ações mapeadas
          </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
          <div
            v-for="p in modulePerms"
            :key="p.id"
            class="p-3 rounded-lg bg-slate-50 dark:bg-slate-950/60 border border-slate-200/60 dark:border-slate-800/80"
          >
            <div class="flex items-start justify-between gap-2">
              <h4 class="font-semibold text-xs text-slate-900 dark:text-slate-100">
                {{ p.name }}
              </h4>
              <KeyRound class="w-3 h-3 text-[#FC6714] shrink-0 mt-0.5" />
            </div>
            <code class="text-[10px] text-indigo-600 dark:text-indigo-400 block mt-0.5 font-mono">
              {{ p.slug }}
            </code>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
              {{ p.description }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 1: CRIAÇÃO / EDIÇÃO DE USUÁRIO                           -->
    <!-- ============================================================== -->
    <BaseModal
      v-if="isUserModalOpen"
      :model-value="isUserModalOpen"
      :title="userModalMode === 'create' ? 'Cadastrar Novo Usuário' : 'Editar Usuário do Sistema'"
      max-width="2xl"
      @close="closeUserModal"
    >
      <form @submit.prevent="saveUser" class="space-y-5">
        <!-- Banner Super Admin (Livre) -->
        <div
          @click="userForm.is_super_admin = !userForm.is_super_admin"
          :class="userForm.is_super_admin
            ? 'border-amber-500 bg-amber-50/70 dark:bg-amber-950/40 shadow-xs ring-1 ring-amber-500/30'
            : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 hover:border-slate-300 dark:hover:border-slate-700'"
          class="p-4 rounded-xl border flex items-center justify-between gap-4 cursor-pointer transition select-none"
        >
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0" :class="userForm.is_super_admin ? 'bg-amber-500 text-white' : 'bg-amber-50 dark:bg-amber-950/60 text-amber-600'">
              <Crown class="w-5 h-5" />
            </div>
            <div>
              <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-900 dark:text-slate-100">
                  Super Admin (Acesso Livre)
                </span>
                <span class="px-2 py-0.2 rounded-full text-[10px] font-bold bg-amber-200 dark:bg-amber-900 text-amber-900 dark:text-amber-100">
                  Irrestrito
                </span>
              </div>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                Super Admins possuem acesso completo a todos os módulos, menus e endpoints, sem necessidade de permissões manuais.
              </p>
            </div>
          </div>
          <button
            type="button"
            role="switch"
            :aria-checked="userForm.is_super_admin"
            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
            :class="userForm.is_super_admin ? 'bg-amber-500' : 'bg-slate-200 dark:bg-slate-700'"
          >
            <span
              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-xs ring-0 transition duration-200 ease-in-out"
              :class="userForm.is_super_admin ? 'translate-x-5' : 'translate-x-0'"
            />
          </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Nome -->
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Nome Completo <span class="text-red-500">*</span>
            </label>
            <input
              v-model="userForm.name"
              type="text"
              required
              placeholder="Ex: Carlos Eduardo"
              class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
            />
          </div>

          <!-- E-mail / Usuário -->
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              E-mail de Acesso <span class="text-red-500">*</span>
            </label>
            <input
              v-model="userForm.email"
              type="email"
              required
              placeholder="usuario@redepronta.com"
              class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
            />
          </div>

          <!-- Senha -->
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Senha {{ userModalMode === 'create' ? '*' : '(Opcional se não alterar)' }}
            </label>
            <div class="relative">
              <input
                v-model="userForm.password"
                :type="showUserPassword ? 'text' : 'password'"
                :required="userModalMode === 'create'"
                placeholder="••••••••"
                class="w-full pl-3 pr-8 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
              />
              <button
                type="button"
                @click="showUserPassword = !showUserPassword"
                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer"
                tabindex="-1"
              >
                <EyeOff v-if="showUserPassword" class="w-3.5 h-3.5" />
                <Eye v-else class="w-3.5 h-3.5" />
              </button>
            </div>
          </div>

          <!-- Papel (Role) -->
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Papel de Usuário (Perfil)
            </label>
            <select
              v-model="userForm.role_id"
              class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
            >
              <option :value="null">Nenhum (Apenas permissões diretas)</option>
              <option v-for="r in rolesList" :key="r.id" :value="r.id">
                {{ r.name }}
              </option>
            </select>
          </div>

          <!-- Vínculo com Pessoa do Cadastro -->
          <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Vínculo com Pessoa Cadastrada (Opcional)
            </label>
            <PersonSearchSelect
              v-model="userForm.person_id"
              placeholder="Digite o nome ou CPF para vincular a uma pessoa do cadastro..."
            />
            <p class="text-[11px] text-slate-400 mt-1">
              Ao vincular, a pessoa terá o papel de Usuário ativado automaticamente no cadastro.
            </p>
          </div>
        </div>

        <!-- Feedback de Erro do Modal -->
        <div v-if="userModalError" class="p-3 rounded-lg bg-red-50 dark:bg-red-950/40 border border-red-200 text-red-700 dark:text-red-300 text-xs flex items-center gap-2">
          <AlertCircle class="w-4 h-4 shrink-0" />
          <span>{{ userModalError }}</span>
        </div>

        <!-- Botões do Modal -->
        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
          <button
            type="button"
            @click="closeUserModal"
            class="px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold transition cursor-pointer"
          >
            Cancelar
          </button>
          <button
            type="submit"
            :disabled="isSavingUser"
            class="px-4 py-2 rounded-lg bg-[#FC6714] hover:bg-[#e05609] text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5 cursor-pointer disabled:opacity-75"
          >
            <Loader2 v-if="isSavingUser" class="w-3.5 h-3.5 animate-spin" />
            <span>{{ isSavingUser ? 'Salvando...' : 'Salvar Usuário' }}</span>
          </button>
        </div>
      </form>
    </BaseModal>

    <!-- ============================================================== -->
    <!-- MODAL 2: CRIAÇÃO / EDIÇÃO DE PAPEL (ROLE)                      -->
    <!-- ============================================================== -->
    <BaseModal
      v-if="isRoleModalOpen"
      :model-value="isRoleModalOpen"
      :title="roleModalMode === 'create' ? 'Cadastrar Novo Papel de Usuário' : 'Editar Papel de Usuário'"
      max-width="3xl"
      @close="closeRoleModal"
    >
      <form @submit.prevent="saveRole" class="space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Nome do Papel <span class="text-red-500">*</span>
            </label>
            <input
              v-model="roleForm.name"
              type="text"
              required
              placeholder="Ex: Supervisor Operacional"
              class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Identificador (Slug) <span class="text-red-500">*</span>
            </label>
            <input
              v-model="roleForm.slug"
              type="text"
              required
              placeholder="Ex: supervisor_operacional"
              class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
            />
          </div>

          <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
              Descrição do Papel
            </label>
            <textarea
              v-model="roleForm.description"
              rows="2"
              placeholder="Descreva as responsabilidades e escopo de atuação deste papel..."
              class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
            ></textarea>
          </div>
        </div>

        <!-- Seletor de Permissões por Módulo -->
        <div class="space-y-4 pt-2">
          <div class="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-800">
            <div>
              <h4 class="font-bold text-xs text-slate-900 dark:text-slate-100 uppercase tracking-wide">
                Permissões Associadas ao Papel
              </h4>
              <p class="text-[11px] text-slate-400">
                Selecione as ações que os usuários com este papel poderão executar.
              </p>
            </div>
            <span class="text-xs font-bold text-[#FC6714]">
              {{ roleForm.permissions.length }} selecionadas
            </span>
          </div>

          <div class="space-y-3 max-h-[360px] overflow-y-auto pr-1">
            <div
              v-for="(modulePerms, moduleName) in permissionsGrouped"
              :key="moduleName"
              class="p-3.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 space-y-2.5"
            >
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                  {{ formatModuleName(moduleName) }}
                </span>
                <button
                  type="button"
                  @click="toggleAllModulePermissions(modulePerms)"
                  class="text-[11px] text-[#FC6714] hover:underline font-semibold cursor-pointer"
                >
                  {{ isAllModuleSelected(modulePerms) ? 'Desmarcar todos' : 'Marcar todos' }}
                </button>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <label
                  v-for="p in modulePerms"
                  :key="p.slug"
                  class="flex items-start gap-2 p-2 rounded-md hover:bg-white dark:hover:bg-slate-900 border border-transparent hover:border-slate-200 dark:hover:border-slate-800 cursor-pointer select-none text-xs transition"
                >
                  <input
                    type="checkbox"
                    :value="p.slug"
                    v-model="roleForm.permissions"
                    class="w-4 h-4 rounded-sm border-slate-300 text-[#FC6714] focus:ring-[#FC6714] mt-0.5"
                  />
                  <div>
                    <span class="font-semibold text-slate-800 dark:text-slate-200 block">{{ p.name }}</span>
                    <span class="text-[10px] text-slate-400 font-mono">{{ p.slug }}</span>
                  </div>
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Feedback de Erro do Modal -->
        <div v-if="roleModalError" class="p-3 rounded-lg bg-red-50 dark:bg-red-950/40 border border-red-200 text-red-700 dark:text-red-300 text-xs flex items-center gap-2">
          <AlertCircle class="w-4 h-4 shrink-0" />
          <span>{{ roleModalError }}</span>
        </div>

        <!-- Botões do Modal -->
        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
          <button
            type="button"
            @click="closeRoleModal"
            class="px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold transition cursor-pointer"
          >
            Cancelar
          </button>
          <button
            type="submit"
            :disabled="isSavingRole"
            class="px-4 py-2 rounded-lg bg-[#FC6714] hover:bg-[#e05609] text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5 cursor-pointer disabled:opacity-75"
          >
            <Loader2 v-if="isSavingRole" class="w-3.5 h-3.5 animate-spin" />
            <span>{{ isSavingRole ? 'Salvando...' : 'Salvar Papel' }}</span>
          </button>
        </div>
      </form>
    </BaseModal>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import {
  ShieldCheck,
  UserPlus,
  Users,
  Crown,
  Layers,
  KeyRound,
  Search,
  RotateCcw,
  Pencil,
  Power,
  Trash2,
  Plus,
  User,
  UserX,
  AlertCircle,
  Loader2,
  Eye,
  EyeOff,
} from 'lucide-vue-next';
import BaseBreadcrumb from '../components/common/BaseBreadcrumb.vue';
import BaseModal from '../components/common/BaseModal.vue';
import PersonSearchSelect from '../components/common/PersonSearchSelect.vue';
import TablePagination from '../components/common/TablePagination.vue';

const currentTab = ref<'users' | 'roles' | 'permissions'>('users');

const stats = reactive({
  users_total: 0,
  users_active: 0,
  users_inactive: 0,
  users_super_admin: 0,
  roles_total: 0,
  permissions_total: 0,
});

const filters = reactive({
  search: '',
  role_id: null as number | null,
  status: '',
  is_super_admin: '',
});

const usersList = ref<any[]>([]);
const rolesList = ref<any[]>([]);
const permissionsGrouped = ref<Record<string, any[]>>({});
const isLoadingUsers = ref(false);

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  per_page: 15,
});

let searchTimeout: any = null;
const debounceSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    pagination.current_page = 1;
    loadUsers();
  }, 350);
};

const formatModuleName = (mod: string) => {
  const map: Record<string, string> = {
    people: 'Pessoas & Clientes',
    supplies: 'Suprimentos & WMS',
    access: 'Usuários & Permissões',
    basics: 'Cadastros Básicos',
    integrations: 'Integrações & APIs',
    developer: 'Desenvolvedor & Governança',
  };
  return map[mod] || mod;
};

// ==============================================================
// CARREGAMENTO DE DADOS
// ==============================================================
const loadUsers = async () => {
  isLoadingUsers.value = true;
  try {
    const res = await axios.get('/api/v1/users', {
      params: {
        page: pagination.current_page,
        search: filters.search || undefined,
        role_id: filters.role_id || undefined,
        status: filters.status || undefined,
        is_super_admin: filters.is_super_admin !== '' ? filters.is_super_admin : undefined,
      },
    });

    usersList.value = res.data?.data || [];
    const meta = res.data?.meta || {};
    pagination.current_page = meta.current_page || 1;
    pagination.last_page = meta.last_page || 1;
    pagination.total = meta.total || 0;
    pagination.per_page = meta.per_page || 15;

    if (meta.stats) {
      Object.assign(stats, meta.stats);
    }
  } catch (e) {
    console.error('Erro ao carregar usuários:', e);
  } finally {
    isLoadingUsers.value = false;
  }
};

const loadRoles = async () => {
  try {
    const res = await axios.get('/api/v1/roles');
    rolesList.value = res.data?.data || [];
  } catch (e) {
    console.error('Erro ao carregar papéis:', e);
  }
};

const loadPermissions = async () => {
  try {
    const res = await axios.get('/api/v1/permissions');
    permissionsGrouped.value = res.data?.data?.modules || {};
  } catch (e) {
    console.error('Erro ao carregar permissões:', e);
  }
};

const changeUserPage = (page: number) => {
  pagination.current_page = page;
  loadUsers();
};

const toggleUserStatus = async (user: any) => {
  try {
    const res = await axios.patch(`/api/v1/users/${user.id}/toggle-status`);
    user.status = res.data?.data?.status || (user.status === 'active' ? 'inactive' : 'active');
    loadUsers();
  } catch (e: any) {
    alert(e.response?.data?.message || 'Erro ao alterar status do usuário.');
  }
};

const deleteUser = async (user: any) => {
  const confirmed = window.confirm(`Deseja realmente remover o usuário "${user.name}"?`);
  if (!confirmed) return;

  try {
    await axios.delete(`/api/v1/users/${user.id}`);
    loadUsers();
  } catch (e: any) {
    alert(e.response?.data?.message || 'Não foi possível excluir o usuário.');
  }
};

const deleteRole = async (role: any) => {
  const confirmed = window.confirm(`Deseja realmente remover o papel "${role.name}"?`);
  if (!confirmed) return;

  try {
    await axios.delete(`/api/v1/roles/${role.id}`);
    loadRoles();
  } catch (e: any) {
    alert(e.response?.data?.message || 'Não foi possível excluir o papel.');
  }
};

// ==============================================================
// MODAL DE USUÁRIO (Criação / Edição)
// ==============================================================
const isUserModalOpen = ref(false);
const userModalMode = ref<'create' | 'edit'>('create');
const isSavingUser = ref(false);
const userModalError = ref('');
const showUserPassword = ref(false);
const editingUserId = ref<number | null>(null);

const userForm = reactive({
  name: '',
  email: '',
  password: '',
  role_id: null as number | null,
  person_id: null as number | null,
  is_super_admin: false,
  status: 'active',
});

const openNewUserModal = () => {
  userModalMode.value = 'create';
  editingUserId.value = null;
  userModalError.value = '';
  showUserPassword.value = false;
  Object.assign(userForm, {
    name: '',
    email: '',
    password: '',
    role_id: null,
    person_id: null,
    is_super_admin: false,
    status: 'active',
  });
  isUserModalOpen.value = true;
};

const openEditUserModal = (u: any) => {
  userModalMode.value = 'edit';
  editingUserId.value = u.id;
  userModalError.value = '';
  showUserPassword.value = false;
  Object.assign(userForm, {
    name: u.name,
    email: u.email,
    password: '',
    role_id: u.role_id,
    person_id: u.person_id,
    is_super_admin: Boolean(u.is_super_admin),
    status: u.status,
  });
  isUserModalOpen.value = true;
};

const closeUserModal = () => {
  isUserModalOpen.value = false;
};

const saveUser = async () => {
  isSavingUser.value = true;
  userModalError.value = '';

  try {
    const payload: any = {
      name: userForm.name,
      email: userForm.email,
      role_id: userForm.role_id,
      person_id: userForm.person_id,
      is_super_admin: userForm.is_super_admin,
      status: userForm.status,
    };
    if (userForm.password) {
      payload.password = userForm.password;
    }

    if (userModalMode.value === 'create') {
      await axios.post('/api/v1/users', payload);
    } else {
      await axios.put(`/api/v1/users/${editingUserId.value}`, payload);
    }

    closeUserModal();
    loadUsers();
  } catch (e: any) {
    userModalError.value =
      e.response?.data?.errors?.email?.[0] ||
      e.response?.data?.errors?.password?.[0] ||
      e.response?.data?.message ||
      'Erro ao salvar usuário.';
  } finally {
    isSavingUser.value = false;
  }
};

// ==============================================================
// MODAL DE PAPEL (Role)
// ==============================================================
const isRoleModalOpen = ref(false);
const roleModalMode = ref<'create' | 'edit'>('create');
const isSavingRole = ref(false);
const roleModalError = ref('');
const editingRoleId = ref<number | null>(null);

const roleForm = reactive({
  name: '',
  slug: '',
  description: '',
  permissions: [] as string[],
});

const openNewRoleModal = () => {
  roleModalMode.value = 'create';
  editingRoleId.value = null;
  roleModalError.value = '';
  Object.assign(roleForm, {
    name: '',
    slug: '',
    description: '',
    permissions: [],
  });
  isRoleModalOpen.value = true;
};

const openEditRoleModal = (r: any) => {
  roleModalMode.value = 'edit';
  editingRoleId.value = r.id;
  roleModalError.value = '';
  Object.assign(roleForm, {
    name: r.name,
    slug: r.slug,
    description: r.description || '',
    permissions: [...(r.permissions || [])],
  });
  isRoleModalOpen.value = true;
};

const closeRoleModal = () => {
  isRoleModalOpen.value = false;
};

const isAllModuleSelected = (modulePerms: any[]) => {
  return modulePerms.every((p) => roleForm.permissions.includes(p.slug));
};

const toggleAllModulePermissions = (modulePerms: any[]) => {
  const allSelected = isAllModuleSelected(modulePerms);
  if (allSelected) {
    const toRemove = modulePerms.map((p) => p.slug);
    roleForm.permissions = roleForm.permissions.filter((s) => !toRemove.includes(s));
  } else {
    modulePerms.forEach((p) => {
      if (!roleForm.permissions.includes(p.slug)) {
        roleForm.permissions.push(p.slug);
      }
    });
  }
};

const saveRole = async () => {
  isSavingRole.value = true;
  roleModalError.value = '';

  try {
    const payload = {
      name: roleForm.name,
      slug: roleForm.slug,
      description: roleForm.description,
      permissions: roleForm.permissions,
    };

    if (roleModalMode.value === 'create') {
      await axios.post('/api/v1/roles', payload);
    } else {
      await axios.put(`/api/v1/roles/${editingRoleId.value}`, payload);
    }

    closeRoleModal();
    loadRoles();
  } catch (e: any) {
    roleModalError.value =
      e.response?.data?.errors?.name?.[0] ||
      e.response?.data?.errors?.slug?.[0] ||
      e.response?.data?.message ||
      'Erro ao salvar papel.';
  } finally {
    isSavingRole.value = false;
  }
};

onMounted(() => {
  loadUsers();
  loadRoles();
  loadPermissions();
});
</script>
