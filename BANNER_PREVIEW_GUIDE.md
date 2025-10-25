# 🎨 Sistema de Banners - Preview & Drag-and-Drop

## ✨ Novas Funcionalidades Implementadas

### 🔴 Preview em Tempo Real
- ✅ Visualização instantânea do banner enquanto edita
- ✅ Atualização automática ao modificar campos
- ✅ Modo tela cheia para visualização ampliada
- ✅ Botão de atualizar manual quando necessário
- ✅ Preview responsivo (desktop e mobile)

### 🎯 Drag & Drop Visual
- ✅ Arraste camadas para reordenar facilmente
- ✅ Cores diferentes para cada tipo de elemento
- ✅ Animações suaves de movimento
- ✅ Feedback visual durante o arrasto
- ✅ Ícones coloridos para identificação rápida

### 🎨 Interface Melhorada
- ✅ Cores específicas por tipo de bloco:
  - 📄 Texto: Azul (#3b82f6)
  - 🔘 Botão: Verde (#10b981)
  - 🖼️ Imagem: Roxo (#8b5cf6)
  - 🏷️ Badge: Laranja (#f59e0b)
  - ↕️ Espaçador: Cinza (#6b7280)
- ✅ Animações de entrada para novos blocos
- ✅ Hover effects em todos os elementos
- ✅ Cursor grab/grabbing visual

## 🚀 Como Usar o Preview

### 1. Visualização Automática
- O preview aparece no topo do formulário
- Atualiza automaticamente quando você modifica:
  - Imagem de fundo
  - Cores e overlay
  - Altura do banner
  - Camadas de conteúdo
  - Qualquer configuração

### 2. Controles do Preview

#### 👁️ Botão "Tela Cheia"
```
1. Clique no botão "Tela Cheia"
2. Preview ocupará toda a tela
3. Visualize o banner em tamanho real
4. Clique "Sair da Tela Cheia" para voltar
```

#### 🔄 Botão "Atualizar"
```
1. Se o preview não atualizar automaticamente
2. Clique em "Atualizar"
3. O preview será recarregado manualmente
```

### 3. Estados do Preview

**🟢 Carregado com Sucesso**
- Preview mostra o banner completo
- Todas as camadas visíveis
- Cores e estilos aplicados

**🟡 Aguardando Conteúdo**
- Mensagem: "Adicione camadas na aba Content Layers"
- Aparece quando não há camadas configuradas

**🔴 Carregando**
- Ícone de loading girando
- Aparece durante atualização

## 🎯 Como Usar Drag & Drop

### Reordenar Camadas

#### Método 1: Arrastar e Soltar
```
1. Clique e segure no cabeçalho de uma camada
2. Arraste para cima ou para baixo
3. Solte na posição desejada
4. A camada será reposicionada
```

#### Método 2: Visual
```
- Cada bloco tem uma cor na borda esquerda
- Ao arrastar, o bloco fica semi-transparente
- Uma linha indica onde será solto
- Animação suave ao soltar
```

### Identificação Visual

**📄 Bloco de Texto**
- Borda azul à esquerda
- Ícone: 📄
- Fundo azul claro

**🔘 Bloco de Botão**
- Borda verde à esquerda
- Ícone: 🔘
- Fundo verde claro

**🖼️ Bloco de Imagem**
- Borda roxa à esquerda
- Ícone: 🖼️
- Fundo roxo claro

**🏷️ Bloco de Badge**
- Borda laranja à esquerda
- Ícone: 🏷️
- Fundo laranja claro

**↕️ Bloco Espaçador**
- Borda cinza à esquerda
- Ícone: ↕️
- Fundo cinza claro

## 💡 Dicas de Uso

### Preview
- ✅ Deixe o preview visível enquanto edita
- ✅ Use tela cheia para verificar proporções
- ✅ Teste diferentes alturas (400-600px)
- ✅ Verifique contraste de cores no preview
- ⚠️ Preview pode levar 1-2 segundos para atualizar

### Drag & Drop
- ✅ Arraste pela barra de título do bloco
- ✅ Use as cores para identificar rapidamente
- ✅ Reordene antes de preencher detalhes
- ✅ Teste diferentes ordens visualmente
- ⚠️ Não arraste pelos campos de formulário

### Performance
- ✅ Preview é leve e rápido
- ✅ Atualização é "debounced" (aguarda parar de digitar)
- ✅ Não sobrecarrega o navegador
- ⚠️ Com muitas camadas (>10) pode demorar um pouco

## 🎨 Fluxo de Trabalho Recomendado

### 1. Configure o Background
```
Tab "Background & Layout":
1. Upload da imagem de fundo
2. Defina altura (ex: 500px)
3. Configure overlay se necessário
4. Escolha alinhamento do conteúdo
→ Veja mudanças no preview acima
```

### 2. Adicione Camadas
```
Tab "Content Layers":
1. Clique "➕ Adicionar Elemento"
2. Escolha tipo (Texto, Botão, etc)
3. Preencha configurações básicas
4. Veja aparecer no preview
5. Repita para mais camadas
```

### 3. Reordene Visualmente
```
1. Observe o preview
2. Identifique ordem ideal
3. Arraste camadas para reordenar
4. Preview atualiza automaticamente
5. Ajuste até ficar perfeito
```

### 4. Ajuste Fino
```
1. Use preview em tela cheia
2. Ajuste cores e tamanhos
3. Teste espaçamentos
4. Verifique responsividade mental
5. Salve quando satisfeito
```

## 🐛 Solução de Problemas

### Preview não atualiza
**Solução:**
1. Clique no botão "🔄 Atualizar"
2. Aguarde 2 segundos
3. Se persistir, recarregue a página

### Preview em branco
**Possíveis causas:**
- ✅ Nenhuma camada adicionada → Adicione conteúdo
- ✅ Fundo sem cor/imagem → Configure background
- ✅ Cache do navegador → Limpe cache (Ctrl+F5)

### Drag não funciona
**Solução:**
1. Certifique-se de arrastar pela barra de título
2. Não arraste pelos campos de formulário
3. Recarregue a página se necessário
4. Verifique se JavaScript está habilitado

### Preview dessincronizado
**Solução:**
1. Aguarde 2-3 segundos após editar
2. Clique em "Atualizar" manualmente
3. Se persistir, salve e reabra

### Cores não aparecem no preview
**Solução:**
1. Verifique se preencheu o campo de cor
2. Use o ColorPicker ao invés de digitar
3. Formato deve ser hexadecimal (#ff0000)
4. Atualize o preview manualmente

## 🎯 Atalhos e Truques

### Atalhos de Teclado
- **Tab**: Navegar entre campos
- **Enter**: Confirmar seleções
- **Esc**: Fechar modais/dropdowns
- **F11**: Tela cheia do navegador

### Truques Visuais
1. **Clone Rápido**: Duplicate blocos em vez de criar do zero
2. **Template Base**: Crie um banner modelo e duplique
3. **Preview Lado a Lado**: Use monitor duplo (preview + formulário)
4. **Teste Rápido**: Use templates prontos e modifique

### Workflow Profissional
```
1. Escolha template pronto
2. Customize background
3. Ajuste textos no preview
4. Reordene arrastando
5. Teste em tela cheia
6. Salve e visualize no site
```

## 📱 Responsividade do Preview

O preview se adapta ao tamanho:

**Desktop (>1200px)**
- Preview em largura total
- Controles lado a lado
- Tela cheia disponível

**Tablet (768-1200px)**
- Preview redimensionado
- Controles empilhados
- Scroll horizontal se necessário

**Mobile (<768px)**
- Preview compacto
- Controles verticais
- Tela cheia recomendada

## 🔧 Configurações Avançadas

### Personalizar Preview
O preview usa os mesmos estilos do site:
- Fontes: Inter, -apple-system
- Cores: Variáveis CSS do tema
- Responsividade: Breakpoints do Bootstrap

### Debug Mode
Para desenvolvedores:
```javascript
// No console do navegador
window.bannerDebug = true;
refreshPreview(); // Mostra logs detalhados
```

## 📚 Recursos Adicionais

### Inspiração de Layouts
- Hero com badge + título + botão
- Notícia urgente com fundo colorido
- Promoção com números grandes
- Editorial com imagem + overlay escuro

### Paleta de Cores Sugeridas
- **Política**: #c41e3a (vermelho)
- **Sucesso**: #10b981 (verde)
- **Info**: #3b82f6 (azul)
- **Alerta**: #f59e0b (laranja)
- **Neutro**: #6b7280 (cinza)

### Combinações de Fonte
- **Título Hero**: 48-52px, weight 800
- **Subtítulo**: 20-24px, weight 600
- **Descrição**: 16-18px, weight 400
- **Badge**: 12px, weight 700, uppercase

## 🆘 Suporte

### FAQ Rápido

**P: Preview atualiza sozinho?**
R: Sim, aguarda 1 segundo após parar de digitar.

**P: Posso arrastar imagens também?**
R: Não, apenas reordenar blocos. Upload é por botão.

**P: Preview mostra versão mobile?**
R: Não, apenas desktop. Teste no site real para mobile.

**P: Quantas camadas posso adicionar?**
R: Ilimitado, mas recomendamos 3-7 para performance.

**P: Posso copiar um banner?**
R: Sim, use o botão "Duplicar" ao editar.

---

**Desenvolvido com ❤️ e tecnologia moderna por HYTECH TECNOLOGIA LTDA**

🚀 Sistema com Preview ao Vivo + Drag & Drop Visual
