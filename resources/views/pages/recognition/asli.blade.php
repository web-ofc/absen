<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Face Recognition Absensi</title>

  <meta name="csrf-token" content="{{ csrf_token() }}">


  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- FontAwesome (biar icon sign in/out muncul) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}" />

  <style>
    #map {
      width: 100%;
      height: 500px;
    }
    video, canvas {
      border-radius: .5rem;
    }
  </style>
</head>
<body>
<div class="container py-4">
  <h2 class="text-center mb-4">Face Recognition Absensi</h2>

  <div class="row justify-content-center">
    <!-- Video Section -->
    <div class="col-md-6 text-center mb-4">
      <div class="position-relative d-inline-block">
        <video id="video" width="480" height="360" autoplay muted class="border rounded"></video>
        <canvas id="overlay" width="480" height="360"
                class="position-absolute top-0 start-50 translate-middle-x"></canvas>
      </div>
      <p id="status" class="mt-3 fw-bold">Loading models...</p>

      <!-- Tombol Absen -->
      <div class="mt-3">
        <button id="absenMasukBtn" class="btn btn-success me-2" disabled>
          <i class="fas fa-sign-in-alt me-1"></i> Absen Masuk
        </button>
        <button id="absenPulangBtn" class="btn btn-danger" disabled>
          <i class="fas fa-sign-out-alt me-1"></i> Absen Pulang
        </button>
      </div>
    </div>

    <!-- Map Section -->
    <div class="col-md-6 text-center mb-4">
      <h5 class="mb-3">Lokasi Geozone</h5>
      <div id="map"></div>
    </div>
  </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Leaflet JS -->
<script src="{{ asset('leaflet/leaflet.js') }}"></script>

<!-- Face API JS -->

