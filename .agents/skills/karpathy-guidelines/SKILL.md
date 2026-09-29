---
name: karpathy-guidelines
description: >-
  Use this skill when writing, reviewing, or refactoring code to minimize LLM hallucinations, avoid over-engineering, and enforce surgical changes based on Andrej Karpathy's coding observations.
---

# Karpathy Guidelines

Diretrizes comportamentais para reduzir erros comuns de LLMs ao programar, derivadas das observações de Andrej Karpathy sobre armadilhas de código gerado por IA.

**Compromisso fundamental:** Priorizar cautela e precisão sobre velocidade. Evite alterações cosméticas ou especulativas.

---

## 1. Pense Antes de Codificar (Think Before Coding)

**Não assuma. Não esconda incertezas. Explicite decisões.**

Antes de implementar qualquer alteração:
- Declare suas premissas de forma explícita. Se algo for incerto, pergunte ao usuário.
- Se existirem múltiplas interpretações plausíveis, apresente-as ao invés de escolher em silêncio.
- Se existir uma abordagem mais simples e direta, aponte-a.
- Se um requisito não estiver claro, pare e aponte exatamente a dúvida.

---

## 2. Simplicidade em Primeiro Lugar (Simplicity First)

**Mínimo de código necessário para resolver o problema. Nada especulativo.**

- Não implemente recursos além do que foi solicitado.
- Não crie abstrações, classes genéricas ou interfaces para código de uso único.
- Não adicione "flexibilidade" ou "configurabilidade" não solicitada.
- Não adicione tratamento de erro defensivo para cenários impossíveis no domínio.
- Se escreveu 200 linhas e 50 são suficientes, reescreva de forma enxuta.

---

## 3. Mudanças Cirúrgicas (Surgical Changes)

**Altere apenas o estritamente necessário. Limpe apenas a sua própria alteração.**

Ao editar código existente:
- Não "melhore" código adjacente, comentários ou formatação não relacionados.
- Não refatore partes que já estejam funcionando a menos que isso seja o objetivo da tarefa.
- Mantenha estritamente o padrão de estilo e convenção do arquivo existente.
- Se notar código morto pré-existente não relacionado, mencione-o na resposta, mas não o remova.

Ao concluir sua alteração:
- Remova imports, variáveis ou métodos que ficaram órfãos por conta da **sua** alteração.
- Garanta que cada linha alterada tenha relação direta com o pedido do usuário.

---

## 4. Execução Orientada a Metas (Goal-Driven Execution)

**Defina critérios de sucesso. Valide até a verificação.**

Transforme tarefas em metas verificáveis:
- "Criar rota e tela" → "Registrar rota, carregar view, testar resposta HTTP 200"
- "Corrigir bug" → "Identificar reprodução, aplicar correção cirúrgica, validar que o bug não ocorre mais"
- "Refatorar X" → "Garantir que os testes ou verificações manuais passem antes e depois"

Para tarefas multi-etapas, estruture:
```
1. [Passo] → verificação: [teste / comando]
2. [Passo] → verificação: [teste / comando]
```
