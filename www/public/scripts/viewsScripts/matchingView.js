const matchingSection = document.querySelector("#Matching");
const matchingGrid = document.querySelector("#matchingGrid");
const matchingList = document.querySelector("#matchingList");
const gridViewToggle = document.querySelector("#gridViewToggle");
const listViewToggle = document.querySelector("#listViewToggle");

export async function showMatchingView(object) {
  const searchSection = document.querySelector("#Search");
  const matchingTitle = matchingSection.querySelector("h1");
  const detailsContent = matchingSection.querySelector("#detailsContent");

  matchingTitle.textContent = `Showing ${object.numOfImages} photos in ${object.name}`;
  detailsContent.textContent = `Details for ${object.name}`;

  searchSection.classList.add("hide");
  matchingSection.classList.remove("hide");

  try {
    const response = await fetch(
      `/api/matching?area=${object.name}&type=${object.type}`,
    );
    const photos = await response.json();
    displayMatchingPhotos(photos);
  } catch (error) {
    console.error("Error fetching matching photos:", error);
  }
}

function displayMatchingPhotos(photos) {
  matchingGrid.textContent = "";
  matchingList.textContent = "";

  const cloudinaryBaseUrl =
    "https://res.cloudinary.com/dppo4xs3o/image/upload/c_auto,h_60,w_60/r_100/";

  photos.forEach((photo) => {
    const cloudinaryUrl = cloudinaryBaseUrl + photo.Path;

    const photoDiv = document.createElement("div");
    photoDiv.classList.add("photo-item");

    const photoImg = document.createElement("img");
    photoImg.src = cloudinaryUrl;
    photoImg.alt = photo.Title;

    const caption = document.createElement("p");
    caption.textContent = `${photo.City ? photo.City + ", " : ""}${
      photo.CountryName
    } - ${photo.fullname}`;

    photoDiv.appendChild(photoImg);
    photoDiv.appendChild(caption);

    matchingGrid.appendChild(photoDiv);

    const listItem = photoDiv.cloneNode(true);
    matchingList.appendChild(listItem);
  });
}

gridViewToggle.addEventListener("click", () => {
  matchingGrid.classList.remove("hide");
  matchingList.classList.add("hide");
});

listViewToggle.addEventListener("click", () => {
  matchingGrid.classList.add("hide");
  matchingList.classList.remove("hide");
});