<script>
document.addEventListener("DOMContentLoaded", function () {
  let map;
  let currentDetectedUser = null;

  // === MAP ===
  function initMap(lat, lon, zoom = 16) {
    map = L.map("map").setView([lat, lon], zoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);
  }

  async function loadUserGeozones() {
    const res = await fetch("/me");
    const data = await res.json();

    if (data.geozones.length > 0) {
      const firstZone = data.geozones[0];
      initMap(firstZone.latitude, firstZone.longitude);

      data.geozones.forEach(zone => {
        L.circle([zone.latitude, zone.longitude], {
          color: "blue",
          fillColor: "#3f51b5",
          fillOpacity: 0.2,
          radius: zone.radius
        }).addTo(map).bindPopup(`<b>${zone.name}</b>`);
      });
    } else {
      // fallback kalau user gak punya geozone
      initMap(-6.200000, 106.816666); // fallback Jakarta
      console.warn("User tidak memiliki geozone.");
    }

    if (data.can_attend_anywhere) {
      console.log("User bisa absen di mana saja ✅");
    }

    // Tambahkan marker posisi user
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        pos => {
          const userLat = pos.coords.latitude;
          const userLon = pos.coords.longitude;

          const redIcon = L.icon({
            iconUrl: "https://maps.google.com/mapfiles/ms/icons/red-dot.png",
            iconSize: [32, 32]
          });

          L.marker([userLat, userLon], { icon: redIcon })
            .addTo(map)
            .bindPopup(`Lokasi Anda<br>Lat: ${userLat}, Lon: ${userLon}`)
            .openPopup();

          // Optional: pindahin map ke posisi user
          map.setView([userLat, userLon], 16);
        },
        err => {
          console.error("Geolocation error:", err);
          alert("Gagal mendapatkan lokasi: " + err.message);
        },
        {
          enableHighAccuracy: true,
          timeout: 10000,
          maximumAge: 0
        }
      );
    }
  }

  // === FACE RECOGNITION ===
  const video = document.getElementById('video');
  const overlay = document.getElementById('overlay');
  const statusEl = document.getElementById('status');
  const absenMasukBtn = document.getElementById('absenMasukBtn');
  const absenPulangBtn = document.getElementById('absenPulangBtn');

  // let currentDetectedUser = null;

  function capturePhoto(vid) {
    const canvas = document.createElement("canvas");
    canvas.width = vid.videoWidth;
    canvas.height = vid.videoHeight;
    const ctx = canvas.getContext("2d");
    ctx.drawImage(vid, 0, 0, canvas.width, canvas.height);
    return canvas.toDataURL("image/jpeg");
  }

  async function loadModels() {
    const modelPath = "/models";
    await faceapi.nets.tinyFaceDetector.loadFromUri(modelPath);
    await faceapi.nets.faceLandmark68Net.loadFromUri(modelPath);
    await faceapi.nets.faceRecognitionNet.loadFromUri(modelPath);
    statusEl.innerText = "Models loaded!";
  }

  function startVideo() {
    navigator.mediaDevices.getUserMedia({ video: {} })
      .then(stream => video.srcObject = stream)
      .catch(err => {
        console.error("Camera error:", err);
        statusEl.innerText = "Error: Kamera tidak bisa diakses.";
      });
  }

  async function loadLabeledDescriptors() {
    const response = await fetch("/api/users");
    const users = await response.json();

    return Promise.all(users
      .filter(u => u.face_descriptor) // filter yg ada datanya aja
      .map(user => {
        let descriptor = [];
        try {
          descriptor = JSON.parse(user.face_descriptor);
        } catch (e) {
          console.warn("Descriptor parse error for user", user.name);
        }
        return new faceapi.LabeledFaceDescriptors(
          user.name,
          [new Float32Array(descriptor)]
        );
      })
    );
  }


  video.addEventListener("play", async () => {
    const labeledDescriptors = await loadLabeledDescriptors();
    const faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.6);

    const displaySize = { width: video.width, height: video.height };
    faceapi.matchDimensions(overlay, displaySize);

    setInterval(async () => {
      const detections = await faceapi
        .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks()
        .withFaceDescriptors();

      const resizedDetections = faceapi.resizeResults(detections, displaySize);
      overlay.getContext("2d").clearRect(0, 0, overlay.width, overlay.height);

      if (resizedDetections.length > 0) {
        const bestMatch = faceMatcher.findBestMatch(resizedDetections[0].descriptor);
        const box = resizedDetections[0].detection.box;
        new faceapi.draw.DrawBox(box, { label: bestMatch.toString() }).draw(overlay);

        if (bestMatch.label !== "unknown") {
          currentDetectedUser = bestMatch.label;
          statusEl.innerText = `Wajah dikenali: ${bestMatch.label}`;
          absenMasukBtn.disabled = false;
          absenPulangBtn.disabled = false;
        } else {
          currentDetectedUser = null;
          statusEl.innerText = "Wajah tidak dikenali";
          absenMasukBtn.disabled = true;
          absenPulangBtn.disabled = true;
        }
      } else {
        currentDetectedUser = null;
        statusEl.innerText = "Mencari wajah...";
        absenMasukBtn.disabled = true;
        absenPulangBtn.disabled = true;
      }
    }, 1000);
  });


  

  async function submitAttendance(type) {
    if (!currentDetectedUser) {
      alert('Wajah belum dikenali.');
      return;
    }
    const photo = capturePhoto(video);

    try {
      const resTime = await fetch("/server-time");
      const serverTimeData = await resTime.json();

      const response = await fetch("/attendance", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
          name: currentDetectedUser,
          photo: photo,
          type: type,
          server_timestamp: serverTimeData.time,
        })
      });
      const data = await response.json();
      alert(data.message);
    } catch (err) {
      console.error(err);
      alert("Error saat absen.");
    }
  }

  absenMasukBtn.addEventListener('click', () => submitAttendance('in'));
  absenPulangBtn.addEventListener('click', () => submitAttendance('out'));

  (async () => {
    await loadModels();
    startVideo();
     await loadUserGeozones();
  })();
});
</script>
</body>
</html>
