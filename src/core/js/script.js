function filterVideos() {
  const query = document.getElementById("searchInput").value.toLowerCase();
  const cards = document.querySelectorAll(".video-card");
  let visible = 0;

  cards.forEach((card) => {
    const title = card.dataset.title;
    const match = title.includes(query);
    card.style.display = match ? "" : "none";
    if (match) visible++;
  });

  document.getElementById("noResults").style.display =
    visible === 0 ? "" : "none";
}
