
export const createCropper = (imageUrl) => ({
    imageUrl,
    canvas: null,
    ctx: null,
    img: null,

    isDragging: false,
    dragMode: null, // 'new' | 'move' | 'resize'
    resizeHandle: null, // 'n','s','e','w','ne','nw','se','sw'
    offsetX: 0,
    offsetY: 0,

    startX: 0,
    startY: 0,
    currentX: 0,
    currentY: 0,

    // Track mouse (for paste target when no selection)
    lastMouseX: 0,
    lastMouseY: 0,

    // Selection
    selection: {
        x: null,
        y: null,
        w: null,
        h: null,
    },

    preview: null,

    handleSize: 8,
    hitPadding: 6,

    // --- Zoom/magnifier ---
    zoomCanvas: null,
    zoomCtx: null,
    zoomSize: 150,
    zoomScale: 3,

    showZoom: false,
    zoomPosX: 0,
    zoomPosY: 0,

    // listeners (cleanup)
    onKeyDown: null,
    onPaste: null,

    init() {
        this.canvas = this.$refs.canvas;
        this.ctx = this.canvas.getContext("2d");

        if (this.$refs.zoomCanvas) {
            this.zoomCanvas = this.$refs.zoomCanvas;
            this.zoomCtx = this.zoomCanvas.getContext("2d");
            this.zoomCanvas.width = this.zoomSize;
            this.zoomCanvas.height = this.zoomSize;
        }

        this.img = new Image();
        this.img.onload = () => {
            this.canvas.width = this.img.width;
            this.canvas.height = this.img.height;
            this.ctx.drawImage(this.img, 0, 0);
        };
        this.img.src = this.imageUrl;

        // --- Clipboard / keyboard ---
        this.onKeyDown = async (e) => {
            // Copy selection to clipboard
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === "c") {
                e.preventDefault();
                try {
                    await this.copySelectionToClipboard();
                } catch (err) {
                    console.error("Copy failed:", err);
                }
            }
        };

        this.onPaste = async (e) => {
            try {
                const items = e.clipboardData?.items || [];
                const imgItem = [...items].find((i) => i.type?.startsWith("image/"));
                if (!imgItem) return;

                e.preventDefault();
                const blob = imgItem.getAsFile();
                if (!blob) return;

                await this.pasteBlobOntoSource(blob);
            } catch (err) {
                console.error("Paste failed:", err);
            }
        };

        window.addEventListener("keydown", this.onKeyDown);
        window.addEventListener("paste", this.onPaste);
    },

    destroy() {
        if (this.onKeyDown) window.removeEventListener("keydown", this.onKeyDown);
        if (this.onPaste) window.removeEventListener("paste", this.onPaste);
    },

    getHandleAt(mx, my) {
        const s = this.selection;
        if (s.x === null || s.w <= 0 || s.h <= 0) return null;

        const left = s.x;
        const right = s.x + s.w;
        const top = s.y;
        const bottom = s.y + s.h;
        const pad = this.hitPadding;

        const nearLeft = Math.abs(mx - left) <= pad;
        const nearRight = Math.abs(mx - right) <= pad;
        const nearTop = Math.abs(my - top) <= pad;
        const nearBottom = Math.abs(my - bottom) <= pad;

        if (nearLeft && nearTop) return "nw";
        if (nearRight && nearTop) return "ne";
        if (nearLeft && nearBottom) return "sw";
        if (nearRight && nearBottom) return "se";

        if (nearTop && mx > left && mx < right) return "n";
        if (nearBottom && mx > left && mx < right) return "s";
        if (nearLeft && my > top && my < bottom) return "w";
        if (nearRight && my > top && my < bottom) return "e";

        return null;
    },

    start(event) {
        const rect = this.canvas.getBoundingClientRect();
        const mx = event.clientX - rect.left;
        const my = event.clientY - rect.top;

        this.lastMouseX = mx;
        this.lastMouseY = my;

        const s = this.selection;

        const handle = this.getHandleAt(mx, my);
        if (handle) {
            this.dragMode = "resize";
            this.resizeHandle = handle;
            this.isDragging = true;
            this.startX = mx;
            this.startY = my;
            return;
        }

        if (
            s.x !== null &&
            mx >= s.x &&
            mx <= s.x + s.w &&
            my >= s.y &&
            my <= s.y + s.h
        ) {
            this.dragMode = "move";
            this.offsetX = mx - s.x;
            this.offsetY = my - s.y;
            this.isDragging = true;
            return;
        }

        this.dragMode = "new";
        this.isDragging = true;
        this.selection = { x: null, y: null, w: null, h: null };

        this.startX = mx;
        this.startY = my;
        this.currentX = mx;
        this.currentY = my;
    },

    move(event) {
        const rect = this.canvas.getBoundingClientRect();
        const mx = event.clientX - rect.left;
        const my = event.clientY - rect.top;

        // always update mouse position (even when not dragging)
        this.lastMouseX = mx;
        this.lastMouseY = my;

        if (!this.isDragging) return;

        const s = this.selection;

        if (this.dragMode === "move") {
            s.x = mx - this.offsetX;
            s.y = my - this.offsetY;

            s.x = Math.max(0, Math.min(s.x, this.canvas.width - s.w));
            s.y = Math.max(0, Math.min(s.y, this.canvas.height - s.h));

            this.draw();
            return;
        }

        if (this.dragMode === "resize") {
            const dx = mx - this.startX;
            const dy = my - this.startY;

            let { x, y, w, h } = s;

            switch (this.resizeHandle) {
                case "n":
                    y += dy;
                    h -= dy;
                    break;
                case "s":
                    h += dy;
                    break;
                case "w":
                    x += dx;
                    w -= dx;
                    break;
                case "e":
                    w += dx;
                    break;
                case "nw":
                    x += dx;
                    w -= dx;
                    y += dy;
                    h -= dy;
                    break;
                case "ne":
                    w += dx;
                    y += dy;
                    h -= dy;
                    break;
                case "sw":
                    x += dx;
                    w -= dx;
                    h += dy;
                    break;
                case "se":
                    w += dx;
                    h += dy;
                    break;
            }

            const minSize = 5;
            if (w < minSize) w = minSize;
            if (h < minSize) h = minSize;

            if (x < 0) x = 0;
            if (y < 0) y = 0;
            if (x + w > this.canvas.width) w = this.canvas.width - x;
            if (y + h > this.canvas.height) h = this.canvas.height - y;

            this.selection = { x, y, w, h };

            this.startX = mx;
            this.startY = my;

            this.draw();
            this.drawZoom(mx, my, event);
            return;
        }

        if (this.dragMode === "new") {
            const x = Math.min(this.startX, mx);
            const y = Math.min(this.startY, my);
            const w = Math.abs(mx - this.startX);
            const h = Math.abs(my - this.startY);

            this.selection = { x, y, w, h };
            this.draw();
        }
    },

    end() {
        this.isDragging = false;
        this.dragMode = null;
        this.resizeHandle = null;
        this.clearZoom();
    },

    draw() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        this.ctx.drawImage(this.img, 0, 0);

        const s = this.selection;
        if (s.x !== null && s.w > 0 && s.h > 0) {
            this.ctx.strokeStyle = "red";
            this.ctx.lineWidth = 2;
            this.ctx.strokeRect(s.x, s.y, s.w, s.h);

            const hs = this.handleSize;
            const half = hs / 2;
            const left = s.x;
            const right = s.x + s.w;
            const top = s.y;
            const bottom = s.y + s.h;

            const handles = [
                { x: left, y: top },     // nw
                { x: right, y: top },    // ne
                { x: left, y: bottom },  // sw
                { x: right, y: bottom }, // se
            ];

            this.ctx.fillStyle = "red";
            handles.forEach((h) => {
                this.ctx.fillRect(h.x - half, h.y - half, hs, hs);
            });
        }
    },

    drawZoom(mx, my, event) {
        if (!this.zoomCtx) return;

        const size = this.zoomSize;
        const scale = this.zoomScale;
        const srcSize = size / scale;

        let sx = mx - srcSize / 2;
        let sy = my - srcSize / 2;

        if (sx < 0) sx = 0;
        if (sy < 0) sy = 0;
        if (sx + srcSize > this.canvas.width) sx = this.canvas.width - srcSize;
        if (sy + srcSize > this.canvas.height) sy = this.canvas.height - srcSize;

        this.zoomCtx.clearRect(0, 0, size, size);

        this.zoomCtx.drawImage(
            this.canvas,
            sx,
            sy,
            srcSize,
            srcSize,
            0,
            0,
            size,
            size
        );

        this.zoomCtx.strokeStyle = "red";
        this.zoomCtx.lineWidth = 1;
        this.zoomCtx.beginPath();
        this.zoomCtx.moveTo(size / 2, 0);
        this.zoomCtx.lineTo(size / 2, size);
        this.zoomCtx.moveTo(0, size / 2);
        this.zoomCtx.lineTo(size, size / 2);
        this.zoomCtx.stroke();

        this.zoomPosX = event.pageX + 20;
        this.zoomPosY = event.pageY + 20;
        this.showZoom = true;
    },

    clearZoom() {
        if (!this.zoomCtx) return;
        this.zoomCtx.clearRect(0, 0, this.zoomCanvas.width, this.zoomCanvas.height);
        this.showZoom = false;
    },

    crop() {
        const s = this.selection;
        if (!s || s.w <= 0 || s.h <= 0 || s.x === null) {
            alert("Select an area first");
            return;
        }

        const offCanvas = document.createElement("canvas");
        offCanvas.width = s.w;
        offCanvas.height = s.h;

        const offCtx = offCanvas.getContext("2d");
        offCtx.drawImage(this.img, s.x, s.y, s.w, s.h, 0, 0, s.w, s.h);

        this.preview = offCanvas.toDataURL("image/png");
    },

    reset() {
        this.preview = null;
        this.selection = { x: null, y: null, w: null, h: null };
        this.ctx.drawImage(this.img, 0, 0);
        this.clearZoom();
    },

    // ---------------------------
    // Clipboard helpers (NEW)
    // ---------------------------
    async copySelectionToClipboard() {
        const s = this.selection;
        if (!s || s.x === null || s.w <= 0 || s.h <= 0) return;

        const offCanvas = document.createElement("canvas");
        offCanvas.width = s.w;
        offCanvas.height = s.h;

        const offCtx = offCanvas.getContext("2d");
        // copy pixels FROM CURRENT CANVAS (includes any pastes/edits)
        offCtx.drawImage(this.canvas, s.x, s.y, s.w, s.h, 0, 0, s.w, s.h);

        const blob = await new Promise((resolve) =>
            offCanvas.toBlob(resolve, "image/png")
        );
        if (!blob) return;

        if (!navigator.clipboard?.write) {
            console.warn("Clipboard write not supported in this browser/context.");
            return;
        }

        await navigator.clipboard.write([new ClipboardItem({ "image/png": blob })]);
    },

    async pasteBlobOntoSource(blob) {
        const url = URL.createObjectURL(blob);

        const pasted = new Image();
        pasted.onload = () => {
            // Target height = pasted image height
            const targetHeight = pasted.height;
            const scale = targetHeight / pasted.height; // = 1, but kept explicit
            const targetWidth = Math.round(pasted.width * scale);

            // 🔥 Reset canvas completely
            this.canvas.width = targetWidth;
            this.canvas.height = targetHeight;

            // Clear any old state
            this.selection = { x: null, y: null, w: null, h: null };
            this.preview = null;

            // Draw pasted image scaled to 100% height
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
            this.ctx.drawImage(
                pasted,
                0,
                0,
                pasted.width,
                pasted.height,
                0,
                0,
                targetWidth,
                targetHeight
            );

            // Make canvas the new source image
            const newSrc = this.canvas.toDataURL("image/png");
            const newImg = new Image();
            newImg.onload = () => {
                this.img = newImg;
                this.draw(); // redraw overlays (none yet)
            };
            newImg.src = newSrc;

            URL.revokeObjectURL(url);
        };

        pasted.onerror = () => URL.revokeObjectURL(url);
        pasted.src = url;
    }


});

export default function registerCropperComponent() {
    if (window.Alpine?.data) {
        window.Alpine.data("cropperComponent", createCropper);
    }
}

registerCropperComponent();
