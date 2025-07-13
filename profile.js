document.addEventListener("DOMContentLoaded", function () {
    const profilePic = document.getElementById("profilePic");
    const profilePhotoInput = document.getElementById("profilePhoto");
    const profileForm = document.getElementById("profileForm");
    const message = document.getElementById("message");

    async function fetchProfile() {
        try {
            let response = await fetch("profile.php");
            let data = await response.json();

            if (data.success) {
                document.getElementById("name").value = data.name;
                document.getElementById("batch").value = data.batch;
                document.getElementById("current_position").value = data.current_position;

                profilePic.src = data.profile_pic || "default.jpg";
            } else {
                message.innerHTML = `❌ ${data.message}`;
            }
        } catch (error) {
            message.innerHTML = "❌ Error fetching profile data!";
        }
    }

    fetchProfile();

    profileForm.addEventListener("submit", async function (event) {
        event.preventDefault();

        let name = document.getElementById("name").value;
        let batch = document.getElementById("batch").value;
        let current_position = document.getElementById("current_position").value;

        let response = await fetch("update_profile.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ name, batch, current_position })
        });

        let data = await response.json();
        message.innerHTML = data.success ? `✅ ${data.message}` : `❌ ${data.message}`;
    });

    profilePhotoInput.addEventListener("change", async function () {
        let formData = new FormData();
        formData.append("profilePhoto", profilePhotoInput.files[0]);

        let response = await fetch("upload_photo.php", {
            method: "POST",
            body: formData
        });

        let data = await response.json();
        if (data.success) {
            profilePic.src = data.profile_pic;
            message.innerHTML = `✅ ${data.message}`;
        } else {
            message.innerHTML = `❌ ${data.message}`;
        }
    });
});
