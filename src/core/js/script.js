function filterVideos() {
  const query = document.getElementById("searchInput").value.toLowerCase();
  const cards = document.querySelectorAll(".video-card");
  const cats = document.querySelectorAll("#cats");
  let visible = 0;

  for (let i = 0; i < cards.length; i++) {
    const title = cards[i].querySelector("p").textContent.toLowerCase();
    const description = cards[i].querySelectorAll("p")[1].textContent.toLowerCase();
    const category = cats[i].textContent.toLowerCase();

    const match = title.includes(query) || description.includes(query) || category.includes(query);
    cards[i].style.display = match ? "" : "none";
    if (match) visible++;
  }

  document.getElementById("noResults").style.display =
    visible === 0 ? "" : "none";
}
