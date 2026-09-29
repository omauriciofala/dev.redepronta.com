# Operations & Server Maintenance (Laravel 13 - RedePronta)

Guia de execução para comandos operacionais frequentes, depuração de rotas, testes e manutenção de permissões no ambiente Laravel 13 da RedePronta.

---

## 1. Limpeza e Otimização de Caches

Sempre que alterar rotas, configurações (`.env` ou `config/`), arquivos Blade ou Service Providers, execute a limpeza segura:

```bash
# Limpeza completa (rotas, views, cache, config)
php artisan optimize:clear
```

Para limpezas pontuais:
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

---

## 2. Inspeção e Depuração de Rotas

Para validar se uma rota nova ou refatorada foi registrada corretamente:

```bash
# Listar rotas filtrando por URI ou nome
php artisan route:list --path=erp
php artisan route:list --name=tickets
```

---

## 3. Execução de Testes Automatizados

Antes de finalizar qualquer alteração em regras de negócio ou Services:

```bash
# Executar todos os testes
php artisan test

# Executar suíte ou teste específico
php artisan test tests/Feature/ExampleTest.php
```

---

## 4. Manutenção de Permissões de Servidor

O servidor Nginx / PHP-FPM roda sob o usuário/grupo `www-data`. Para evitar erros de escrita em `storage/logs/laravel.log` ou `bootstrap/cache/`:

```bash
# Ajuste seguro de proprietário e permissões
sudo chown -R mol:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```
