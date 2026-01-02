// resources/js/admin/books-attachments-preview.js

document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("attachments");
    const grid = document.getElementById("attachments-preview");

    if (!input || !grid) return;

    let currentFiles = [];
    let objectUrls = [];

    const humanSize = (bytes) => {
        const units = ["B", "KB", "MB", "GB"];
        let i = 0;
        let n = bytes;
        while (n >= 1024 && i < units.length - 1) {
            n /= 1024;
            i++;
        }
        return `${n.toFixed(i === 0 ? 0 : 1)} ${units[i]}`;
    };

    const clearObjectUrls = () => {
        for (const url of objectUrls) URL.revokeObjectURL(url);
        objectUrls = [];
    };

    const rebuildInputFiles = () => {
        const dt = new DataTransfer();
        for (const f of currentFiles) dt.items.add(f);
        input.files = dt.files;
    };

    const render = () => {
        clearObjectUrls();
        grid.innerHTML = "";

        if (!currentFiles.length) return;

        currentFiles.forEach((file, index) => {
            const isImage = file.type?.startsWith("image/");
            const isPdf =
                file.type === "application/pdf" ||
                file.name.toLowerCase().endsWith(".pdf");

            const card = document.createElement("div");
            card.className =
                "rounded-md bg-[#343933] border border-white/10 overflow-hidden";

            const preview = document.createElement("div");
            preview.className =
                "w-full h-28 bg-black/20 flex items-center justify-center overflow-hidden";

            if (isImage) {
                const url = URL.createObjectURL(file);
                objectUrls.push(url);

                const img = document.createElement("img");
                img.src = url;
                img.alt = file.name;
                img.className = "w-full h-full object-cover block";
                preview.appendChild(img);
            } else if (isPdf) {
                const tile = document.createElement("div");
                tile.className =
                    "w-full h-full flex flex-col items-center justify-center text-white/80";
                tile.innerHTML = `
                    <div class="text-xs font-semibold px-2 py-1 rounded bg-white/10">PDF</div>
                    <div class="mt-2 text-[10px] text-white/50 truncate px-2 w-full text-center">
                        ${file.name}
                    </div>
                `;
                preview.appendChild(tile);
            } else {
                const other = document.createElement("div");
                other.className = "text-xs text-white/60";
                other.textContent = "File";
                preview.appendChild(other);
            }

            const meta = document.createElement("div");
            meta.className = "p-2";

            const name = document.createElement("div");
            name.className = "text-white text-xs font-semibold truncate";
            name.title = file.name;
            name.textContent = file.name;

            const sub = document.createElement("div");
            sub.className =
                "mt-1 text-[10px] text-white/60 flex items-center justify-between gap-2";
            sub.innerHTML = `
                <span class="truncate">${
                    isImage ? "Image" : isPdf ? "PDF" : file.type || "File"
                }</span>
                <span class="shrink-0">${humanSize(file.size)}</span>
            `;

            const actions = document.createElement("div");
            actions.className = "mt-2 flex items-center gap-2";

            const openBtn = document.createElement("button");
            openBtn.type = "button";
            openBtn.className =
                "inline-flex items-center justify-center h-7 px-2 text-[10px] rounded bg-white/10 text-white hover:bg-white/20 transition";
            openBtn.textContent = "Open";
            openBtn.addEventListener("click", () => {
                const url = URL.createObjectURL(file);
                window.open(url, "_blank", "noopener");
                setTimeout(() => URL.revokeObjectURL(url), 30000);
            });

            const removeBtn = document.createElement("button");
            removeBtn.type = "button";
            removeBtn.className =
                "ml-auto inline-flex items-center justify-center h-7 px-2 text-[10px] rounded bg-red-600 text-white hover:bg-red-700 transition";
            removeBtn.textContent = "Remove";
            removeBtn.addEventListener("click", () => {
                currentFiles.splice(index, 1);
                rebuildInputFiles();
                render();
            });

            actions.appendChild(openBtn);
            actions.appendChild(removeBtn);

            meta.appendChild(name);
            meta.appendChild(sub);
            meta.appendChild(actions);

            card.appendChild(preview);
            card.appendChild(meta);

            grid.appendChild(card);
        });
    };

    input.addEventListener("change", (e) => {
        currentFiles = Array.from(e.target.files || []);
        render();
    });
});
