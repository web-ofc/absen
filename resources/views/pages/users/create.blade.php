@extends('layouts.master')

@section('content')
<div class="container">
    <h2>Daftar User Baru</h2>

    <form id="userForm" method="POST" action="{{ route('users.store') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="text" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="geozones" class="form-label">Geozone</label>
            <select name="geozones[]" id="geozones" class="form-select" multiple>
                @foreach($geozones as $zone)
                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                @endforeach
            </select>
            <small class="text-muted">Bisa pilih lebih dari satu lokasi</small>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="can_attend_anywhere" id="can_attend_anywhere" class="form-check-input" value="1">
            <label for="can_attend_anywhere" class="form-check-label">Boleh Absen di Mana Saja</label>
        </div>

        <!-- TAMBAHAN: Field untuk can_anytime dan work_schedule -->
        <div class="mb-3 form-check">
            <input type="checkbox" name="can_anytime" id="can_anytime" class="form-check-input" value="1">
            <label for="can_anytime" class="form-check-label">Bisa Absen Kapan Saja</label>
        </div>
        

        <div class="mb-3" id="workScheduleField" style="display: none;">
            <label for="work_schedule_id" class="form-label">Work Schedule</label>
            <select name="work_schedule_id" id="work_schedule_id" class="form-select" required>
                <option value="">Pilih Work Schedule</option>
                @foreach($workSchedules as $schedule)
                    <option value="{{ $schedule->id }}">{{ $schedule->name }}</option>
                @endforeach
            </select>
            <small class="text-muted">Wajib dipilih jika tidak bisa absen kapan saja</small>
        </div>

         {{-- Tambahkan field untuk Roles --}}
        <div class="mb-3">
            <label for="roles" class="form-label">Pilih Roles</label>
            <select name="roles[]" id="roles" class="form-select" multiple required>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                @endforeach
            </select>
            <small class="text-muted">Bisa pilih lebih dari satu role. Gunakan CTRL/CMD untuk multi-select.</small>
        </div>

        <div class="mb-3">
            <label>Pengambilan Foto Wajah</label>
            <div class="border p-3">
                <video id="video" width="320" height="240" autoplay muted></video>
                <canvas id="canvas" width="320" height="240" class="d-none"></canvas>
                <img id="capturedPhoto" width="320" class="d-none"/>
                <div class="mt-2">
                    <button type="button" class="btn btn-primary" onclick="startCamera()">Mulai Kamera</button>
                    <button type="button" class="btn btn-success" onclick="capturePhoto()">Ambil Foto</button>
                </div>
                <small id="debug" class="text-muted d-block mt-2">Debug: Waiting...</small>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<script>
let video = document.getElementById('video');
let canvas = document.getElementById('canvas');
let capturedPhoto = document.getElementById('capturedPhoto');
let debugEl = document.getElementById('debug');
let photoData = null;

// JavaScript untuk toggle work schedule field
document.getElementById('can_anytime').addEventListener('change', function() {
    const workScheduleField = document.getElementById('workScheduleField');
    const workScheduleSelect = document.getElementById('work_schedule_id');
    
    if (this.checked) {
        // Jika bisa absen kapan saja, sembunyikan dan non-required
        workScheduleField.style.display = 'none';
        workScheduleSelect.removeAttribute('required');
    } else {
        // Jika tidak bisa absen kapan saja, tampilkan dan required
        workScheduleField.style.display = 'block';
        workScheduleSelect.setAttribute('required', 'required');
    }
});

function updateDebug(msg) {
    debugEl.innerText = "Debug: " + msg;
}

async function loadModels() {
    updateDebug("Loading face-api models...");
    await faceapi.nets.tinyFaceDetector.loadFromUri('/models');
    await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
    await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
    updateDebug("Face API Models loaded ✅");
}

async function startCamera() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
        updateDebug("Camera started 🎥");
    } catch (err) {
        updateDebug("Camera error: " + err.message);
    }
}

function capturePhoto() {
    let ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    photoData = canvas.toDataURL('image/png');

    capturedPhoto.src = photoData;
    capturedPhoto.classList.remove('d-none');
    updateDebug("Photo captured 📸");
}

async function generateDescriptorFromImage(imgElement) {
    const options = new faceapi.TinyFaceDetectorOptions();
    const detection = await faceapi
        .detectSingleFace(imgElement, options)
        .withFaceLandmarks()
        .withFaceDescriptor();

    if (!detection) {
        updateDebug("No face detected ❌");
        return null;
    }
    updateDebug("Face detected ✅");
    return Array.from(detection.descriptor);
}

document.getElementById('userForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    // Validasi tambahan: pastikan work schedule dipilih jika can_anytime false
    const canAnytime = document.getElementById('can_anytime').checked;
    const workScheduleId = document.getElementById('work_schedule_id').value;
    
    if (!canAnytime && !workScheduleId) {
        alert('Work schedule harus dipilih jika tidak bisa absen kapan saja!');
        return;
    }

    if (!photoData) {
        alert("Silakan ambil foto terlebih dahulu!");
        return;
    }

    const descriptor = await generateDescriptorFromImage(capturedPhoto);
    if (!descriptor) {
        alert("Wajah tidak terdeteksi. Coba lagi dengan pencahayaan lebih baik.");
        return;
    }

    const formData = new FormData(this);
    formData.append('photo', photoData);
    formData.append('face_descriptor', JSON.stringify(descriptor));

    const response = await fetch(this.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    });

    const result = await response.json();
    if (response.ok && result.success) {
        alert(result.message || "Data berhasil disimpan!");
        window.location.href = "{{ route('users.index') }}";
    } else {
        alert("Terjadi kesalahan: " + (result.message || response.status));
    }
});

document.addEventListener("DOMContentLoaded", function() {
    loadModels();
    
    // Trigger change event untuk set initial state
    document.getElementById('can_anytime').dispatchEvent(new Event('change'));
});
</script>
@endpush