// preview hinh anh
document.addEventListener("DOMContentLoaded", function () {
  function setupPreview(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    if (input && preview) {
      input.addEventListener("change", function () {
        preview.innerHTML = "";
        const files = this.files;
        for (let i = 0; i < files.length; i++) {
          const img = document.createElement("img");
          img.src = URL.createObjectURL(files[i]);
          img.width = 150;
          img.className = "img-thumbnail me-2 mb-2";
          preview.appendChild(img);
        }
      });
    }
  }

  setupPreview("image", "preview");
  setupPreview("images", "preview-gallery");
});
