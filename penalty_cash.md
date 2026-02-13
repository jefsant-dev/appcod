
# Penalty Cash

---

# 🎯 Conceito Geral
Penalty Cash é um **jogo web mobile-first de cobranças de pênaltis**, com partidas 1x1 e apostas em dinheiro real entre jogadores.

A plataforma funciona como uma **casa de apostas**, cobrando rake (taxa) por partida, mantendo lucratividade e operação semelhante às BETs brasileiras.

---

# 💸 Economia Real — Dinheiro, PIX e Saques
A versão BET envolve **dinheiro real**, depositado e sacado pelos jogadores.

## Depósitos
- Jogador deposita via **PIX automático**.
- Saldo entra imediatamente na carteira interna da plataforma.

## Saques
- Jogadores podem solicitar saque via PIX.
- Saques passam por:
  - checagem antifraude
  - limite diário
  - taxa opcional de saque
  - aprovação manual ou automática

No MVP:
- Depósitos e saques **serão simulados manualmente**, manipulando o banco diretamente.
- O fluxo final (com integração ao PSP) será implementado depois de provar o conceito.

---

# ⚙ Sistema de Apostas
Cada partida exige um **valor de entrada** (aposta).
Ambos os jogadores pagam.

Exemplo:
- Apostam: R$ 5 cada
- Pót total: R$ 10
- Rake da plataforma: 10% (R$ 1) - a porcentagem pode ser aterada no painel administrativo
- Vencedor recebe: R$ 9

Em caso de empate:
- Reembolso menos rake proporcional ou regra escolhida pela plataforma

A plataforma **nunca perde dinheiro** — o risco é zero.

---

# 🤖 Bots com Controle Financeiro
Bots são usados para:
- popular salas
- permitir partidas rápidas mesmo com poucos jogadores reais
- simular concorrência

Bots **não usam dinheiro real**:
- eles "pagam" a entrada, mas o valor não sai da plataforma
- se ganharem, o prêmio não vira dinheiro real para ninguém
- plataforma nunca perde com bots

Dificuldades:
- easy
- normal
- pro

Bots tem comportamento humano e variam erros e acertos.

---

# 🎮 Mecânica de Jogo

Dinâmica principal

Partidas 1x1 divididas em rounds. Cada round tem dois papéis: - Batedor
— escolhe direção e força - Goleiro — escolhe direção para tentar
defender

A partida alterna entre jogador A chutando e jogador B defendendo, e
depois o contrário.

Controles

Pensados para mobile, mas funcionam também no desktop: 1. Controle de
Direção (Aim): - Jogador toca/clica no gol para escolher onde
chutar/defender. 2. Controle de Força (Power Bar): - Uma barra de força
com um marcador se movendo. - Jogador toca/clica para iniciar e solta
para parar.

Resolução do lance

O backend determina o resultado do lance comparando: - direção do
chute - força aplicada - direção do goleiro - modificadores de
habilidades

Possíveis resultados: gol, defesa ou trave/erro.

## Fluxo do jogo
1. Jogador cria sala e define valor da aposta.
2. Segundo jogador entra (ou bot preenche).
3. Jogo começa:
   - Jogador A chuta / Jogador B defende
   - Troca de papéis
4. Após X rounds:
   - vencedor é definido
   - sistema paga automaticamente o prêmio ao ganhador

---

# 🎮 Controles do Jogador

## 1. Mira (Aim)
Jogador toca/clica no gol para definir:
- horizontal (x)
- vertical (y)

## 2. Força (Power)
Barra de força com ponto oscilante.
Jogador toca/clica para parar no momento desejado.

## 3. Ações do goleiro
Idêntico ao batedor, mas apenas a mira é relevante.

---

# 📈 Progressão e Monetização Extra
Além do rake, a plataforma também lucra com:
- venda de skins
- venda de habilidades especiais
- boosts de XP
- acesso premium

Não determinam vitória, mas melhoram retenção.

---

# 📊 Segurança e Antifraude
Com dinheiro real, o sistema exige:
- auditoria de logs
- tracking de transações
- limites de saque
- bloqueio de contas suspeitas
- monitoramento antifraude
- validações contra multi-account

---

# 🏛 Regras Legais (Resumo)
Versão BET envolve:
- regulamentação de apostas no país-alvo
- integração com PSP autorizado
- possível necessidade de CNPJ
- Política de KYC (opcional no começo, recomendada depois)

No MVP, ainda não implementaremos:
- KYC completo
- termo legal robusto
- auditoria bancária

---

# 🧩 Infraestrutura (alto nível)
- Backend: Laravel + MySQL
- Frontend: Mobile-first, React ou equivalente
- Realtime: polling no MVP; WebSocket depois
- PIX: integração via PSP (MercadoPago, Juno, Asaas, Stripe, etc.)
- Saques: endpoints internos e painel administrativo
- Bot engine: usa perfis predefinidos e nunca perde dinheiro da plataforma

---

# 🧱 Fluxo Completo do Jogador
1. Cria conta
2. Deposita via PIX
3. Escolhe valor da aposta
4. Entra numa partida
5. Joga o duelo
6. Ganha ou perde dinheiro
7. Saca via PIX

---

# 🧮 Fórmula Financeira
```
premio = (entrada_jogador1 + entrada_jogador2) - rake
```

Se bot participa:
- rake = 100% do valor pago pelo bot
- bot nunca saca

---

# 🎯 Objetivo do MVP
Criar uma versão totalmente funcional com:
- criação de salas
- aposta com saldo real interno
- duelo completo de pênaltis
- sistema de bots
- cálculo financeiro e rake
- painel administrativo simples
- controle básico de depósitos/saldo/saques (simulados)

Pronto para apresentar a investidores como **prova de conceito**.

---

_Fim._
