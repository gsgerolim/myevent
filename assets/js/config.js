// ===============================
// Funções auxiliares
// ===============================

function applyThemePreview(previewEl, theme) {
  if (!previewEl) return;

  // atualiza preview local
  if (theme.background) previewEl.style.backgroundColor = theme.background;
  if (theme.text)       previewEl.style.color = theme.text;
  previewEl.querySelectorAll('.btn-primary').forEach(b => { if (theme.primary) b.style.backgroundColor = theme.primary; });
  previewEl.querySelectorAll('.btn-secondary').forEach(b => { if (theme.secondary) b.style.backgroundColor = theme.secondary; });

  // atualiza variáveis CSS globais
  if (theme.background) document.documentElement.style.setProperty('--color-bg', theme.background);
  if (theme.text)       document.documentElement.style.setProperty('--color-text', theme.text);
  if (theme.primary)    document.documentElement.style.setProperty('--color-primary', theme.primary);
  if (theme.secondary)  document.documentElement.style.setProperty('--color-secondary', theme.secondary);
}

function previewFile(e, previewEl) {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = ev => previewEl.src = ev.target.result;
  reader.readAsDataURL(file);
}

function createPicker(containerId, key, target, preview, lightColorsRef, darkColorsRef) {
  console.log('Creating picker for', key, 'in', containerId, 'with target:', target);
  console.log('Current target colors:', target[key]);
  const defaultColor = target[key] || '#000000';
  const container = document.getElementById(containerId);
  if (!container) return;

  const wrapper = document.createElement('div');
  wrapper.className = 'd-flex align-items-center gap-2 mb-2';
  wrapper.innerHTML = `
    <label class="form-label mb-0 text-capitalize" style="min-width:80px">${key}</label>
    <input type="color" class="form-control form-control-color" id="${containerId}_${key}_input" value="${defaultColor}" style="width:56px;padding:0;border-radius:6px;">
    <button type="button" class="btn btn-outline-secondary btn-sm" id="${containerId}_${key}_eyedrop">🎯</button>
    <div class="color-preview" id="${containerId}_${key}_preview" style="width:36px;height:36px;border-radius:6px;border:1px solid #ddd;background:${defaultColor}"></div>
  `;
  container.appendChild(wrapper);

  const colorInput = wrapper.querySelector('input[type="color"]');
  const previewBox = wrapper.querySelector(`#${containerId}_${key}_preview`);
  const eyedropBtn = wrapper.querySelector(`#${containerId}_${key}_eyedrop`);

  target[key] = defaultColor;
  applyThemePreview(preview, target);

  const pickr = Pickr.create({
    el: previewBox,
    theme: 'classic',
    default: defaultColor,
    components: { preview: true, opacity: true, hue: true, interaction: { input: true, save: true } }
  });

  pickr.on('save', color => {
    const hex = color.toHEXA().toString();
    target[key] = hex;
    colorInput.value = hex;
    previewBox.style.background = hex;
    applyThemePreview(preview, target);
    pickr.hide();
  });

  colorInput.addEventListener('input', () => {
    const v = colorInput.value;
    target[key] = v;
    previewBox.style.background = v;
    applyThemePreview(preview, target);
    try { pickr.setColor(v); } catch(e){}
  });

  eyedropBtn.addEventListener('click', async () => {
    if (!window.EyeDropper) {
      alert("Conta-gotas não suportado no seu navegador.");
      return;
    }
    try {
      const eye = new EyeDropper();
      const { sRGBHex } = await eye.open();
      colorInput.value = sRGBHex;
      target[key] = sRGBHex;
      previewBox.style.background = sRGBHex;
      applyThemePreview(preview, target);
      try { pickr.setColor(sRGBHex); } catch(e){}
    } catch(err) { console.log('Conta-gotas cancelado ou falhou', err); }
  });
}

// ===============================
// Inicialização da página de configuração
// ===============================
function initConfigPage(themeLight = {}, themeDark = {}) {
  const saveBtn = document.getElementById("saveConfig");
  const toggleBtn = document.getElementById("togglePreviewTheme");
  const logoInput = document.getElementById("logoUpload");
  const faviconInput = document.getElementById("faviconUpload");
  const logoPreview = document.getElementById("logoPreview");
  const faviconPreview = document.getElementById("faviconPreview");
  const preview = document.getElementById("themePreview");

  const colorKeys = ["primary", "secondary", "background", "text"];
  const lightColors = Object.assign({}, themeLight); // já com os valores do BD
  const darkColors  = Object.assign({}, themeDark);  // já com os valores do BD
  let usingDark = false;

  colorKeys.forEach(key => {
    createPicker("themeLightColors", key, lightColors, preview, lightColors, darkColors);
    createPicker("themeDarkColors", key, darkColors, preview, lightColors, darkColors);
  });

  
  applyThemePreview(preview, lightColors);

  logoInput.addEventListener("change", e => previewFile(e, logoPreview));
  faviconInput.addEventListener("change", e => previewFile(e, faviconPreview));

  toggleBtn.addEventListener("click", () => {
    usingDark = !usingDark;
    toggleBtn.textContent = usingDark ? "Simular Tema Claro" : "Simular Tema Escuro";
    applyThemePreview(preview, usingDark ? darkColors : lightColors);
  });

  saveBtn.addEventListener("click", async () => {
    const msgContainer = document.getElementById("saveMsg") || document.createElement('div');
    msgContainer.id = "saveMsg";
    msgContainer.className = "mt-2";
    saveBtn.parentNode.appendChild(msgContainer);
    msgContainer.innerHTML = "Salvando...";

    const formData = new FormData();
    formData.append("site_title", document.getElementById("siteTitle").value.trim());
    formData.append("page_title", document.getElementById("pageTitle").value.trim());
    formData.append("theme_light", JSON.stringify(lightColors));
    formData.append("theme_dark", JSON.stringify(darkColors));

    if (logoInput.files[0]) formData.append("logo", logoInput.files[0]);
    if (faviconInput.files[0]) formData.append("favicon", faviconInput.files[0]);

    try {
      const res = await fetch("admin/save_config.php", { method: "POST", body: formData });
      const data = await res.json();
      msgContainer.innerHTML = data.success
        ? `<span class="text-success">${data.message || "Configurações salvas!"}</span>`
        : `<span class="text-danger">Erro: ${data.message || "Falha ao salvar"}</span>`;
    } catch (err) {
      msgContainer.innerHTML = `<span class="text-danger">Erro de comunicação: ${err.message}</span>`;
    }
  });
}
