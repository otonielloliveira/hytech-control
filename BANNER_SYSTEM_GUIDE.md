# 🎨 Sistema de Banners Moderno - Guia Completo

## 📋 Visão Geral

Sistema de criação de banners com camadas (layers) similar ao WordPress, permitindo criar banners profissionais e personalizados sem código.

## ✨ Recursos Principais

### 🎯 Sistema de Layers
- **📄 Textos**: Títulos, subtítulos, parágrafos com controle total de estilo
- **🔘 Botões**: CTAs customizáveis com cores, tamanhos e links
- **🖼️ Imagens**: Adicione logos, ícones ou imagens decorativas
- **🏷️ Badges**: Tags e etiquetas de destaque (NOVO, URGENTE, etc)
- **↕️ Espaçadores**: Controle o espaçamento vertical entre elementos

### 🎨 Customização de Background
- Upload de imagem de fundo (recomendado: 1920x800px)
- Cor sólida de fundo
- Controle de posicionamento (9 opções)
- Tamanho do fundo (Cover, Contain, Auto, 100%)
- Overlay com cor e opacidade ajustável

### 📐 Layout e Dimensões
- Altura do banner configurável (400-600px recomendado)
- Alinhamento de conteúdo (Topo, Centro, Base)
- Sistema responsivo automático

## 🚀 Como Usar

### 1. Acessar o Editor
Navegue até **Admin Panel → Blog → Banners → Criar Banner**

### 2. Usar Templates Prontos
Clique em um dos botões de template no topo da página:
- **🎯 Template: Hero Principal** - Banner de destaque para página inicial
- **📰 Template: Notícia Destaque** - Banner para anúncios importantes
- **🎁 Template: Promoção** - Banner para ofertas e promoções

### 3. Personalizar Background (Tab 1)

#### Imagem de Fundo
1. Faça upload de uma imagem (1920x800px recomendado)
2. Configure a posição (ex: Centro)
3. Escolha o tamanho (Cover para preencher)

#### Overlay
- Adicione uma cor de sobreposição
- Ajuste a opacidade (0-100%)
- Útil para melhorar legibilidade do texto

#### Dimensões
- Defina a altura do banner em pixels
- Escolha o alinhamento vertical do conteúdo

### 4. Adicionar Camadas de Conteúdo (Tab 2)

#### Adicionar Texto
```
1. Clique em "➕ Adicionar Elemento"
2. Escolha "📄 Texto"
3. Configure:
   - Conteúdo (com editor rico)
   - Tag HTML (H1, H2, H3, P, etc)
   - Cor do texto
   - Tamanho da fonte (px)
   - Peso da fonte (300-800)
   - Alinhamento (Esquerda, Centro, Direita)
   - Margens (superior e inferior)
```

#### Adicionar Botão
```
1. Clique em "➕ Adicionar Elemento"
2. Escolha "🔘 Botão"
3. Configure:
   - Texto do botão
   - URL de destino
   - Cores (fundo e texto)
   - Tamanho (Pequeno, Médio, Grande)
   - Alinhamento
   - Borda arredondada
   - Abrir em nova aba
   - Largura total
```

#### Adicionar Imagem
```
1. Clique em "➕ Adicionar Elemento"
2. Escolha "🖼️ Imagem"
3. Faça upload da imagem
4. Configure largura e altura (opcional)
5. Escolha o alinhamento
```

#### Adicionar Badge
```
1. Clique em "➕ Adicionar Elemento"
2. Escolha "🏷️ Badge/Tag"
3. Configure:
   - Texto (ex: "NOVO", "URGENTE")
   - Cores (fundo e texto)
   - Alinhamento
   - Margem inferior
```

#### Adicionar Espaçador
```
1. Clique em "➕ Adicionar Elemento"
2. Escolha "↕️ Espaçador"
3. Defina a altura em pixels
```

### 5. Reordenar Camadas
- Arraste e solte as camadas para reordenar
- A ordem define como os elementos aparecem (de cima para baixo)

### 6. Configurações Finais (Tab 3)
- **Título do Banner**: Nome para identificação interna
- **Descrição Interna**: Notas sobre o banner
- **Banner Ativo**: Ative/desative a exibição
- **Ordem de Exibição**: Defina a prioridade (menor = primeiro)

### 7. Salvar
Clique em "Criar Banner" para salvar

## 💡 Dicas e Melhores Práticas

### Imagens
- ✅ Use imagens de alta qualidade (1920x800px ou maior)
- ✅ Otimize as imagens antes do upload (use TinyPNG)
- ✅ Formatos recomendados: JPG para fotos, PNG para gráficos
- ⚠️ Evite imagens muito pesadas (>500KB)

