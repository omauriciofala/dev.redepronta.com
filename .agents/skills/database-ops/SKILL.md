---
name: database-ops
description: >-
  Use this skill when creating database migrations, inspecting schemas, querying the database (MySQL/MariaDB), or performing safe schema updates.
---

# Database Operations Skill - RedePronta ERP SaaS

Instruções para manipulação segura e padronizada de banco de dados no ERP Rede Pronta (MariaDB / PHP 8.4).

---

## 1. Conexões Disponíveis

- `mysql` (Padrão: `rp_erp_db`): Conexão principal com as tabelas de negócio do SaaS.
- **Regra Mandatória de FK:** Toda Foreign Key de negócio deve utilizar obrigatoriamente `onDelete('restrict')` para integridade referencial estrita.

---

## 2. Criação de Migrações

- Sempre utilize nomes semânticos e descritivos:
  ```bash
  php artisan make:migration add_field_to_table_name --table=table_name
  ```

- Estrutura obrigatória com `up()` e `down()` idempotentes:
  ```php
  use Illuminate\Database\Migrations\Migration;
  use Illuminate\Database\Schema\Blueprint;
  use Illuminate\Support\Facades\Schema;

  return new class extends Migration
  {
      public function up(): void
      {
          Schema::table('minha_tabela', function (Blueprint $table) {
              if (!Schema::hasColumn('minha_tabela', 'novo_campo')) {
                  $table->string('novo_campo')->nullable()->after('campo_anterior');
              }
          });
      }

      public function down(): void
      {
          Schema::table('minha_tabela', function (Blueprint $table) {
              if (Schema::hasColumn('minha_tabela', 'novo_campo')) {
                  $table->dropColumn('novo_campo');
              }
          });
      }
  };
  ```

---

## 3. Execução e Verificação de Status

```bash
# Verificar status das migrações
php artisan migrate:status

# Executar migrações pendentes em ambiente de desenvolvimento
php artisan migrate

# NUNCA EXECUTAR migrate:fresh SEM AUTORIZAÇÃO EXPRESSA
```
