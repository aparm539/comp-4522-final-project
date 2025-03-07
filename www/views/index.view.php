<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./styles/style3.css">
    <script type="module" src="./scripts/index.js"></script>
</head>

<body>
    <!-------------------------------------------------------- Search View Section -------------------------------------------------------->
    <section id="Search">
        <h1>Search a country, city, or continent</h1>
        <input type="search" id="searchBox" placeholder="Type to search...">

        <div id="container">
            <div id="searchResults"></div>
        </div>
    </section>

    <!-------------------------------------------------------- Matching View Section -------------------------------------------------------->

    <section id="Matching" class="hide">
        <h1>Showing numberOfPhotos in place.name</h1>
        <nav>
            <div id="gridViewToggle"></div>
            <div id="listViewToggle"></div>
        </nav>
        <section id="details" class="hide">
            <h2>Details</h2>
            <div id="detailsContent"></div>
        </section>
        <div id="matchingGrid" class="grid"></div>
        <div id="matchingList" class="list hide"></div>
    </section>


    <!-------------------------------------------------------- Photo Detail View Section -------------------------------------------------------->

    <section id="Photo-Detail" class="hide">
        <h1>Photo Detail</h1>
        <img src="" alt="">
        <p></p>

    </section>

    <!-------------------------------------------------------- Country Detail View Section -------------------------------------------------------->

    <section id="Country-Detail" class="hide">
        <h1>Country Detail</h1>

    </section>

</body>

</html>