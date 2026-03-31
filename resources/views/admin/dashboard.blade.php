@extends('layouts.app')
@section('title', 'Dashboard Admin')

@push('styles')
    <link rel="stylesheet" href="/css/dashboard.css">
    <style>
        .admin-tbl { width: 100%; border-collapse: collapse; margin-top: 20px; text-align: left; }
        .admin-tbl th, .admin-tbl td { padding: 12px 16px; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        .admin-tbl th { background: #f8fafc; color: #475569; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;}
        .admin-tbl tr:hover { background: #f8fafc; }
        .action-flex { display: flex; gap: 8px; align-items: center; }
        .btn-sm { padding: 6px 10px; font-size: 12px; height: 28px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; text-decoration: none; border:none; cursor: pointer; font-family: inherit; font-weight: 600; }
        .btn-blue { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
        .btn-blue:hover { background: #dbeafe; }
        .btn-purp { background: #faf5ff; color: #7c3aed; border: 1px solid #e9d5ff; }
        .btn-purp:hover { background: #f3e8ff; }
    </style>
@endpush

@section('content')
    <div x-data="{
        activeModal: '{{ $errors->any() ? 'addDosen' : '' }}',
        editData: {},
        iframeUrl: '',
        openModal(type) { this.activeModal = type; document.body.style.overflow = 'hidden'; },
        closeModal() { this.activeModal = null; document.body.style.overflow = ''; this.iframeUrl = ''; }
    }">

        <!-- Header -->
        <div class="sec">
            <div class="sec-head" style="margin-bottom: 0;">
                <div class="sec-title">
                    <span class="sec-label">Kelola Data Dosen</span>
                    <span class="sec-count w">{{ $dosenList->count() }}</span>
                </div>
                <button class="btn-add" @click="openModal('addDosen');">
                    <i class="lucide-plus"></i> Tambah Dosen Baru
                </button>
            </div>

            @if(session('success'))
                <div class="status-msg" style="margin-top:20px"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="status-msg err" style="margin-top:20px"><i class="lucide-alert-circle"></i> Terdapat kesalahan input. Silakan cek form.</div>
            @endif

            <table class="admin-tbl">
                <thead>
                    <tr>
                        <th>Nama Dosen</th>
                        <th>NIDN / NIK</th>
                        <th>Kontak & Email</th>
                        <th>Akses Login</th>
                        <th style="width: 250px;">Aksi Admin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dosenList as $d)
                        <tr>
                            <td>
                                <strong>{{ $d->nama }}</strong><br>
                                <span style="color:#64748b; font-size:12px;">{{ $d->jabatan }} | {{ $d->ruangan ?: '...' }}</span>
                            </td>
                            <td>
                                <div style="font-size:13px">{{ $d->nidn ?: '-' }}</div>
                                <div style="font-size:12px; color:#64748b">{{ $d->nik ?: '-' }}</div>
                            </td>
                            <td>
                                <div style="font-size:13px"><i class="lucide-mail" style="font-size:12px; vertical-align:middle;"></i> {{ $d->user->email ?? $d->email }}</div>
                                <div style="font-size:12px; color:#64748b"><i class="lucide-phone" style="font-size:12px; vertical-align:middle;"></i> {{ $d->telepon ?: '-' }}</div>
                            </td>
                            <td>
                                @if($d->user)
                                    <span style="display:inline-block; padding:2px 6px; background:#dcfce7; color:#166534; font-size:11px; border-radius:4px; font-weight:600;">Aktif</span>
                                @else
                                    <span style="display:inline-block; padding:2px 6px; background:#fef2f2; color:#991b1b; font-size:11px; border-radius:4px; font-weight:600;">Belum Ada</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-flex">
                                    <button @click="editData = { id: '{{ $d->id }}', nama: '{{ addslashes($d->nama) }}', jabatan: '{{ $d->jabatan }}', nidn: '{{ $d->nidn }}', nik: '{{ $d->nik }}', telepon: '{{ $d->telepon }}', email: '{{ $d->user->email ?? $d->email }}', ruangan: '{{ addslashes($d->ruangan) }}', tampilkan_nidn: {{ $d->tampilkan_nidn ? 'true' : 'false' }}, tampilkan_nik: {{ $d->tampilkan_nik ? 'true' : 'false' }} }; openModal('editDosen');" class="btn-sm btn-blue">
                                        <i class="lucide-edit-3" style="font-size:14px"></i> Profil & Akun
                                    </button>
                                    <button @click="iframeUrl = '{{ route('admin.dosen.jadwal', $d->id) }}?iframe=1'; openModal('jadwalModal');" class="btn-sm btn-purp">
                                        <i class="lucide-calendar" style="font-size:14px"></i> Kelola Jadwal
                                    </button>
                                    <form method="POST" action="{{ route('admin.dosen.destroy', $d->id) }}" onsubmit="return confirm('Hapus seluruh data Dosen ini? (termasuk jadwal dan akun akan terhapus)')" style="margin:0">
                                        @csrf @method('DELETE')
                                        <button class="btn-del" style="height:28px" title="Hapus"><i class="lucide-trash-2" style="font-size:14px"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 30px; color:#64748b;">Belum ada data Dosen</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── Modal Tambah Dosen ── --}}
        <div x-show="activeModal === 'addDosen'" x-cloak class="modal-bg" @click.self="closeModal()" style="z-index:9999">
            <div class="modal profil-modal" @click.stop>
                <div class="modal-head">
                    <h3><i class="lucide-user-plus" style="color:#2563eb"></i> Tambah Dosen Baru</h3>
                    <button class="modal-close" @click="closeModal()"><i class="lucide-x"></i></button>
                </div>
                <form method="POST" action="{{ route('admin.dosen.store') }}">
                    @csrf
                    <div class="modal-body profil-modal-body">
                        @if($errors->any())
                            <div style="background:#fff1f2; color:#be123c; padding:10px 14px; border-radius:8px; border:1px solid #fecdd3; font-size:12px; margin-bottom:15px">
                                Pendaftaran gagal. Cek kembali isian Anda (email mungkin sudah terpakai).
                            </div>
                        @endif
                        <div class="profil-section-label">Informasi Dasar</div>
                        <div class="pm-fg">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" required maxlength="100">
                        </div>
                        <div class="pm-row">
                            <div class="pm-fg"><label>Jabatan</label>
                                <input type="text" name="jabatan" placeholder="Contoh: Kepala Lab / Staf (Opsional)">
                            </div>
                            <div class="pm-fg"><label>Ruangan</label><input type="text" name="ruangan"></div>
                        </div>

                        <div class="pm-row" style="margin-top:10px">
                            <div class="pm-fg"><label>NIDN</label><input type="text" name="nidn"></div>
                            <div class="pm-fg"><label>NIK</label><input type="text" name="nik"></div>
                        </div>

                        <div class="profil-section-label" style="margin-top:16px"><i class="lucide-lock"></i> Akses Login & Kontak</div>
                        <div class="pm-row">
                            <div class="pm-fg">
                                <label>Email Login</label>
                                <input type="email" name="email" value="{{ old('email') }}" required>
                                @error('email') <div style="color:#be123c; font-size:11px; margin-top:4px">{{ $message }}</div> @enderror
                            </div>
                            <div class="pm-fg"><label>Password Akun</label><input type="password" name="password" required minlength="6"></div>
                        </div>
                        <div class="pm-fg" style="margin-top:10px"><label>Nomor WhatsApp</label><input type="tel" name="telepon"></div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn-cancel" @click="closeModal()">Batal</button>
                        <button type="submit" class="btn btn-dark"><i class="lucide-check" style="font-size:12px"></i> Simpan Dosen</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Modal Edit Dosen ── --}}
        <div x-show="activeModal === 'editDosen'" x-cloak class="modal-bg" @click.self="closeModal()" style="z-index:9999">
            <div class="modal profil-modal" @click.stop>
                <div class="modal-head">
                    <h3><i class="lucide-edit" style="color:#2563eb"></i> Edit Profil & Akun Dosen</h3>
                    <button class="modal-close" @click="closeModal()"><i class="lucide-x"></i></button>
                </div>
                <form method="POST" :action="'/admin/dosen/' + editData.id">
                    @csrf @method('PUT')
                    <div class="modal-body profil-modal-body" style="max-height: 70vh; overflow-y:auto">
                        <div class="profil-section-label">Informasi Dasar</div>
                        <div class="pm-fg"><label>Nama Lengkap</label><input type="text" name="nama" required x-model="editData.nama"></div>
                        <div class="pm-row">
                            <div class="pm-fg"><label>Jabatan</label><input type="text" name="jabatan" x-model="editData.jabatan" placeholder="Opsional"></div>
                            <div class="pm-fg"><label>Ruangan</label><input type="text" name="ruangan" x-model="editData.ruangan"></div>
                        </div>
                        <div class="pm-row" style="margin-top:10px">
                            <div class="pm-fg"><label>NIDN</label><input type="text" name="nidn" x-model="editData.nidn">
                                <label class="toggle-label" style="margin-top:6px"><input type="checkbox" name="tampilkan_nidn" value="1" x-model="editData.tampilkan_nidn"><span class="toggle-txt">Tampilkan NIDN di publik</span></label>
                            </div>
                            <div class="pm-fg"><label>NIK</label><input type="text" name="nik" x-model="editData.nik">
                                <label class="toggle-label" style="margin-top:6px"><input type="checkbox" name="tampilkan_nik" value="1" x-model="editData.tampilkan_nik"><span class="toggle-txt">Tampilkan NIK di publik</span></label>
                            </div>
                        </div>

                        <div class="profil-section-label" style="margin-top:16px"><i class="lucide-lock"></i> Akses Login & Kontak</div>
                        <div class="pm-row">
                            <div class="pm-fg"><label>Email Login</label><input type="email" name="email" required x-model="editData.email"></div>
                            <div class="pm-fg"><label>Nomor WhatsApp</label><input type="tel" name="telepon" x-model="editData.telepon"></div>
                        </div>
                        <div class="pm-fg" style="margin-top:10px; background:#f8fafc; padding:12px; border-radius:6px; border:1px solid #e2e8f0;">
                            <label><strong style="color:#ef4444">Reset Password</strong> (Isi hanya jika ingin mengganti password dosen ini)</label>
                            <input type="password" name="password" minlength="6" placeholder="Password baru">
                        </div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn-cancel" @click="closeModal()">Batal</button>
                        <button type="submit" class="btn btn-dark"><i class="lucide-check" style="font-size:12px"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Modal Kelola Jadwal (Iframe) ── --}}
        <div x-show="activeModal === 'jadwalModal'" x-cloak class="modal-bg" @click.self="closeModal()" style="z-index:9999; padding: 20px;">
            <div class="modal" style="width: 100%; max-width: 900px; height: 90vh; display: flex; flex-direction: column; overflow: hidden; padding: 0;">
                <div class="modal-head" style="padding: 16px 20px; border-bottom: 1px solid #e5e7eb; background: #fff;">
                    <h3 style="margin: 0; display: flex; align-items: center; gap: 8px; font-size: 16px;"><i class="lucide-calendar-days" style="color:#7c3aed"></i> Kelola Jadwal</h3>
                    <button class="modal-close" @click="closeModal()"><i class="lucide-x"></i></button>
                </div>
                <div style="flex: 1; background: #f4f2ef; overflow: hidden; position: relative;">
                    <template x-if="iframeUrl">
                        <iframe :src="iframeUrl" style="width: 100%; height: 100%; border: none; display: block;"></iframe>
                    </template>
                </div>
            </div>
        </div>

    </div>
@endsection
