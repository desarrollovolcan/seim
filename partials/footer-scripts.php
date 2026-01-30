<!-- Vendor js -->
<script src="assets/js/vendors.min.js"></script>

<!-- App js -->
<script src="assets/js/app.js"></script>

<script>
  if ("serviceWorker" in navigator) {
    window.addEventListener("load", () => {
      const swUrl = "<?php echo rtrim(base_url(), '/'); ?>/sw.js";
      navigator.serviceWorker.register(swUrl).catch((error) => {
        console.warn("Service worker registration failed:", error);
      });
    });
  }
</script>
