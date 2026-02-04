<!-- Vendor js -->
<script src="assets/js/vendors.min.js"></script>

<!-- App js -->
<script src="assets/js/app.js"></script>

<?php $baseUrl = function_exists('base_url') ? rtrim(base_url(), '/') : ''; ?>
<script>
  if ("serviceWorker" in navigator) {
    window.addEventListener("load", () => {
      const baseUrl = "<?php echo $baseUrl; ?>";
      const swUrl = baseUrl ? `${baseUrl}/sw.js` : "/sw.js";
      const scope = baseUrl ? `${baseUrl}/` : "/";
      navigator.serviceWorker.register(swUrl, { scope }).catch((error) => {
        console.warn("Service worker registration failed:", error);
      });
    });
  }
</script>
