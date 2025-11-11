// admin/admin.js
window.admin = (() => {

    // ===================== INICIALIZAÇÃO =====================
    const init = () => {
        const btnEvents = document.getElementById("btnManageEvents");
        const btnUsers = document.getElementById("btnManageUsers");
        const btnAds = document.getElementById("btnManageAds");

        if (btnEvents) btnEvents.onclick = () => loadEventsManagement();
        if (btnUsers) btnUsers.onclick = () => loadUsersManagement();
        if (btnAds) btnAds.onclick = () => loadAdsManagement();
    };



    // ===================== GESTÃO DE PROPAGANDAS =====================
    const loadAdsManagement = async () => {
        const container = document.getElementById("adminContent");
        container.innerHTML = `<div class="text-center text-muted mt-4">Carregando Gestão de Propagandas...</div>`;
        try {
            const res = await fetch("admin/ads.php");
            container.innerHTML = await res.text();
            initAdsModule();
        } catch {
            container.innerHTML = `<div class="text-center text-danger mt-4">Erro ao carregar propagandas.</div>`;
        }
    };

    function initAdsModule() {
        const modal = new bootstrap.Modal(document.getElementById("adModal"));
        const form = document.getElementById("adForm");
        const btnAdd = document.getElementById("btnAddAd");
        const btnReload = document.getElementById("btnReloadAds");
        const adsContainer = document.getElementById("adsContainerAdmin");
        const imageInput = document.getElementById("adImage");
        const preview = document.getElementById("adPreview");

        let cropper = null;
        let editingId = null;
        function openAdModal(id = null) {
            const modal = new bootstrap.Modal(document.getElementById("adModal"));
            const form = document.getElementById("adForm");
            const preview = document.getElementById("adPreview");
            const cropContainer = document.getElementById("cropContainer");
            const cropImg = document.getElementById("adCropper");

            form.reset();
            preview.src = "";
            preview.style.display = "none";
            cropContainer.style.display = "none";
            document.getElementById("adId").value = "";

            if (cropper) {
                cropper.destroy();
                cropper = null;
            }

            if (id) {
                fetch(`admin/ads_get.php?id=${id}`)
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success) return alert("Erro ao carregar propaganda.");
                        const ad = data.ad;

                        document.getElementById("adId").value = ad.id;
                        document.getElementById("adTitle").value = ad.title;
                        document.getElementById("adLink").value = ad.link || "";
                        document.getElementById("adDisplayTime").value = ad.display_time || 5;
                        document.getElementById("adActive").checked = !!ad.active;

                        preview.src = ad.image;
                        preview.style.display = "block";
                    })
                    .catch(() => alert("Erro ao carregar propaganda."));
            }

            modal.show();
        }



        async function loadAds() {
            adsContainer.innerHTML = `<div class="text-center text-muted mt-3">Carregando propagandas...</div>`;
            try {
                const res = await fetch("admin/ads_list.php");
                const html = await res.text();
                adsContainer.innerHTML = html;
            } catch (err) {
                console.error(err);
                adsContainer.innerHTML = `<div class="text-center text-danger mt-3">Erro ao carregar propagandas.</div>`;
            }
            adsContainer.querySelectorAll(".btnEditAd").forEach(btn => {
                btn.addEventListener("click", e => {
                    const id = e.target.closest("tr").dataset.id;
                    openAdModal(id);
                });
            });

            adsContainer.querySelectorAll(".btnDeleteAd").forEach(btn => {
                btn.addEventListener("click", e => {
                    const id = e.target.closest("tr").dataset.id;
                    deleteAd(id);
                });
            });

        }


        // Adicionar
        btnAdd.addEventListener("click", () => {
            form.reset();
            editingId = null;
            preview.style.display = "none";
            document.getElementById("adModalLabel").textContent = "Adicionar Propaganda";
            modal.show();
        });

        // Recarregar
        btnReload.addEventListener("click", loadAds);

        // Cropper
        imageInput.addEventListener("change", (e) => {
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
        document.getElementById("saveAdBtn").addEventListener("click", async () => {
            const formData = new FormData(form);

            if (cropper) {
                const canvas = cropper.getCroppedCanvas({ width: 1200, height: 600 });
                const blob = await new Promise(res => canvas.toBlob(res, "image/jpeg"));
                formData.append("cropped_image", blob, "cropped.jpg");
            }

            const id = document.getElementById("adId").value;
            const endpoint = id ? "admin/ads_edit.php" : "admin/ads_save.php";

            try {
                const res = await fetch(endpoint, { method: "POST", body: formData });
                const data = await res.json();
                alert(data.message);

                if (data.success) {
                    const modalInstance = bootstrap.Modal.getInstance(document.getElementById("adModal"));
                    modalInstance.hide();
                    cropper?.destroy();
                    cropper = null;
                    form.reset();
                    document.getElementById("adPreview").style.display = "none";
                    loadAds();
                }
            } catch {
                alert("Erro ao salvar propaganda.");
            }
        });



        // Editar / Excluir
        adsContainer.addEventListener("click", async (e) => {
            const btn = e.target.closest("button");
            if (!btn) return;
            const id = btn.dataset.id;

            if (btn.classList.contains("btn-edit")) {
                try {
                    const res = await fetch("admin/ads_list.php");
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
                } catch {
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
                } catch {
                    alert("Erro ao excluir propaganda.");
                }
            }
        });

        loadAds();
    }

    // ===================== GESTÃO DE EVENTOS =====================
    // ===================== GESTÃO DE EVENTOS =====================
    const loadEventsManagement = async () => {
        const container = document.getElementById("adminContent");
        container.innerHTML = `<div class="text-center text-muted mt-4">Carregando Gestão de Eventos...</div>`;
        try {
            const res = await fetch("admin/admin_events.php");
            container.innerHTML = await res.text();
            initEventsModule();
        } catch {
            container.innerHTML = `<div class="text-center text-danger mt-4">Erro ao carregar eventos.</div>`;
        }
    };

   
function initEventsModule() {
    const modal = new bootstrap.Modal(document.getElementById("eventModalAdmin"));
    const form = document.getElementById("eventFormAdmin");
    const btnAdd = document.getElementById("btnAddEvent");
    const container = document.getElementById("adminEventsContainer");

    const imageInput = document.getElementById("eventImageFile"); // input file novo
    const preview = document.getElementById("eventImagePreview"); // img para preview
    let cropper = null;

    let map, marker;

    // ===================== MAPA =====================
    function initMap() {
        const latInput = document.getElementById("eventLatitude");
        const lngInput = document.getElementById("eventLongitude");
        const mapContainer = document.getElementById("eventMap");

        const lat = parseFloat(latInput.value) || -23.5505;
        const lng = parseFloat(lngInput.value) || -46.6333;

        map = new google.maps.Map(mapContainer, {
            center: { lat, lng },
            zoom: 12,
        });

        marker = new google.maps.Marker({
            position: { lat, lng },
            map: map,
            draggable: true,
        });

        marker.addListener("dragend", () => {
            latInput.value = marker.getPosition().lat();
            lngInput.value = marker.getPosition().lng();
        });
    }

    // ===================== CARREGA EVENTOS =====================
    async function loadEvents() {
        container.innerHTML = `<div class="text-center text-muted mt-3">Carregando eventos...</div>`;
        try {
            const res = await fetch("admin/events_get.php");
            const data = await res.json();
            if (!data.success) throw new Error("Erro ao carregar eventos.");

            if (!data.events.length) {
                container.innerHTML = `<div class="text-center text-muted mt-3">Nenhum evento cadastrado.</div>`;
                return;
            }

            let html = `<table class="table table-striped">
      <thead>
        <tr>
          <th>Nome</th>
          <th>Data</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>`;

            data.events.forEach(ev => {
                const date = new Date(ev.date_start);
                html += `<tr data-id="${ev.id}">
        <td>${ev.name}</td>
        <td>${date.toLocaleDateString()}</td>
        <td>
          <button class="btn btn-sm btn-primary btn-edit" data-id="${ev.id}">Editar</button>
          <button class="btn btn-sm btn-danger btn-del" data-id="${ev.id}">Excluir</button>
          <button class="btn btn-sm btn-secondary btn-export" data-id="${ev.id}">Exportar</button>
        </td>
      </tr>`;
            });

            html += `</tbody></table>`;
            container.innerHTML = html;

            container.querySelectorAll(".btn-edit").forEach(btn => btn.onclick = () => openEventModal(btn.dataset.id));
            container.querySelectorAll(".btn-del").forEach(btn => btn.onclick = () => deleteEvent(btn.dataset.id));
            container.querySelectorAll(".btn-export").forEach(btn => btn.onclick = () => openExportModal(btn.dataset.id));

        } catch (err) {
            console.error(err);
            container.innerHTML = `<div class="text-center text-danger mt-3">Erro ao carregar eventos.</div>`;
        }
    }

    // ===================== ABRIR MODAL =====================
    function openEventModal(id = null) {
        form.reset();
        preview.src = "";
        preview.style.display = "none";
        if (cropper) { cropper.destroy(); cropper = null; }

        document.getElementById("eventId").value = id || "";
        modal.show();

        if (id) {
            fetch(`admin/events_get.php?id=${id}`)
                .then(r => r.json())
                .then(d => {
                    if (!d.success) return alert("Erro ao carregar evento.");
                    const e = d.event;

                    document.getElementById("eventName").value = e.name;
                    document.getElementById("eventSummary").value = e.summary;
                    document.getElementById("eventDateStart").value = e.date_start.replace(" ", "T");
                    document.getElementById("eventDateEnd").value = e.date_end.replace(" ", "T");
                    document.getElementById("eventAddress").value = e.address;
                    document.getElementById("eventCity").value = e.city;
                    document.getElementById("eventCapacity").value = e.capacity;
                    document.getElementById("eventUnlimited").checked = e.unlimited == 't' || e.unlimited == true || e.unlimited == 1;
                    document.getElementById("eventCost").value = e.cost;
                    document.getElementById("eventLatitude").value = e.latitude;
                    document.getElementById("eventLongitude").value = e.longitude;
                    document.getElementById("eventImage").value = e.image;

                    // preview da imagem existente
                    if (e.image) {
                        preview.src = e.image;
                        preview.style.display = "block";
                    }

                    initMap();
                });
        } else {
            initMap();
        }
    }

    // ===================== UPLOAD / CROPPER =====================
    imageInput.addEventListener("change", e => {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = ev => {
            preview.src = ev.target.result;
            preview.style.display = "block";

            if (cropper) cropper.destroy();
            cropper = new Cropper(preview, { aspectRatio: 4/3, viewMode: 1, autoCropArea: 1 });
        };
        reader.readAsDataURL(file);
    });

    // ===================== SALVAR =====================
    form.onsubmit = async e => {
        e.preventDefault();
        const formData = new FormData(form);
        const id = document.getElementById("eventId").value;
        if (id) formData.append('update', '1');
        const url = id ? "admin/events_edit.php" : "admin/events_add.php";

        // adiciona imagem cropped
        if (cropper) {
            const canvas = cropper.getCroppedCanvas({ width: 1200, height: 800 });
            const blob = await new Promise(res => canvas.toBlob(res, "image/jpeg"));
            formData.append("cropped_image", blob, "cropped.jpg");
        }

        try {
            const res = await fetch(url, { method: "POST", body: formData });
            const data = await res.json();
            alert(data.message);
            if (data.success) {
                modal.hide();
                loadEvents();
            }
        } catch (err) {
            console.error(err);
            alert("Erro ao salvar evento.");
        }
    };

    // ===================== DELETE / EXPORT =====================
    async function deleteEvent(id) {
        if (!confirm("Excluir evento?")) return;
        const res = await fetch("admin/events_delete.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id=${id}`
        });
        const data = await res.json();
        alert(data.message);
        if (data.success) loadEvents();
    }

    function openExportModal(id) {
        document.getElementById("exportEventId").value = id;
        new bootstrap.Modal(document.getElementById("exportModal")).show();
    }

    btnAdd.onclick = () => openEventModal();
    loadEvents();
}


    // ===================== GESTÃO DE USUÁRIOS =====================
    const loadUsersManagement = async () => {
        const container = document.getElementById("adminContent");
        container.innerHTML = `<div class="text-center text-muted mt-4">Carregando Gestão de Usuários...</div>`;
        try {
            const res = await fetch("admin/users.php");
            container.innerHTML = await res.text();
            if (typeof loadUsersList === "function") loadUsersList();
        } catch {
            container.innerHTML = `<div class="text-center text-danger mt-4">Erro ao carregar usuários.</div>`;
        }
    };

    return { init };

})();
