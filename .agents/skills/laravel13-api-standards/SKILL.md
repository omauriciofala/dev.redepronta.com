---
name: laravel13-api-standards
description: Diretrizes mandatórias e padrões de código para APIs em Laravel 13.x no ecossistema SaaS (Debian 13, MariaDB, PHP 8.4). Use ao criar migrations, models, services, controllers, form requests e resources.
---

# LARAVEL 13.x API STANDARDS - DIRETRIZES DE ARQUITETURA BACKEND

Este skill define o padrão oficial de implementação backend para o novo SaaS, garantindo código limpo, de alta performance e desacoplado.

---

## 🏛️ 1. ESTRUTURA DE CAMADAS (API-FIRST)

O fluxo de cada requisição segue rigorosamente a separação em 5 camadas:
1. **Route (`routes/api.php` ou `routes/api/v1/...`)**: Apenas aponta para o controller de domínio.
2. **FormRequest (`app/Http/Requests/{Domain}/...`)**: Valida payload, cabeçalhos e permissões de entrada antes de chegar ao controller.
3. **Controller (`app/Http/Controllers/Api/V1/{Domain}/...`)**: Controlador magro. Recebe o DTO/Request validado, chama a camada de serviço e retorna o Resource.
4. **Service (`app/Services/{Domain}/{Domain}Service.php`)**: Contém a regra de negócio e encapsula mutações em `DB::transaction()`.
5. **JsonResource (`app/Http/Resources/{Domain}/...`)**: Serializa e formata a resposta JSON de forma consistente.

---

## 🔒 2. MULTI-TENANCY E ESCOPO GLOBAL

Todo modelo que pertence a uma conta de assinante deve implementar o isolamento de tenant automático.

### Trait `BelongsToTenant`:
```php
namespace App\Traits;

use App\Models\Tenant\Account;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::creating(function ($model) {
            if (empty($model->account_id) && auth()->check()) {
                $model->account_id = auth()->user()->account_id;
            }
        });

        static::addGlobalScope('account', function (Builder $builder) {
            if (auth()->check() && !auth()->user()->isSuperAdmin()) {
                $builder->where($builder->getModel()->getTable() . '.account_id', auth()->user()->account_id);
            }
        });
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
```

---

## 🛡️ 3. INTEGRIDADE RELACIONAL NO MARIADB

- **NUNCA** utilize `onDelete('cascade')` para tabelas de domínio (Pessoas, Cidades, Depósitos, Materiais, Clientes, Contratos).
- Utilize **SEMPRE** `onDelete('restrict')` ou omita o cascade para preservar o padrão seguro do MariaDB.
- Exemplo de Migration correta:
```php
Schema::create('people_employees', function (Blueprint $table) {
    $table->id();
    $table->foreignId('person_id')->constrained('people')->onDelete('restrict');
    $table->foreignId('account_id')->constrained('accounts')->onDelete('restrict');
    $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('restrict');
    $table->string('registration_code', 50)->nullable();
    $table->string('position', 100);
    $table->decimal('salary', 12, 2)->default(0);
    $table->string('work_regime', 20)->default('clt'); // clt, pj, estagio
    $table->timestamps();
    $table->softDeletes();
});
```

---

## ⚡ 4. TRANSAÇÕES ATÔMICAS NA CAMADA DE SERVIÇO

Qualquer rotina que insere ou altera mais de uma linha de tabela DEVE executar dentro de `DB::transaction()`:

```php
namespace App\Services\Task;

use Illuminate\Support\Facades\DB;
use App\Models\Task\Task;
use App\Models\Task\TaskHistory;

class TaskService
{
    public function createTask(array $data, int $userId): Task
    {
        return DB::transaction(function () use ($data, $userId) {
            $task = Task::create($data);

            TaskHistory::create([
                'task_id' => $task->id,
                'account_id' => $task->account_id,
                'user_id' => $userId,
                'from_status' => null,
                'to_status' => $task->status,
                'action_notes' => 'Tarefa criada via API/Omnichannel',
            ]);

            return $task;
        });
    }
}
```

---

## 📋 5. FORMATO PADRONIZADO DE RESPOSTA API (JSON)

Todas as respostas da API seguem um envelope uniforme:

```json
{
  "success": true,
  "data": { ... },
  "message": "Operação realizada com sucesso",
  "meta": {
    "current_page": 1,
    "total": 50,
    "per_page": 15
  }
}
```

Em caso de erro (422 / 400 / 500):
```json
{
  "success": false,
  "error": "Dados inválidos",
  "message": "A cidade selecionada não permite desativação pois possui chamados vinculados.",
  "errors": {
    "city_id": ["Cidade possui 14 ordens de serviço pendentes."]
  }
}
```
