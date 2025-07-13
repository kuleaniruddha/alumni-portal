// Wait for the DOM to load
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("search");
    const alumniList = document.getElementById("alumni-list");

    // Function to fetch and display alumni data
    function fetchAlumni(searchTerm = "") {
        // Sending the search term to searchAlumni.php
        fetch(`searchAlumni.php?search=${searchTerm}`)
            .then(response => response.json())
            .then(data => {
                alumniList.innerHTML = ""; // Clear existing list

                if (data.length > 0) {
                    // Loop through the alumni data and display it
                    data.forEach(alumnus => {
                        const alumniItem = document.createElement("div");
                        alumniItem.classList.add("alumni-item");
                        alumniItem.innerHTML = `
                            <h3>${alumnus.name}</h3>
                            <p><strong>Email:</strong> ${alumnus.email}</p>
                            <p><strong>Batch:</strong> ${alumnus.batch}</p>
                            <p><strong>Job:</strong> ${alumnus.job}</p>
                        `;
                        alumniList.appendChild(alumniItem);
                    });
                } else {
                    alumniList.innerHTML = "<p>No alumni found.</p>";
                }
            })
            .catch(error => {
                console.error('Error fetching alumni data:', error);
                alumniList.innerHTML = "<p>Failed to load alumni data.</p>";
            });
    }

    // Initial fetch with empty search term
    fetchAlumni();

    // Listen for changes in the search input field
    searchInput.addEventListener("input", function() {
        const searchTerm = searchInput.value.trim();
        fetchAlumni(searchTerm); // Fetch filtered alumni based on the input
    });
});
