const searchBarElem = document.querySelector("#searchBox");
const searchSection = document.querySelector("#Search");
const matchingSection = document.querySelector("#Matching");
const matchingGrid = document.querySelector("#matchingGrid");
const matchingList = document.querySelector("#matchingList");
const gridViewToggle = document.querySelector("#gridViewToggle");
const listViewToggle = document.querySelector("#listViewToggle");

searchSection.classList.add("wrapper");

const container = document.querySelector("#container"); // The parent element

// Handle input event in the search bar
function handleInputEvent() {
  const searchValue = searchBarElem.value.trim();
  if (searchValue.length >= 2) {
    // Fetch data only if input length is 2 or more
    fetch(`/api/search?query=${searchValue}`)
      .then((response) => response.json())
      .then(makeElements)
      .catch((error) => console.error("Error fetching data:", error));
  }
}

// Create search result elements dynamically
function makeElements(objects) {
  const searchResults = document.querySelector("#searchResults");
  searchResults.textContent = ""; // Clear previous results

  if (objects.length === 0) {
    const noResultsMessage = document.createElement("p");
    noResultsMessage.textContent = "No results found.";
    searchResults.appendChild(noResultsMessage);
    return;
  }

  objects.forEach((object) => {
    const photoCount = object.numOfImages || 0;
    const resultDiv = document.createElement("div");
    resultDiv.classList.add("search-result"); // Add a class for styling
    resultDiv.textContent = `${object.name} (${photoCount} photos)`;

    // Create a div for each result to hold more information (name, usernames, type)
    const infoDiv = document.createElement("div");
    infoDiv.classList.add("result-info");

    // Add the information using textContent
    const nameHeading = document.createElement("h3");
    nameHeading.textContent = object.name;
    nameHeading.classList.add("hide");

    const usernamesPara = document.createElement("p");
    usernamesPara.textContent = `Usernames: ${object.UserNames}`;
    usernamesPara.classList.add("hide");

    const typePara = document.createElement("p");
    typePara.textContent = `Type: ${object.type}`;
    typePara.classList.add("hide");

    infoDiv.appendChild(nameHeading);
    infoDiv.appendChild(usernamesPara);
    infoDiv.appendChild(typePara);

    // Add result details to the result div
    resultDiv.appendChild(infoDiv);
    resultDiv.dataset.name = object.name; // Store the name as a data attribute
    resultDiv.dataset.type = object.type; // Store the type as a data attribute
    resultDiv.dataset.numOfImages = object.numOfImages || 0; // Store number of images

    // Append to the search results container
    searchResults.appendChild(resultDiv);
  });
}

// Event delegation for clicks on the entire #container
container.addEventListener("click", (event) => {
  const clickedElement = event.target;

  // Check if the clicked element is a div (result item) that was dynamically created
  if (clickedElement && clickedElement.dataset.name) {
    const object = {
      name: clickedElement.dataset.name,
      type: clickedElement.dataset.type,
      numOfImages: clickedElement.dataset.numOfImages,
    };
    showMatchingView(object);
  }
});

// Function to show the matching view based on the search result clicked
export async function showMatchingView(object) {
  const searchSection = document.querySelector("#Search");
  const matchingTitle = matchingSection.querySelector("h1");
  const detailsContent = matchingSection.querySelector("#detailsContent");

  matchingTitle.textContent = `Showing ${object.numOfImages} items in ${object.name}`;
  detailsContent.textContent = `Details for ${object.name}`;

  searchSection.classList.add("hide");
  matchingSection.classList.remove("hide");

  try {
    // Fetch data specific to the area (Naples) and type (city)
    const response = await fetch(
      `/api/search?area=${object.name}&type=${object.type}`,
    );
    const photos = await response.json();
    displayMatchingResults(photos);
  } catch (error) {
    console.error("Error fetching matching photos:", error);
  }
}

// Function to display the matching results (with names and usernames)
function displayMatchingResults(results) {
  matchingGrid.textContent = "";
  matchingList.textContent = "";

  results.forEach((result) => {
    const resultDiv = document.createElement("div");
    resultDiv.classList.add("result-item");

    const infoDiv = document.createElement("div");
    infoDiv.classList.add("result-info");

    // Create elements for name, usernames, and type
    const nameHeading = document.createElement("h4");
    nameHeading.textContent = result.name;

    const usernamesPara = document.createElement("p");
    usernamesPara.textContent = `Usernames: ${result.UserNames}`;

    const typePara = document.createElement("p");
    typePara.textContent = `Type: ${result.type}`;

    // Append all to the info div
    infoDiv.appendChild(nameHeading);
    infoDiv.appendChild(usernamesPara);
    infoDiv.appendChild(typePara);

    resultDiv.appendChild(infoDiv);

    matchingGrid.appendChild(resultDiv);

    // Clone the result div for list view
    const listItem = resultDiv.cloneNode(true);
    matchingList.appendChild(listItem);
  });
}

// Toggle views for grid and list display
gridViewToggle.addEventListener("click", () => {
  matchingGrid.classList.remove("hide");
  matchingList.classList.add("hide");
});

listViewToggle.addEventListener("click", () => {
  matchingGrid.classList.add("hide");
  matchingList.classList.remove("hide");
});

// Listen for input events on the search box
searchBarElem.addEventListener("input", handleInputEvent);

// Export for potential external usage
export { handleInputEvent, makeElements };
