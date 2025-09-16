@extends('layouts.master')



@section('content')
<style>
    #map {
      width: 100%;
      height: 500px;
    }
    video, canvas {
      border-radius: .5rem;
    }
  </style>
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
@endsection

@push('scripts')

<script>
document.addEventListener("DOMContentLoaded", function () {

  // // === MAP ===
  function initMap(lat, lon, zoom = 16) {
    map = L.map("map").setView([lat, lon], zoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);
  }

    let map;
    let currentDetectedUser = null;
    // Tambahkan variabel untuk menyimpan lokasi pengguna
    let userLocation = null;
    let isInGeozone = false;
    let isMouthOpen = false;


    // Fungsi untuk menghitung jarak Haversine (bisa juga di-copy dari PHP, tapi lebih baik dikerjakan di backend)
    function haversineDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3; // metres
        const φ1 = lat1 * Math.PI/180; // φ, λ in radians
        const φ2 = lat2 * Math.PI/180;
        const Δφ = (lat2-lat1) * Math.PI/180;
        const Δλ = (lon2-lon1) * Math.PI/180;

        const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                  Math.cos(φ1) * Math.cos(φ2) *
                  Math.sin(Δλ/2) * Math.sin(Δλ/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

        return R * c;
    }

    // Fungsi untuk cek apakah user di dalam geozone
    function checkGeozone(userLat, userLon, geozones) {
        for (const zone of geozones) {
            const distance = haversineDistance(userLat, userLon, zone.latitude, zone.longitude);
            if (distance <= zone.radius) {
                return true;
            }
        }
        return false;
    }

    async function loadUserGeozones() {
      try {
          const res = await fetch("/me");
          const data = await res.json();
          const geozones = data.geozones;
          const canAttendAnywhere = data.can_attend_anywhere;

          console.log("User can attend anywhere:", canAttendAnywhere);
          console.log("Available geozones:", geozones);

          // Inisialisasi peta
          if (geozones.length > 0) {
              const firstZone = geozones[0];
              initMap(firstZone.latitude, firstZone.longitude);

              // Tambahkan semua geozone ke peta (hanya untuk visualisasi)
              geozones.forEach(zone => {
                  L.circle([zone.latitude, zone.longitude], {
                      color: "blue",
                      fillColor: "#3f51b5",
                      fillOpacity: 0.2,
                      radius: zone.radius
                  }).addTo(map).bindPopup(`<b>${zone.name}</b><br>Radius: ${zone.radius}m`);
              });

              
          } else {
              initMap(-6.200000, 106.816666);
              console.warn("User tidak memiliki geozone.");
          }

          // Dapatkan lokasi pengguna
          if (navigator.geolocation) {
              navigator.geolocation.getCurrentPosition(
                  pos => {
                      userLocation = { 
                          lat: pos.coords.latitude, 
                          lon: pos.coords.longitude 
                      };
                      const userLat = userLocation.lat;
                      const userLon = userLocation.lon;

                      console.log("User location:", userLat, userLon);

                      // Tambahkan marker lokasi pengguna
                      L.marker([userLat, userLon], {
                          icon: L.icon({ 
                              iconUrl: "https://maps.google.com/mapfiles/ms/icons/red-dot.png", 
                              iconSize: [32, 32] 
                          })
                      }).addTo(map).bindPopup(`
                          <b>Lokasi Anda</b><br>
                          Lat: ${userLat.toFixed(6)}<br>
                          Lon: ${userLon.toFixed(6)}<br>
                          Status: ${canAttendAnywhere ? 'Bisa absen di mana saja' : 'Harus dalam geozone'}
                      `).openPopup();

                      map.setView([userLat, userLon], 16);

                      // LOGICA UTAMA: Tentukan isInGeozone berdasarkan can_attend_anywhere
                      if (canAttendAnywhere) {
                          // User bisa absen di mana saja - langsung set true
                          isInGeozone = true;
                          console.log("✅ User bisa absen di mana saja");
                      } else {
                          // User harus dalam geozone - lakukan pengecekan
                          isInGeozone = checkGeozone(userLat, userLon, geozones);
                          console.log("📍 Pengecekan geozone:", isInGeozone);
                      }
                      
                      updateButtonState();
                  },
                  err => {
                      console.error("Geolocation error:", err);
                      
                      if (canAttendAnywhere) {
                          // Jika bisa absen di mana saja, tetap izinkan meski GPS error
                          isInGeozone = true;
                          console.log("📍 GPS error, tetapi bisa absen di mana saja ✅");
                      } else {
                          // Jika harus dalam geozone, tolak absen jika GPS error
                          isInGeozone = false;
                          alert("Gagal mendapatkan lokasi: " + err.message + 
                                "\nAnda harus mengaktifkan GPS untuk absen.");
                      }
                      
                      updateButtonState();
                  },
                  { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
              );
          } else {
              // Fallback untuk browser tanpa geolocation
              if (canAttendAnywhere) {
                  isInGeozone = true;
                  console.log("📍 Browser tidak support geolocation, tetapi bisa absen di mana saja ✅");
              } else {
                  isInGeozone = false;
                  alert("Browser tidak mendukung geolocation. Tidak dapat absen.");
              }
              updateButtonState();
          }
      } catch (error) {
          console.error("Error loading user geozones:", error);
          isInGeozone = false;
          updateButtonState();
      }
  }

  // Fungsi untuk menampilkan informasi debug di console
  function debugGeozoneInfo(canAttendAnywhere, userLocation, geozones, isInGeozone) {
      console.group("Debug Geozone Information");
      console.log("Can attend anywhere:", canAttendAnywhere);
      console.log("User location:", userLocation);
      console.log("Available geozones:", geozones);
      console.log("Is in geozone:", isInGeozone);
      
      if (!canAttendAnywhere && userLocation && geozones.length > 0) {
          geozones.forEach(zone => {
              const distance = haversineDistance(
                  userLocation.lat, userLocation.lon,
                  zone.latitude, zone.longitude
              );
              console.log(`Distance to ${zone.name}: ${distance.toFixed(2)}m (Radius: ${zone.radius}m)`);
          });
      }
      console.groupEnd();
  }


  function updateButtonState() {
    const canAbsen = currentDetectedUser && isInGeozone && isMouthOpen;

    absenMasukBtn.disabled = !canAbsen;
    absenPulangBtn.disabled = !canAbsen;

    if (!currentDetectedUser) {
      statusEl.innerHTML = "🔍 Mencari wajah...";
      statusEl.style.color = "blue";
    } else if (currentDetectedUser === "unknown") {
      statusEl.innerHTML = "❌ Wajah tidak terdaftar";
      statusEl.style.color = "red";
    } else if (!isInGeozone) {
      statusEl.innerHTML = `👤 ${currentDetectedUser} - 📍 Diluar area absen`;
      statusEl.style.color = "orange";
    } else if (!isMouthOpen) {
      statusEl.innerHTML = `👤 ${currentDetectedUser} - 👄 Harap buka mulut`;
      statusEl.style.color = "orange";
    } else {
      statusEl.innerHTML = `✅ ${currentDetectedUser} - Siap absen`;
      statusEl.style.color = "green";
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
    try {
      const response = await fetch("/api/users");
      const users = await response.json();

      const validDescriptors = users
        .filter(u => {
          if (!u.face_descriptor) return false;
          try {
            const d = JSON.parse(u.face_descriptor);
            return Array.isArray(d) && d.length === 128;
          } catch {
            return false;
          }
        })
        .map(user => {
          const descriptor = JSON.parse(user.face_descriptor);
          return {
            label: user.name,
            descriptors: [new Float32Array(descriptor)]
          };
        });

      console.log(`Loaded ${validDescriptors.length} valid face descriptors`);
      return validDescriptors;
    } catch (error) {
      console.error("Error loading labeled descriptors:", error);
      return [];
    }
  }

  // Custom face matcher yang benar
  function createStrictFaceMatcher(labeledDescriptors, threshold = 0.6) {
    return {
      findBestMatch: (queryDescriptor) => {
        let bestMatch = { label: "unknown", distance: 1.0, toString: function() { return this.label; } };
        
        labeledDescriptors.forEach(({ label, descriptors }) => {
          descriptors.forEach(descriptor => {
            const distance = faceapi.euclideanDistance(queryDescriptor, descriptor);
            if (distance < bestMatch.distance) {
              bestMatch = { 
                label, 
                distance,
                toString: function() { return this.label; }
              };
            }
          });
        });

        // 🔥 Hanya return sebagai recognized jika distance di bawah threshold
        return bestMatch.distance < threshold 
          ? bestMatch 
          : { 
              label: "unknown", 
              distance: bestMatch.distance,
              toString: function() { return this.label; }
            };
      }
    };
  }


  video.addEventListener("play", async () => {
    const labeledDescriptors = await loadLabeledDescriptors();
    
    // 🔥 PERBAIKAN: Naikkan threshold jadi 0.8 dan pastikan unknown di-handle dengan benar
    const faceMatcher = createStrictFaceMatcher(labeledDescriptors, 0.55);

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
        const results = resizedDetections.map(d => faceMatcher.findBestMatch(d.descriptor));

        results.forEach((result, i) => {
          const box = resizedDetections[i].detection.box;
          const landmarks = resizedDetections[i].landmarks;

          const mouth = landmarks.getMouth(); // array titik bibir
          const topLip = mouth[13];  // titik bibir atas tengah
          const bottomLip = mouth[19]; // titik bibir bawah tengah

          // Hitung jarak vertikal bibir
          const mouthOpenDistance = bottomLip.y - topLip.y;
          isMouthOpen = mouthOpenDistance > 15; // threshold bisa diatur sesuai kebutuhan

          if (result.label !== "unknown" && result.distance < 0.5) {
            new faceapi.draw.DrawBox(box, { 
              label: result.label,
              boxColor: isMouthOpen ? 'green' : 'orange' // hijau kalau mulut terbuka
            }).draw(overlay);

            currentDetectedUser = result.label;

            if (isMouthOpen) {
              statusEl.innerText = `👤 ${result.label} - Mulut terbuka ✅`;
            } else {
              statusEl.innerText = `👤 ${result.label} - Harap buka mulut 👄`;
            }
          } else {
            new faceapi.draw.DrawBox(box, { 
              label: "Unknown",
              boxColor: 'red'
            }).draw(overlay);

            currentDetectedUser = null;
            isMouthOpen = false;
            statusEl.innerText = "❌ Wajah tidak dikenali";
          }
        });
      } else {
        currentDetectedUser = null;
        isMouthOpen = false;
        statusEl.innerText = "🔍 Mencari wajah...";
      }


      updateButtonState();
    }, 1000);
  });



  

  async function submitAttendance(type) {
      if (!currentDetectedUser) {
          alert('Wajah belum dikenali.');
          return;
      }

      if (!isInGeozone) {
          alert('Anda berada di luar area yang diizinkan untuk absen.');
          return;
      }

      if (!userLocation) {
          alert('Lokasi Anda tidak dapat ditemukan.');
          return;
      }
      
      const photo = capturePhoto(video);

      try {
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
                  latitude: userLocation.lat,
                  longitude: userLocation.lon,
              })
          });
          
          const data = await response.json();
          
          if (response.ok) {
              alert(data.message);
              // Reset state setelah absen berhasil
              if (type === 'in') {
                  absenMasukBtn.disabled = true;
              } else {
                  absenPulangBtn.disabled = true;
              }
          } else {
              alert('Error: ' + data.message);
          }
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
@endpush
