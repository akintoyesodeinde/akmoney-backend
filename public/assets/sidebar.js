// Sidebar state management
let sidebarState = {
  sidebar: null,
  sidebarOverlay: null,
  navLinks: null,
  closeSidebarBtn: null,
};

let sidebarInitRetries = 0;
const MAX_SIDEBAR_RETRIES = 10; // Maximum retry attempts

// Global toggle function
window.toggleSidebar = function () {
  if (!sidebarState.sidebar) return;

  if (sidebarState.sidebar.classList.contains("-translate-x-full")) {
    sidebarState.sidebar.classList.remove("-translate-x-full");
    sidebarState.sidebarOverlay.classList.remove("-translate-x-full");
  } else {
    sidebarState.sidebar.classList.add("-translate-x-full");
    sidebarState.sidebarOverlay.classList.add("-translate-x-full");
  }
};

function setActiveLink(page) {
  if (!sidebarState.navLinks) return;

  sidebarState.navLinks.forEach((link) => {
    if (link.getAttribute("data-page") === page) {
      link.style.background = getComputedStyle(document.documentElement).getPropertyValue('--nav-active-bg').trim() || '#A71B47';
      link.style.color = "#ffffff";
      link.classList.remove("text-text-secondary");
    } else {
      link.style.background = "";
      link.style.color = "";
      link.classList.add("text-text-secondary");
    }
  });
  localStorage.setItem("activePage", page);
}

function closeSidebar() {
  if (!sidebarState.sidebar) return;
  sidebarState.sidebar.classList.add("-translate-x-full");
  sidebarState.sidebarOverlay.classList.add("-translate-x-full");
}

function initSidebar() {
  // Initialize sidebar active state based on current page
  const currentPage =
    document.body.getAttribute("data-current-page") || "dashboard";
  const navLinks = document.querySelectorAll(".nav-link");
  const sidebar = document.getElementById("sidebar");
  const sidebarOverlay = document.getElementById("sidebar-overlay");
  const closeSidebarBtn = document.getElementById("close-sidebar");

  if (!sidebar || !sidebarOverlay || !navLinks.length) {
    if (sidebarInitRetries < MAX_SIDEBAR_RETRIES) {
      sidebarInitRetries++;
      console.warn(
        "Sidebar elements not found, retrying... (" +
          sidebarInitRetries +
          "/" +
          MAX_SIDEBAR_RETRIES +
          ")"
      );
      setTimeout(initSidebar, 100);
    } else {
      console.warn(
        "Sidebar elements not found after " +
          MAX_SIDEBAR_RETRIES +
          " retries. Page may not have a sidebar."
      );
    }
    return;
  }

  // Store in global state
  sidebarState.sidebar = sidebar;
  sidebarState.sidebarOverlay = sidebarOverlay;
  sidebarState.navLinks = navLinks;
  sidebarState.closeSidebarBtn = closeSidebarBtn;

  // Set initial active state - prioritize data-current-page attribute over localStorage
  setActiveLink(currentPage);

  // Handle navigation link clicks
  navLinks.forEach((link) => {
    link.addEventListener("click", function () {
      const page = this.getAttribute("data-page");
      setActiveLink(page);

      // Close sidebar on mobile after navigation
      if (window.innerWidth < 768) {
        closeSidebar();
      }
    });
  });

  // Handle mobile sidebar close button
  if (closeSidebarBtn) {
    closeSidebarBtn.addEventListener("click", function () {
      closeSidebar();
    });
  }

  // Handle overlay click to close sidebar
  sidebarOverlay.addEventListener("click", function () {
    closeSidebar();
  });

  // Initialize sidebar toggle button
  const toggleBtn = document.getElementById("toggle-sidebar");
  if (toggleBtn) {
    toggleBtn.addEventListener("click", function () {
      window.toggleSidebar();
    });
  }

  console.log("Sidebar initialized successfully with page:", currentPage);
}

// Export for use
window.initSidebar = initSidebar;

// Auto-initialize sidebar when DOM is loaded
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initSidebar);
} else {
  initSidebar();
}
