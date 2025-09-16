<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Karyawan</title>
    
    <!-- Face API JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        #video, #canvas {
            width: 100%;
            max-width: 400px;
            height: 300px;
            border: 2px solid #ddd;
            border-radius: 8px;
        }
        #map {
            height: 300px;
            border-radius: 8px;
        }
        .loading {
            display: none;
        }
        .face-detection-box {
            position: relative;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Sistem Absensi Karyawan</h1>
        
        <!-- User Info -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title" id="userName">Loading...</h5>
                <p class="card-text" id="userDivision">Loading...</p>
                <p class="card-text" id="currentTime"></p>
            </div>
        </div>

        <!-- Today's Attendance Status -->
        <div class="card mb-4" id="attendanceStatus">
            <div class="card-body">
                <h5 class="card-title">Status Absensi Hari Ini</h5>
                <div id="statusContent">
                    <p class="text-muted">Loading...</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Camera Section -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Face Recognition</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="face-detection-box mb-3">
                            <video id="video" autoplay muted></video>
                            <canvas id="canvas" style="display: none;"></canvas>
                        </div>
                        <div id="faceStatus" class="mb-3">
                            <span class="badge bg-secondary">Memuat kamera...</span>
                        </div>
                        <div class="d-grid gap-2">
                            <button class="btn btn-success" id="checkInBtn" onclick="checkIn()" disabled>
                                <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                                Check In
                            </button>
                            <button class="btn btn-warning" id="checkOutBtn" onclick="checkOut()" disabled>
                                <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                                Check Out
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map Section -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Lokasi Anda</h5>
                    </div>
                    <div class="card-body">
                        <div id="map"></div>
                        <div class="mt-3">
                            <p id="locationStatus" class="mb-1">
                                <span class="badge bg-secondary">Mendapatkan lokasi...</span>
                            </p>
                            <p id="locationAddress" class="text-muted small">-</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Global variables
        let video, canvas, ctx;
        let map, userMarker;
        let currentLocation = null;
        let faceDescriptor = null;
        let isModelsLoaded = false;
        let user = null;
        let todayAttendance = null;
        
        const API_BASE = '/api';
        const token = localStorage.getItem('auth_token');

        // Initialize app
        document.addEventListener('DOMContentLoaded', async function() {
            if (!token) {
                window.location.href = '/login.html';
                return;
            }

            await loadUser();
            await loadTodayAttendance();
            await initializeFaceAPI();
            await initializeCamera();
            await initializeMap();
            await getCurrentLocation();
            
            updateTime();
            setInterval(updateTime, 1000);
        });

        // Load user data
        async function loadUser() {
            try {
                const response = await fetch(`${API_BASE}/user`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    user = await response.json();
                    document.getElementById('userName').textContent = user.name;
                    document.getElementById('userDivision').textContent = user.division.name;
                } else {
                    throw new Error('Failed to load user data');
                }
            } catch (error) {
                console.error('Error loading user:', error);
                localStorage.removeItem('auth_token');
                window.location.href = '/login.html';
            }
        }

        // Load today's attendance
        async function loadTodayAttendance() {
            try {
                const response = await fetch(`${API_BASE}/attendance/today`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    const result = await response.json();
                    todayAttendance = result.data;
                    updateAttendanceStatus();
                }
            } catch (error) {
                console.error('Error loading attendance:', error);
            }
        }

        // Update attendance status display
        function updateAttendanceStatus() {
            const statusContent = document.getElementById('statusContent');
            const checkInBtn = document.getElementById('checkInBtn');
            const checkOutBtn = document.getElementById('checkOutBtn');

            if (!todayAttendance) {
                statusContent.innerHTML = '<p class="text-warning">Belum absen hari ini</p>';
                checkInBtn.disabled = false;
                checkOutBtn.disabled = true;
            } else if (todayAttendance.check_in_time && !todayAttendance.check_out_time) {
                statusContent.innerHTML = `
                    <p class="text-success">Sudah Check In: ${new Date(todayAttendance.check_in_time).toLocaleTimeString('id-ID')}</p>
                    <p class="text-warning">Belum Check Out</p>
                `;
                checkInBtn.disabled = true;
                checkOutBtn.disabled = false;
            } else if (todayAttendance.check_in_time && todayAttendance.check_out_time) {
                statusContent.innerHTML = `
                    <p class="text-success">Check In: ${new Date(todayAttendance.check_in_time).toLocaleTimeString('id-ID')}</p>
                    <p class="text-success">Check Out: ${new Date(todayAttendance.check_out_time).toLocaleTimeString('id-ID')}</p>
                    <p class="text-info">Status: ${todayAttendance.status}</p>
                `;
                checkInBtn.disabled = true;
                checkOutBtn.disabled = true;
            }
        }

        // Initialize Face API
        async function initializeFaceAPI() {
            try {
                console.log('Loading Face API models...');
                
                // Load models from CDN
                await faceapi.nets.ssdMobilenetv1.loadFromUri('https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/weights');
                await faceapi.nets.faceRecognitionNet.loadFromUri('https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/weights');
                await faceapi.nets.faceLandmark68Net.loadFromUri('https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/weights');
                
                isModelsLoaded = true;
                console.log('Face API models loaded successfully');
                
                document.getElementById('faceStatus').innerHTML = 
                    '<span class="badge bg-success">Model Face Recognition siap</span>';
                
            } catch (error) {
                console.error('Error loading Face API models:', error);
                document.getElementById('faceStatus').innerHTML = 
                    '<span class="badge bg-danger">Gagal memuat model Face Recognition</span>';
            }
        }

        // Initialize camera
        async function initializeCamera() {
            try {
                video = document.getElementById('video');
                canvas = document.getElementById('canvas');
                ctx = canvas.getContext('2d');

                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        width: { ideal: 640 },
                        height: { ideal: 480 },
                        facingMode: 'user'
                    } 
                });
                
                video.srcObject = stream;
                
                video.addEventListener('loadedmetadata', () => {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                });

                video.addEventListener('play', startFaceDetection);
                
            } catch (error) {
                console.error('Error accessing camera:', error);
                document.getElementById('faceStatus').innerHTML = 
                    '<span class="badge bg-danger">Gagal mengakses kamera</span>';
            }
        }

        // Start face detection
        async function startFaceDetection() {
            if (!isModelsLoaded) return;
            
            setInterval(async () => {
                await detectFace();
            }, 1000);
        }

        // Detect face
        async function detectFace() {
            if (!isModelsLoaded || video.paused || video.ended) return;

            try {
                const detections = await faceapi
                    .detectSingleFace(video, new faceapi.SsdMobilenetv1Options())
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                if (detections) {
                    faceDescriptor = Array.from(detections.descriptor);
                    document.getElementById('faceStatus').innerHTML = 
                        '<span class="badge bg-success">Wajah terdeteksi</span>';
                } else {
                    faceDescriptor = null;
                    document.getElementById('faceStatus').innerHTML = 
                        '<span class="badge bg-warning">Wajah tidak terdeteksi</span>';
                }
                
                updateButtonStates();
                
            } catch (error) {
                console.error('Error in face detection:', error);
                document.getElementById('faceStatus').innerHTML = 
                    '<span class="badge bg-danger">Error deteksi wajah</span>';
            }
        }

        // Update button states
        function updateButtonStates() {
            const checkInBtn = document.getElementById('checkInBtn');
            const checkOutBtn = document.getElementById('checkOutBtn');
            
            const hasValidLocation = currentLocation !== null;
            const hasFaceDescriptor = faceDescriptor !== null;
            const canAttend = hasValidLocation && hasFaceDescriptor;

            if (!todayAttendance) {
                checkInBtn.disabled = !canAttend;
                checkOutBtn.disabled = true;
            } else if (todayAttendance.check_in_time && !todayAttendance.check_out_time) {
                checkInBtn.disabled = true;
                checkOutBtn.disabled = !canAttend;
            } else {
                checkInBtn.disabled = true;
                checkOutBtn.disabled = true;
            }
        }

        // Initialize map
        function initializeMap() {
            map = L.map('map').setView([-7.9666, 112.6326], 13); // Default to Malang

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
        }

        // Get current location
        async function getCurrentLocation() {
            if (!navigator.geolocation) {
                document.getElementById('locationStatus').innerHTML = 
                    '<span class="badge bg-danger">GPS tidak didukung</span>';
                return;
            }

            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    currentLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    // Update map
                    map.setView([currentLocation.lat, currentLocation.lng], 15);
                    
                    if (userMarker) {
                        map.removeLayer(userMarker);
                    }
                    
                    userMarker = L.marker([currentLocation.lat, currentLocation.lng])
                        .addTo(map)
                        .bindPopup('Lokasi Anda')
                        .openPopup();

                    // Get address
                    await getAddressFromCoordinates(currentLocation.lat, currentLocation.lng);
                    
                    document.getElementById('locationStatus').innerHTML = 
                        '<span class="badge bg-success">Lokasi ditemukan</span>';
                    
                    updateButtonStates();
                },
                (error) => {
                    console.error('Error getting location:', error);
                    document.getElementById('locationStatus').innerHTML = 
                        '<span class="badge bg-danger">Gagal mendapatkan lokasi</span>';
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

        // Get address from coordinates
        async function getAddressFromCoordinates(lat, lng) {
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                const data = await response.json();
                
                if (data && data.display_name) {
                    document.getElementById('locationAddress').textContent = data.display_name;
                }
            } catch (error) {
                console.error('Error getting address:', error);
                document.getElementById('locationAddress').textContent = 'Alamat tidak tersedia';
            }
        }

        // Capture photo from video
        function capturePhoto() {
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            return canvas.toDataURL('image/jpeg', 0.8);
        }

        // Check In
        async function checkIn() {
            if (!currentLocation || !faceDescriptor) {
                alert('Pastikan lokasi dan wajah terdeteksi');
                return;
            }

            const button = document.getElementById('checkInBtn');
            const loading = button.querySelector('.loading');
            
            button.disabled = true;
            loading.style.display = 'inline-block';

            try {
                const photo = capturePhoto();
                const address = document.getElementById('locationAddress').textContent;

                const response = await fetch(`${API_BASE}/attendance/check-in`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        lat: currentLocation.lat,
                        lng: currentLocation.lng,
                        address: address,
                        face_descriptor: faceDescriptor,
                        photo: photo
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Check-in berhasil!');
                    await loadTodayAttendance();
                } else {
                    alert('Check-in gagal: ' + result.message);
                }
            } catch (error) {
                console.error('Error during check-in:', error);
                alert('Terjadi kesalahan saat check-in');
            } finally {
                loading.style.display = 'none';
                updateButtonStates();
            }
        }

        // Check Out
        async function checkOut() {
            if (!currentLocation || !faceDescriptor) {
                alert('Pastikan lokasi dan wajah terdeteksi');
                return;
            }

            const button = document.getElementById('checkOutBtn');
            const loading = button.querySelector('.loading');
            
            button.disabled = true;
            loading.style.display = 'inline-block';

            try {
                const photo = capturePhoto();
                const address = document.getElementById('locationAddress').textContent;

                const response = await fetch(`${API_BASE}/attendance/check-out`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        lat: currentLocation.lat,
                        lng: currentLocation.lng,
                        address: address,
                        face_descriptor: faceDescriptor,
                        photo: photo
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Check-out berhasil!');
                    await loadTodayAttendance();
                } else {
                    alert('Check-out gagal: ' + result.message);
                }
            } catch (error) {
                console.error('Error during check-out:', error);
                alert('Terjadi kesalahan saat check-out');
            } finally {
                loading.style.display = 'none';
                updateButtonStates();
            }
        }

        // Update time display
        function updateTime() {
            const now = new Date();
            document.getElementById('currentTime').textContent = 
                `${now.toLocaleDateString('id-ID')} ${now.toLocaleTimeString('id-ID')}`;
        }

        // Handle page unload
        window.addEventListener('beforeunload', function() {
            if (video && video.srcObject) {
                video.srcObject.getTracks().forEach(track => track.stop());
            }
        });
    </script>
</body>
</html>
