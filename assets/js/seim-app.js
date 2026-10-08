/**
 * SEIM ERP - Lightweight Application JS
 */

document.addEventListener("DOMContentLoaded", function () {
  // 1. Initialize Lucide Icons
  if (window.lucide && typeof window.lucide.createIcons === "function") {
    window.lucide.createIcons();
  }

  // 2. Sidebar Toggle (Desktop Collapse & Mobile Offcanvas)
  const toggleButtons = document.querySelectorAll(
    ".button-toggle-menu, .sidenav-toggle-button, .button-on-hover, [data-toggle-sidebar]"
  );
  const closeOffcanvasButtons = document.querySelectorAll(".button-close-offcanvas");

  toggleButtons.forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      if (window.innerWidth <= 991.98) {
        document.body.classList.toggle("sidebar-enable");
      } else {
        document.body.classList.toggle("sidebar-collapsed");
        const isCollapsed = document.body.classList.contains("sidebar-collapsed");
        localStorage.setItem("seim_sidebar_collapsed", isCollapsed ? "1" : "0");
      }
    });
  });

  closeOffcanvasButtons.forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      document.body.classList.remove("sidebar-enable");
    });
  });

  // Restore sidebar state from localStorage on desktop
  if (window.innerWidth > 991.98 && localStorage.getItem("seim_sidebar_collapsed") === "1") {
    document.body.classList.add("sidebar-collapsed");
  }

  // Close sidebar on mobile when clicking backdrop
  document.addEventListener("click", function (e) {
    if (document.body.classList.contains("sidebar-enable")) {
      const sidebar = document.querySelector(".sidenav-menu");
      const toggle = document.querySelector(".button-toggle-menu");
      if (sidebar && !sidebar.contains(e.target) && toggle && !toggle.contains(e.target)) {
        document.body.classList.remove("sidebar-enable");
      }
    }
  });

  // 3. Highlight and open active route in sidebar
  const currentRoute = new URLSearchParams(window.location.search).get("route") || "dashboard";

  document.querySelectorAll(".side-nav a").forEach((link) => {
    const href = link.getAttribute("href") || "";
    if (!href || href.startsWith("#")) return;

    try {
      const url = new URL(href, window.location.origin);
      const linkRoute = url.searchParams.get("route");

      const isMatch =
        linkRoute === currentRoute ||
        (currentRoute === "dashboard" && (href === "index.php" || linkRoute === "dashboard"));

      if (isMatch) {
        link.classList.add("active");
        let parentItem = link.closest(".side-nav-item");
        if (parentItem) {
          parentItem.classList.add("active");
        }

        // Open ALL parent collapses recursively
        let parentEl = link.parentElement;
        while (parentEl && !parentEl.classList.contains("side-nav")) {
          if (parentEl.classList.contains("collapse")) {
            parentEl.classList.add("show");
            const trigger = document.querySelector(
              `[data-bs-target="#${parentEl.id}"], [href="#${parentEl.id}"]`
            );
            if (trigger) {
              trigger.setAttribute("aria-expanded", "true");
              trigger.classList.add("active");
            }
          }
          parentEl = parentEl.parentElement;
        }
      }
    } catch (e) {}
  });

  // 4. Robust Sidebar Accordion Collapse Click Handler
  document.querySelectorAll('.side-nav [data-bs-toggle="collapse"]').forEach((toggleLink) => {
    toggleLink.addEventListener("click", function (e) {
      e.preventDefault();
      const targetSelector = this.getAttribute("href") || this.getAttribute("data-bs-target");
      if (!targetSelector || targetSelector === "#") return;

      const targetEl = document.querySelector(targetSelector);
      if (!targetEl) return;

      if (window.bootstrap && bootstrap.Collapse) {
        const bsCollapse = bootstrap.Collapse.getOrCreateInstance(targetEl, { toggle: false });
        bsCollapse.toggle();
      } else {
        targetEl.classList.toggle("show");
      }

      const isNowExpanded = targetEl.classList.contains("show") || !this.getAttribute("aria-expanded") === "true";
      this.setAttribute("aria-expanded", targetEl.classList.contains("show") ? "true" : "false");
    });
  });

  // 5. Initialize Bootstrap Tooltips
  if (typeof bootstrap !== "undefined" && bootstrap.Tooltip) {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
      new bootstrap.Tooltip(tooltipTriggerEl);
    });
  }

  // 6. Initialize Bootstrap Popovers
  if (typeof bootstrap !== "undefined" && bootstrap.Popover) {
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.forEach(function (popoverTriggerEl) {
      new bootstrap.Popover(popoverTriggerEl);
    });
  }
});
