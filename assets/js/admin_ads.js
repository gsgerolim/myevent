document.addEventListener("DOMContentLoaded", () => {
  const modal = new bootstrap.Modal(document.getElementById("adModal"));
  const form = document.getElementById("adForm");
  const btnAdd = document.getElementById("btnAddAd");
  const btnReload = document.getElementById("btnReloadAds");
  const adsContainer = document.getElementById("adsContainer");
  const imageInput = document.getElementById("adImage");
  const preview = document.getElementById("adPreview");

  let cropper = null;
  let editingId = null;

  async function loadAds() {
    try {
      const res = await fetch("php/ads_public.php");
      const data = await res.json();
      if (!data.success) throw new Error(data.message || "Erro ao carregar ads");

      const ads = data.data || [];
      adsContainer.innerHTML = "";

      if (ads.length === 0) {
        adsContainer.innerHTML = `<div class="text-center text-muted mt-3">Nenhuma propaganda cadastrada.</div>`;
        return;
      }

      const table = document.createElement("table");
      table.className = "table table-striped align-middle";
      table.innerHTML = `
        <thead>
          <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Link</th>
            <th>Tempo</th>
            <th>Ativa</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          ${ads
            .map(
              (a) => `
            <tr>
              <td>${a.id}</td>
              <td>${a.title}</td>
              <td>${a.link || "-"}</td>
              <td>${a.display_time}s</td>
              <td><span class="badge ${a.active ? "bg-success" : "bg-secondary"}">${a.active ? "Sim" : "Não"}</span></td>
              <td>
                <button class="btn btn-sm btn-primary btn-edit" data-id="${a.id}">Editar</button>
                <button class="btn btn-sm btn-danger btn-del" data-id="${a.id}">Excluir</button>
              </td>
            </tr>
          `
            )
            .join("")}
        </tbody>
      `;
      adsContainer.appendChild(table);
    } catch (err) {
      console.error("Erro ao carregar propagandas:", err);
      adsContainer.innerHTML = `<div class="text-center text-danger mt-3">Erro ao carregar propagandas.</div>`;
    }
  }

  // Novo anúncio
  btnAdd?.addEventListener("click", () => {
    form.reset();
    editingId = null;
    preview.style.display = "none";
    document.getElementById("adModalLabel").textContent = "Adicionar Propaganda";
    modal.show();
  });

  // Recarregar lista
  btnReload?.addEventListener("click", () => loadAds());

  // Cropper e preview
  imageInput?.addEventListener("change", (e) => {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (ev) => {
      const img = document.getElementById("adCropper");
      img.src = ev.target.result;
      document.getElementById("cropContainer").style.display = "block";
      if (cropper) cropper.destroy();
      cropper = new Cropper(img, {
        aspectRatio: 2 / 1,
        viewMode: 1,
        autoCropArea: 1,
      });
    };
    reader.readAsDataURL(file);
  });

  // Salvar (add/edit)
  document.getElementById("saveAdBtn")?.addEventListener("click", async () => {
    const formData = new FormData(form);

    if (cropper) {
      const canvas = cropper.getCroppedCanvas({ width: 1200, height: 600 });
      const blob = await new Promise((res) => canvas.toBlob(res, "image/jpeg"));
      formData.append("cropped_image", blob, "cropped.jpg");
    }

    const endpoint = editingId ? "admin/ads_edit.php" : "admin/ads_save.php";
    if (editingId) formData.append("id", editingId);

    try {
      const res = await fetch(endpoint, { method: "POST", body: formData });
      const data = await res.json();
      alert(data.message);
      if (data.success) {
        modal.hide();
        loadAds();
      }
    } catch (err) {
      console.error(err);
      alert("Erro ao salvar propaganda.");
    }
  });

  // Editar/Excluir
  adsContainer?.addEventListener("click", async (e) => {
    const btn = e.target.closest("button");
    if (!btn) return;

    const id = btn.dataset.id;

    if (btn.classList.contains("btn-edit")) {
      try {
        const res = await fetch("php/ads_public.php");
        const data = await res.json();
        const ad = (data.data || []).find((a) => a.id == id);
        if (!ad) return alert("Propaganda não encontrada.");

        editingId = ad.id;
        document.getElementById("adModalLabel").textContent = "Editar Propaganda";
        document.getElementById("adTitle").value = ad.title;
        document.getElementById("adLink").value = ad.link;
        document.getElementById("adDisplayTime").value = ad.display_time;
        document.getElementById("adActive").checked = ad.active;
        preview.src = ad.image;
        preview.style.display = "block";
        modal.show();
      } catch (err) {
        alert("Erro ao carregar dados da propaganda.");
      }
    }

    if (btn.classList.contains("btn-del")) {
      if (!confirm("Deseja excluir esta propaganda?")) return;
      try {
        const res = await fetch("admin/ads_delete.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `id=${id}`,
        });
        const data = await res.json();
        alert(data.message);
        if (data.success) loadAds();
      } catch (err) {
        alert("Erro ao excluir propaganda.");
      }
    }
  });

  loadAds();
});
