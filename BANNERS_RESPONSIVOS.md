# 🖼️ Sistema de Banners Responsivos - Instrções

## ✅ O que foi implementado

### 1. **Banners Responsivos Desktop + Mobile**
   - ✓ Suporte completo a imagens de desktop (1920x400px)
   - ✓ Suporte completo a imagens de mobile (540x960px)
   - ✓ Detecção automática do dispositivo
   - ✓ Fallback para banners antigos se nenhum banner ativo

### 2. **Painel Administrativo Moderno**
   - ✓ Interface limpa e intuitiva
   - ✓ Preview em tempo real das imagens
   - ✓ Suporte a Drag & Drop para upload
   - ✓ Thumbnails responsivas
   - ✓ Gerenciar múltiplos banners
   - ✓ Ativar/Desativar banners
   - ✓ Editar banners existentes
   - ✓ Deletar banners

### 3. **Funcionalidades Avançadas**
   - ✓ Ordem customizável de banners
   - ✓ Título e descrição opcional
   - ✓ Link de destino customizável
   - ✓ Upload de imagens via drag & drop
   - ✓ Preview antes de salvar
   - ✓ Validações de tamanho e tipo de arquivo

---

## 🚀 Como Usar

### **Acessar Painel de Banners**

1. Acesse o painel administrativo
2. Clique em "🖼️ Banners" no menu superior
3. Você será redirecionado para a lista de banners

### **Criar Novo Banner**

1. Clique em "Novo Banner"
2. **Preencha as informações:**
   - Título (opcional)
   - Descrição (opcional)
   - Ordem de exibição (0 = primeiro)
   - Link de destino (opcional)

3. **Faça upload das imagens:**
   - **Desktop Banner:** 1920x400px (recomendado)
     - Formato: PNG, JPG, GIF, WEBP
     - Tamanho máximo: 5MB
   
   - **Mobile Banner:** 540x960px (recomendado)
     - Formato: PNG, JPG, GIF, WEBP
     - Tamanho máximo: 5MB

4. **Opções:**
   - Marque "Ativar este banner imediatamente" se quiser que apareça na página de agendamento
   - Deixe desmarcado para criar como rascunho

5. Clique em "Criar Banner"

### **Editar Banner Existente**

1. Na lista de banners, clique em "Editar"
2. Atualize as informações desejadas
3. Para mudar as imagens, faça upload das novas
4. Deixe em branco se quiser manter as imagens atuais
5. Clique em "Salvar Alterações"

### **Gerenciar Banners**

- **Ativar/Desativar:** Clique no botão de status para alternar
- **Deletar:** Clique em "Deletar" e confirme
- **Visualização:** Veja previews de desktop e mobile lado a lado

---

## 📱 Responsividade

### **Desktop (1920x400px)**
- Exibido em telas de desktop
- Formato paisagem
- Altura fixa de 250px na página

### **Mobile (540x960px)**
- Exibido em telas de celular e tablet
- Formato retrato
- Altura máxima de 60% da viewport

---

## 🎨 Recomendações de Design

### **Desktop Banner**
- Proporção: 16:4 (1920x400)
- Margem de segurança: 200px nas laterais
- Fonte mínima: 24px se tiver texto
- Deixar espaço central para conteúdo

### **Mobile Banner**
- Proporção: 9:16 (540x960)
- Margem de segurança: 40px nas laterais
- Fonte mínima: 16px se tiver texto
- Colocar conteúdo importante no centro superior

---

## 💾 Banco de Dados

### **Tabela: banners**
```
- id (PK)
- titulo (VARCHAR 255)
- banner_desktop (VARCHAR 255) - Caminho do arquivo
- banner_mobile (VARCHAR 255) - Caminho do arquivo
- descricao (TEXT)
- link_destino (VARCHAR 255)
- ativo (BOOLEAN)
- ordem (INTEGER)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

---

## 🔌 Integração com Página de Agendamento

A página de agendamento (`/agendar`) automaticamente:

1. ✅ Busca o banner ativo com menor ordem
2. ✅ Detecta o tipo de dispositivo
3. ✅ Exibe a imagem apropriada (desktop ou mobile)
4. ✅ Se houver link, torna o banner clicável
5. ✅ Fallback para banners antigos se nenhum ativo

---

## 🔒 Permissões

- ✅ Apenas **Proprietária** pode gerenciar banners
- ✅ Clientes podem **visualizar** os banners na página de agendamento
- ✅ Profissionais podem **visualizar** mas não editar

---

## 📸 Rotas Disponíveis

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/admin/banners` | Listar banners |
| GET | `/admin/banners/create` | Formulário criar banner |
| POST | `/admin/banners` | Salvar novo banner |
| GET | `/admin/banners/{banner}/edit` | Formulário editar banner |
| PUT | `/admin/banners/{banner}` | Salvar edição |
| DELETE | `/admin/banners/{banner}` | Deletar banner |
| POST | `/admin/banners/{banner}/toggle` | Ativar/Desativar |

---

## 🐛 Troubleshooting

### **Imagens não aparecem**
- Verifique se o storage está público: `php artisan storage:link`
- Verifique permissões da pasta `storage/app/public/banners/`

### **Erro ao fazer upload**
- Verifique tamanho da imagem (máx 5MB)
- Verifique formato (PNG, JPG, GIF, WEBP)
- Verifique espaço em disco

### **Banner não aparece na página de agendamento**
- Verifique se o banner está marcado como "Ativo"
- Verifique se tem pelo menos uma imagem (desktop ou mobile)
- Limpe o cache: `php artisan cache:clear`

---

## 🎓 Exemplo de Uso

### **Criando banner de promoção:**
1. Título: "Promoção de Verão - 50% OFF"
2. Desktop: Imagem 1920x400 com design chamativo
3. Mobile: Imagem 540x960 otimizada para celular
4. Link: `https://seu-site.com.br/promocoes`
5. Ordem: 0 (aparecerá primeiro)
6. Status: Ativar

### **Resultado:**
- ✅ Ao abrir `/agendar` em desktop → vê a imagem 1920x400
- ✅ Ao abrir `/agendar` em mobile → vê a imagem 540x960
- ✅ Ao clicar no banner → vai para a página de promoções

---

**Status:** ✅ Sistema Completo e Funcional
**Data:** 14/07/2026
**Versão:** 1.0.0