### Textos
- ✅ Use títulos grandes e impactantes (36-52px)
- ✅ Subtítulos médios (18-24px)
- ✅ Contraste adequado (texto branco em fundo escuro)
- ✅ Fonte peso 700-800 para títulos
- ⚠️ Evite textos muito longos

### Cores
- ✅ Use a paleta do site (#c41e3a para vermelho político)
- ✅ Mantenha consistência visual
- ✅ Teste o contraste (texto legível)
- ⚠️ Não use muitas cores diferentes

### Layout
- ✅ Altura ideal: 400-600px (desktop)
- ✅ Alinhamento central funciona melhor
- ✅ Use espaçadores entre elementos (20-30px)
- ✅ Deixe respiro (margens adequadas)
- ⚠️ Não sobrecarregue com muitos elementos

### Performance
- ✅ Use overlay com opacidade 30-50% para escurecer fundo
- ✅ Máximo 3-4 banners no carousel
- ✅ Desative banners antigos
- ⚠️ Não use GIFs animados pesados

## 🎯 Exemplos de Uso

### Banner Hero de Destaque
```
Background:
- Imagem: Foto de alta qualidade
- Overlay: Preto 40%
- Altura: 600px
- Alinhamento: Centro

Layers:
1. Badge: "DESTAQUE" (vermelho)
2. Título H1: "Título Principal" (branco, 48px, bold)
3. Texto P: "Descrição breve" (branco, 18px)
4. Espaçador: 20px
5. Botão: "Saiba Mais" (vermelho, grande)
```

### Banner de Notícia Urgente
```
Background:
- Cor: #c41e3a (vermelho)
- Altura: 400px
- Alinhamento: Centro

Layers:
1. Badge: "URGENTE" (branco com texto vermelho)
2. Título H2: "Título da Notícia" (branco, 36px)
3. Texto P: "Resumo da notícia" (branco, 16px)
4. Botão: "Leia Mais" (branco com texto vermelho)
```

### Banner Promocional
```
Background:
- Cor gradiente ou sólida
- Altura: 350px
- Alinhamento: Centro

Layers:
1. Texto H3: "SUPER OFERTA" (20px)
2. Título H1: "Até 50% OFF" (52px, extra-bold)
3. Espaçador: 15px
4. Botão: "Aproveitar Agora" (grande, arredondado)
```

## 🔧 Funcionalidades Avançadas

### Duplicar Banner
1. Abra um banner existente
2. Clique em "📋 Duplicar" no topo
3. Uma cópia será criada (inativa por padrão)
4. Edite e ative quando pronto

### Visualizar Banner
1. Durante a edição, clique em "👁️ Visualizar Banner"
2. Abrirá a página principal em nova aba
3. Veja como o banner aparece no site

### Reordenar no Carousel
- Use o campo "Ordem de Exibição" nas Configurações
- Menor número = maior prioridade
- Exemplo: 0, 10, 20, 30...

## 📱 Responsividade

O sistema é totalmente responsivo:

**Desktop (>768px)**
- Altura configurada normalmente
- Todos os elementos visíveis

**Mobile (<768px)**
- Altura mínima: 400px
- Títulos reduzidos automaticamente
- Botões ajustados
- Layout otimizado

## 🐛 Troubleshooting

### Banner não aparece
- ✅ Verifique se está ativo (toggle verde)
- ✅ Confirme que tem imagem ou cor de fundo
- ✅ Limpe cache do navegador

### Texto ilegível
- ✅ Aumente o overlay opacity (40-60%)
- ✅ Use cores contrastantes
- ✅ Adicione sombra no texto

### Imagem cortada
- ✅ Altere background-position
- ✅ Mude para background-size: contain
- ✅ Use imagem com proporção correta

### Botão não aparece
- ✅ Verifique a cor do botão (contraste com fundo)
- ✅ Confirme que URL está preenchida
- ✅ Teste o alinhamento

## 📚 Recursos Adicionais

### Ferramentas Úteis
- **TinyPNG**: Otimização de imagens (https://tinypng.com)
- **Coolors**: Paletas de cores (https://coolors.co)
- **Google Fonts**: Fontes gratuitas (https://fonts.google.com)
- **Unsplash**: Imagens gratuitas (https://unsplash.com)

### Inspiração
- Mercado Livre
- Amazon
- Submarino
- Magazine Luiza
- Grandes portais de notícias

## 🆘 Suporte

Para dúvidas ou problemas:
1. Consulte este guia primeiro
2. Teste com templates prontos
3. Entre em contato com o suporte técnico

---

**Desenvolvido com ❤️ por HYTECH TECNOLOGIA LTDA**
