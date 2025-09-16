{{-- resources/views/users/edit.blade.php --}}
@extends('layouts.master')

@section('title', 'Edit User - ' . $user->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-user-edit me-2"></i>Edit User - {{ $user->name }}
                </h4>
            </div>
            <div class="card-body">
                <form id="editUserForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ $user->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="nip" class="form-label">NIP</label>
                                <input type="text" class="form-control" id="nip" name="nip" 
                                       value="{{ $user->nip }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ $user->email }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="position" class="form-label">Jabatan</label>
                                <input type="text" class="form-control" id="position" name="position" 
                                       value="{{ $user->position }}">
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                           {{ $user->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        User Aktif
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Foto Saat Ini</label>
                                <div class="border rounded p-3 text-center">
                                    @if($user->photo)
                                        <img src="{{ asset('storage/' . $user->photo) }}" 
                                             alt="{{ $user->name }}" 
                                             class="img-fluid rounded mb-2"
                                             style="max-width: 200px; max-height: 200px; object-fit: cover;">
                                        <div class="mt-2">
                                            @if($user->face_descriptor)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Data wajah tersedia
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Data wajah tidak tersedia
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="bg-secondary rounded d-flex align-items-center justify-content-center" 
                                             style="width: 200px; height: 200px; margin: 0 auto;">
                                            <i class="fas fa-user fa-3x text-white"></i>
                                        </div>
                                        <p class="text-muted mt-2">Tidak ada foto</p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Update Foto Wajah (Opsional)</label>
                                <div class="border rounded p-3 text-center">
                                    <div id="videoContainer" style="display: none;">
                                        <video id="video" width="300" height="225" autoplay muted></video>
                                        <canvas id="canvas" style="display: none;"></canvas>
                                    </div>
                                    <div id="photoPreview" style="display: none;">
                                        <img id="capturedPhoto" width="300" height="225" class="rounded">
                                        <div class="mt-2">
                                            <span id="faceStatus" class="badge bg-secondary">Mendeteksi wajah...</span>
                                        </div>
                                    </div>
                                    <div id="startCamera" class="text-center">
                                        <i class="fas fa-camera fa-2x text-muted mb-2"></i>
                                        <p class="text-muted small">Klik untuk mengupdate foto wajah</p>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="startCamera()">
                                            <i class="fas fa-camera me-2"></i>Ambil Foto Baru
                                        </button>
                                    </div>
                                </div>
                                <div id="cameraControls" class="mt-2 text-center" style="display: none;">
                                    <button type="button" class="btn btn-success btn-sm me-2" onclick="capturePhoto()">
                                        <i class="fas fa-camera me-1"></i>Ambil
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="cancelCamera()">
                                        <i class="fas fa-times me-1"></i>Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-secondary me-2">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let video, canvas, ctx;
let faceDescriptor = null;
let capturedImageData = null;

// Load face-api models
Promise.all([
    faceapi.nets.tinyFaceDetector.loadFromUri('https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/model/'),
    faceapi.nets.faceLandmark68Net.loadFromUri('https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/model/'),
    faceapi.nets.faceRecognitionNet.loadFromUri('https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/model/')
]).then(() => {
    console.log('Face-api models loaded');
});

async function startCamera() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ 
            video: { width: 300, height: 225 } 
        });
        
        video = document.getElementById('video');
        canvas = document.getElementById('canvas');
        ctx = canvas.getContext('2d');
        
        video.srcObject = stream;
        
        document.getElementById('startCamera').style.display = 'none';
        document.getElementById('videoContainer').style.display = 'block';
        document.getElementById('cameraControls').style.display = 'block';
        
    } catch (err) {
        alert('Error accessing camera: ' + err.message);
    }
}

