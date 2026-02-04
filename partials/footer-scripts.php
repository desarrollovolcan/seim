<!-- Vendor js -->
<script src="assets/js/vendors.min.js"></script>

<!-- App js -->
<script src="assets/js/app.js"></script>

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
