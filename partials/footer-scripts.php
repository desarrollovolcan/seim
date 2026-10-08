<!-- Bootstrap 5.3.x Bundle JS (with Popper, Local) -->
<script src="assets/plugins/bootstrap/bootstrap.bundle.min.js"></script>

<!-- Lucide Icons -->
<script src="assets/plugins/lucide/lucide.min.js"></script>

<!-- SEIM Clean App JS -->
<script src="assets/js/seim-app.js"></script>

<?php $baseUrl = function_exists('base_url') ? rtrim(base_url(), '/') : ''; ?>
<script>
  if ("serviceWorker" in navigator) {
    window.addEventListener("load", () => {
      const baseUrl = <?php echo json_encode($baseUrl); ?>;
      const basePath = baseUrl
        ? new URL(baseUrl, window.location.origin).pathname.replace(/\/$/, "")
        : window.location.pathname.replace(/\/[^/]*$/, "");
      const scope = `${basePath || ""}/`;
      const swUrl = new URL(`${scope}sw.js`, window.location.origin).toString();
      navigator.serviceWorker.register(swUrl, { scope }).catch((error) => {
        console.warn("Service worker registration failed:", error);
      });
    });
  }
</script>