async function capturePhoto() {
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0);
    
    // Convert to base64
    capturedImageData = canvas.toDataURL('image/jpeg', 0.8);
    
    // Show captured photo
    document.getElementById('capturedPhoto').src = capturedImageData;
    document.getElementById('videoContainer').style.display = 'none';
    document.getElementById('photoPreview').style.display = 'block';
    document.getElementById('cameraControls').innerHTML = `
        <button type="button" class="btn btn-success btn-sm me-2" onclick="confirmPhoto()">
            <i class="fas fa-check me-1"></i>Konfirmasi
        </button>
        <button type="button" class="btn btn-secondary btn-sm" onclick="retakePhoto()">
            <i class="fas fa-redo me-1"></i>Ambil Ulang
        </button>
    `;
    
    // Stop video stream
    const stream = video.srcObject;
    const tracks = stream.getTracks();
    tracks.forEach(track => track.stop());
    
    // Detect face and get descriptor
    await detectAndGetDescriptor();
}

async function detectAndGetDescriptor() {
    try {
        const img = document.getElementById('capturedPhoto');
        const detection = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks()
            .withFaceDescriptor();
        
        if (detection) {
            faceDescriptor = detection.descriptor;
            document.getElementById('faceStatus').className = 'badge bg-success';
            document.getElementById('faceStatus').textContent = 'Wajah terdeteksi! ✓';
        } else {
            faceDescriptor = null;
            document.getElementById('faceStatus').className = 'badge bg-danger';
            document.getElementById('faceStatus').textContent = 'Wajah tidak terdeteksi!';
            alert('Wajah tidak terdeteksi! Silakan ambil foto ulang dengan pencahayaan yang baik.');
        }
    } catch (error) {
        console.error('Error detecting face:', error);
        faceDescriptor = null;
        document.getElementById('faceStatus').className = 'badge bg-danger';
        document.getElementById('faceStatus').textContent = 'Error deteksi wajah!';
        alert('Terjadi kesalahan saat mendeteksi wajah. Silakan coba lagi.');
    }
}

function confirmPhoto() {
    if (!faceDescriptor) {
        alert('Wajah tidak terdeteksi! Silakan ambil foto ulang.');
        return;
    }
    
    document.getElementById('photoPreview').style.display = 'none';
    document.getElementById('startCamera').innerHTML = `
        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
        <p class="text-success small">Foto baru siap diupdate</p>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="retakePhoto()">
            <i class="fas fa-camera me-2"></i>Ambil Foto Lain
        </button>
    `;
    document.getElementById('startCamera').style.display = 'block';
    document.getElementById('cameraControls').style.display = 'none';
}

function retakePhoto() {
    faceDescriptor = null;
    capturedImageData = null;
    document.getElementById('photoPreview').style.display = 'none';
    document.getElementById('startCamera').innerHTML = `
        <i class="fas fa-camera fa-2x text-muted mb-2"></i>
        <p class="text-muted small">Klik untuk mengupdate foto wajah</p>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="startCamera()">
            <i class="fas fa-camera me-2"></i>Ambil Foto Baru
        </button>
    `;
    document.getElementById('startCamera').style.display = 'block';
    document.getElementById('cameraControls').style.display = 'none';
}

function cancelCamera() {
    // Stop video stream
    if (video && video.srcObject) {
        const stream = video.srcObject;
        const tracks = stream.getTracks();
        tracks.forEach(track => track.stop());
    }
    
    faceDescriptor = null;
    capturedImageData = null;
    document.getElementById('videoContainer').style.display = 'none';
    document.getElementById('photoPreview').style.display = 'none';
    document.getElementById('startCamera').style.display = 'block';
    document.getElementById('cameraControls').style.display = 'none';
}

// Handle form submission
document.getElementById('editUserForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('_method', 'PUT');
    formData.append('name', document.getElementById('name').value);
    formData.append('nip', document.getElementById('nip').value);
    formData.append('email', document.getElementById('email').value);
    formData.append('position', document.getElementById('position').value);
    
    if (document.getElementById('is_active').checked) {
        formData.append('is_active', '1');
    }
    
    // Add new photo and face descriptor if captured
    if (capturedImageData && faceDescriptor) {
        formData.append('photo', capturedImageData);
        formData.append('face_descriptor', JSON.stringify(Array.from(faceDescriptor)));
    }
    
    try {
        const response = await fetch('{{ route("users.update", $user->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            window.location.href = '{{ route("users.show", $user->id) }}';
        } else {
            alert('Terjadi kesalahan: ' + (result.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengupdate data!');
    }
});
</script>
@endpush