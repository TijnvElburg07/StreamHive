function toggleFilter() {
  const dropdown = document.getElementById("filterDropdown");
  const btn = document.getElementById("filterToggle");
  const isOpen = dropdown.classList.contains("open");

  dropdown.classList.toggle("open", !isOpen);
  btn.classList.toggle("active", !isOpen);
}

document.addEventListener("click", function (e) {
  const wrapper = document.querySelector(".filter-wrapper");
  if (wrapper && !wrapper.contains(e.target)) {
    document.getElementById("filterDropdown").classList.remove("open");
    document.getElementById("filterToggle").classList.remove("active");
  }
});

function filterVideos() {
  const query = document.getElementById("searchInput").value.toLowerCase().trim();
  const checkboxes = document.querySelectorAll(".filter-options input[type='checkbox']:checked");
  const selectedCats = Array.from(checkboxes).map(cb => cb.value);

  const cards = document.querySelectorAll(".video-card");
  let visible = 0;

  cards.forEach(card => {
    const title = card.dataset.title || "";
    const description = card.dataset.description || "";
    const categories = JSON.parse(card.dataset.categories || "[]");

    // Zoekterm match
    const searchMatch =
      query === "" ||
      title.includes(query) ||
      description.includes(query) ||
      categories.some(c => c.includes(query));

    const catMatch =
      selectedCats.length === 0 ||
      selectedCats.every(sc => categories.includes(sc));

    const show = searchMatch && catMatch;
    card.style.display = show ? "" : "none";
    if (show) visible++;
  });

  document.getElementById("noResults").style.display =
    visible === 0 ? "" : "none";

  updateFilterBadge(selectedCats.length);
}

function updateFilterBadge(count) {
  const badge = document.getElementById("filterBadge");
  const clearBtn = document.getElementById("filterClear");
  if (count > 0) {
    badge.textContent = count;
    badge.style.display = "inline-flex";
    clearBtn.style.display = "inline-flex";
  } else {
    badge.style.display = "none";
    clearBtn.style.display = "none";
  }
}

function clearFilters() {
  document.querySelectorAll(".filter-options input[type='checkbox']").forEach(cb => {
    cb.checked = false;
  });
  filterVideos();
}