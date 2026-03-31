@extends('layouts.app')
@section('title', 'Dashboard Dosen')

@push('styles')
    <link rel="stylesheet" href="/css/dashboard.css">
    <style>
        .btn-del svg, .btn-edit svg { pointer-events: none !important; }
    </style>
@endpush

@section('content')

    @php $dosen = $dosen ?? null; @endphp

    {{-- ═══ HERO + MODAL EDIT PROFIL ═══ --}}
    <div x-data="{
            showProfil: {{ $errors->any() || session('success_profil') ? 'true' : 'false' }},
            openProfil()  { this.showProfil = true;  document.body.style.overflow = 'hidden'; },
            closeProfil() { this.showProfil = false; document.body.style.overflow = ''; }
        }">

        {{-- ── Hero Banner ── --}}
        <div class="dash-hero">
            <div class="hero-left">
                <div>
                    <h2>{{ $dosen->nama ?? 'Dosen' }}</h2>
                    <div class="info">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/></svg>
                            {{ $dosen->jabatan ?? '' }}
                        </span>
                        <span class="sp"></span>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="9" y2="9"/><line x1="4" x2="20" y1="15" y2="15"/><line x1="10" x2="8" y1="3" y2="21"/><line x1="16" x2="14" y1="3" y2="21"/></svg>
                            NIDN: {{ $dosen->nidn ?? '' }}
                        </span>
                        @if(($dosen->tampilkan_nik ?? false) && $dosen->nik)
                        <span class="sp"></span>
                        <span>NIK: {{ $dosen->nik }}</span>
                        @endif
                        @if($dosen->ruangan)
                        <span class="sp"></span>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            {{ $dosen->ruangan }}
                        </span>
                        @endif
                        <span class="sp"></span>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                            {{ now()->translatedFormat('l, d F Y') }}
                        </span>
                    </div>
                </div>
            </div>
            <button class="hero-edit-btn" @click="openProfil()" title="Edit Profil">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                Edit Profil
            </button>
        </div>

        {{-- ── Modal Edit Profil ── --}}
        <div x-show="showProfil" x-cloak class="modal-bg profil-modal-bg" @click.self="closeProfil()"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-1"
            x-transition:leave-start="opacity-1" x-transition:leave-end="opacity-0">

            <div class="modal profil-modal" @click.stop>
                {{-- Header --}}
                <div class="modal-head">
                    <h3>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Edit Profil Dosen
                    </h3>
                    <button class="modal-close" @click="closeProfil()" title="Tutup">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                {{-- Form --}}
                <form method="POST" action="{{ route('dosen.updateProfil') }}">
                    @csrf
                    <div class="modal-body profil-modal-body">

                        {{-- ── Informasi Dasar ── --}}
                        <div class="profil-section-label">Informasi Dasar</div>

                        <div class="pm-fg">
                            <label for="pm_nama">Nama Lengkap</label>
                            <input type="text" id="pm_nama" name="nama" value="{{ old('nama', $dosen->nama) }}" required maxlength="100" placeholder="Nama lengkap dosen">
                            @error('nama')<span class="profil-err">{{ $message }}</span>@enderror
                        </div>

                        <div class="pm-row">
                            <div class="pm-fg">
                                <label for="pm_jabatan">Jabatan</label>
                                <input type="text" id="pm_jabatan" name="jabatan" value="{{ old('jabatan', $dosen->jabatan) }}" required maxlength="50" placeholder="Contoh: Kepala Lab / Dosen">
                                @error('jabatan')<span class="profil-err">{{ $message }}</span>@enderror
                            </div>
                            <div class="pm-fg">
                                <label for="pm_ruangan">Lokasi / Ruangan</label>
                                <input type="text" id="pm_ruangan" name="ruangan" value="{{ old('ruangan', $dosen->ruangan) }}" maxlength="100" placeholder="Ruang Lab Komputer Lt. 2">
                                @error('ruangan')<span class="profil-err">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        {{-- ── Nomor Identitas ── --}}
                        <div class="profil-section-label" style="margin-top:16px">Nomor Identitas</div>
                        <p class="profil-hint">Pilih mana yang ditampilkan di halaman publik.</p>

                        <div class="pm-row">
                            <div class="pm-fg">
                                <label for="pm_nidn">NIDN</label>
                                <input type="text" id="pm_nidn" name="nidn" value="{{ old('nidn', $dosen->nidn) }}" maxlength="20" placeholder="Nomor Induk Dosen Nasional">
                                @error('nidn')<span class="profil-err">{{ $message }}</span>@enderror
                                <label class="toggle-label" style="margin-top:8px">
                                    <input type="checkbox" name="tampilkan_nidn" value="1" {{ ($dosen->tampilkan_nidn ?? true) ? 'checked' : '' }}>
                                    <span class="toggle-txt">Tampilkan di halaman publik</span>
                                </label>
                            </div>
                            <div class="pm-fg">
                                <label for="pm_nik">NIK <span class="profil-opt">(opsional)</span></label>
                                <input type="text" id="pm_nik" name="nik" value="{{ old('nik', $dosen->nik) }}" maxlength="20" placeholder="Nomor Induk Kepegawaian">
                                @error('nik')<span class="profil-err">{{ $message }}</span>@enderror
                                <label class="toggle-label" style="margin-top:8px">
                                    <input type="checkbox" name="tampilkan_nik" value="1" {{ ($dosen->tampilkan_nik ?? false) ? 'checked' : '' }}>
                                    <span class="toggle-txt">Tampilkan di halaman publik</span>
                                </label>
                            </div>
                        </div>

                        {{-- ── Kontak ── --}}
                        <div class="profil-section-label" style="margin-top:16px">Kontak</div>

                        <div class="pm-row">
                            <div class="pm-fg">
                                <label for="pm_telepon">Nomor WhatsApp</label>
                                <input type="tel" id="pm_telepon" name="telepon" value="{{ old('telepon', $dosen->telepon) }}" maxlength="20" placeholder="Contoh: 08123456789">
                                <span class="profil-caption">Tombol WA di halaman publik</span>
                                @error('telepon')<span class="profil-err">{{ $message }}</span>@enderror
                            </div>
                            <div class="pm-fg">
                                <label for="pm_email">Email Akun Login</label>
                                <input type="email" id="pm_email" name="email" value="{{ old('email', $dosen->user?->email ?? $dosen->email) }}" required maxlength="100" placeholder="email@example.com">
                                <span class="profil-caption">Digunakan untuk login dashboard</span>
                                @error('email')<span class="profil-err">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        {{-- ── Ganti Password ── --}}
                        <div class="profil-section-label" style="margin-top:16px">
                            Ganti Password <span class="profil-opt">(kosongkan jika tidak ingin ganti)</span>
                        </div>
                        <div class="pm-row">
                            <div class="pm-fg">
                                <label for="pm_password">Password Baru</label>
                                <input type="password" id="pm_password" name="password" minlength="6" placeholder="Min. 6 karakter" autocomplete="new-password">
                                @error('password')<span class="profil-err">{{ $message }}</span>@enderror
                            </div>
                            <div class="pm-fg">
                                <label for="pm_password_confirmation">Konfirmasi Password</label>
                                <input type="password" id="pm_password_confirmation" name="password_confirmation" placeholder="Ulangi password baru" autocomplete="new-password">
                            </div>
                        </div>

                        {{-- Messages --}}
                        @if(session('success_profil'))
                            <div class="status-msg" style="margin-top:8px">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                {{ session('success_profil') }}
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="status-msg err" style="margin-top:8px">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                                {{ $errors->first() }}
                            </div>
                        @endif

                    </div>{{-- /modal-body --}}

                    <div class="modal-foot">
                        <button type="button" class="btn-cancel" @click="closeProfil()">Batal</button>
                        <button type="submit" class="btn btn-dark">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>{{-- END profil x-data --}}

    <!-- STATUS -->
    <div class="status-box" x-data="{ mode: '{{ $dosen->status_mode ?? 'otomatis' }}' }">
        <h3><i class="lucide-radio"></i> Status Keberadaan Saat Ini</h3>

        <!-- Mode Toggle -->
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap">
            <span style="font-size:12px;color:#57534e;font-weight:600">Mode:</span>
            <form method="POST" action="{{ route('dosen.updateStatus') }}" style="display:flex;gap:6px;align-items:center">
                @csrf
                <input type="hidden" name="update_mode" value="1">
                <button type="submit" name="status_mode" value="otomatis"
                    class="mode-btn {{ ($dosen->status_mode ?? 'otomatis') === 'otomatis' ? 'active' : '' }}">
                    <i class="lucide-zap" style="font-size:12px"></i> Otomatis
                </button>
                <button type="submit" name="status_mode" value="manual"
                    class="mode-btn {{ ($dosen->status_mode ?? 'otomatis') === 'manual' ? 'active' : '' }}">
                    <i class="lucide-hand" style="font-size:12px"></i> Manual
                </button>
            </form>
        </div>

        @if(($dosen->status_mode ?? 'otomatis') === 'otomatis')
            <div class="mode-info auto">
                <i class="lucide-zap" style="font-size:14px"></i>
                <div>
                    <strong>Mode Otomatis Aktif</strong>
                    <p>Status diperbarui otomatis berdasarkan jadwal. Jam kerja: 08:00 – 17:00 WITA. Di luar jam kerja = Tidak
                        Di Ruangan.</p>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;margin-top:12px">
                <span style="font-size:12px;color:#57534e;font-weight:600">Status saat ini:</span>
                <span class="bdg-status {{ ($dosen->status ?? '') === 'Di Ruangan' ? 'ok' : 'away' }}">
                    {{ $dosen->status ?? 'Di Ruangan' }}
                </span>
            </div>
        @else
            <div class="mode-info manual">
                <i class="lucide-hand" style="font-size:14px"></i>
                <div>
                    <strong>Mode Manual Aktif</strong>
                    <p>Anda mengatur status secara manual. Status tidak akan berubah otomatis berdasarkan jadwal.</p>
                </div>
            </div>
            <form method="POST" action="{{ route('dosen.updateStatus') }}" class="status-form" style="margin-top:12px">
                @csrf
                <select name="status">
                    <option value="Di Ruangan" {{ ($dosen->status ?? '') === 'Di Ruangan' ? 'selected' : '' }}>Di Ruangan</option>
                    <option value="Tidak Di Ruangan" {{ ($dosen->status ?? '') === 'Tidak Di Ruangan' ? 'selected' : '' }}>Tidak
                        Di Ruangan</option>
                </select>
                <button type="submit" class="btn btn-dark"><i class="lucide-refresh-cw" style="font-size:12px"></i>
                    Perbarui</button>
            </form>
        @endif

        @if(session('success'))
            <div class="status-msg" style="margin-top:10px"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
        @endif
    </div>




    {{--
    ═══════════════════════════════════════════════════════════════
    GLOBAL MODAL MANAGER — x-data diletakkan di wrapper terluar
    sehingga HANYA SATU modal yang dapat terbuka di seluruh page.
    openModal(type) menutup modal lama dan membuka modal baru.
    ═══════════════════════════════════════════════════════════════
    --}}
    <div x-data="{
            activeModal: null,
            editData: {},
            editFullday: true,
            deleteUrl: '',
            openModal(type) { this.activeModal = type; },
            closeModal()   { this.activeModal = null; }
        }">

        <!-- ═══ JADWAL MINGGUAN ═══ -->
        <div class="sec">
            <div class="sec-head">
                <div class="sec-title">
                    <span class="sec-label">Jadwal Mingguan</span>
                    <span class="sec-count w">{{ $jadwalMingguan->count() }}</span>
                </div>
                <button class="btn-add" @click="openModal('mingguanAdd'); addRipple($event, $el)">
                    <i class="lucide-plus"></i> Tambah
                </button>
            </div>
            <div class="sec-body">
                @if($jadwalMingguan->count() > 0)
                    <div class="table-responsive">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th>Waktu</th>
                                <th>Kegiatan</th>
                                <th>Mata Kuliah</th>
                                <th>Ruangan</th>
                                <th>Keterangan</th>
                                <th style="width:60px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwalMingguan->groupBy('hari') as $hari => $items)
                                @foreach($items as $jIdx => $j)
                                    <tr>
                                        @if($jIdx === 0)
                                        <td class="dy" rowspan="{{ $items->count() }}">{{ $hari }}</td>@endif
                                        <td class="tm">{{ substr($j->jam_mulai, 0, 5) }} – {{ substr($j->jam_selesai, 0, 5) }}</td>
                                        <td>{{ $j->kegiatan }}</td>
                                        <td class="sb">{{ $j->mata_kuliah ?: '—' }}</td>
                                        <td class="rm">{{ $j->ruangan ?: '—' }}</td>
                                        <td class="rm" style="color:#78716c; font-size:11.5px">{{ $j->keterangan ?: '—' }}</td>
                                        <td>
                                            <div class="act-btns">
                                                <button type="button" class="btn-edit"
                                                    @click="editData = { id: {{ $j->id }}, hari: '{{ $j->hari }}', jam_mulai: '{{ substr($j->jam_mulai, 0, 5) }}', jam_selesai: '{{ substr($j->jam_selesai, 0, 5) }}', kegiatan: '{{ $j->kegiatan }}', mata_kuliah: '{{ addslashes($j->mata_kuliah) }}', ruangan: '{{ addslashes($j->ruangan) }}', keterangan: '{{ addslashes($j->keterangan) }}' }; openModal('mingguanEdit')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                                        <path d="m15 5 4 4" />
                                                    </svg>
                                                </button>
                                                <button type="button" class="btn-del" title="Hapus" @click="deleteUrl = '{{ route('jadwal.mingguan.destroy', $j->id) }}'; openModal('deleteConfirm')">
                                                    <svg style="pointer-events:none;" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path style="pointer-events:none;" d="M3 6h18" /><path style="pointer-events:none;" d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" /><path style="pointer-events:none;" d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" /></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                @else
                    <div class="sec-empty">Belum ada jadwal mingguan</div>
                @endif
            </div>
        </div>

        <!-- ═══ JADWAL AKAN DATANG ═══ -->
        <div class="sec">
            <div class="sec-head">
                <div class="sec-title">
                    <span class="sec-label">Jadwal Akan Datang</span>
                    <span class="sec-count u">{{ $jadwalAkanDatang->count() }}</span>
                </div>
                <div style="display:flex; gap:8px">
                    <button type="button" @click="openModal('riwayatAkanDatang'); addRipple($event, $el)" style="position:relative; overflow:hidden; background:#fff; border:1px solid #e2e8f0; color:#475569; border-radius:10px; font-weight:600; font-size:13.5px; padding:0 14px; cursor:pointer; display:flex; align-items:center; gap:5px; box-shadow:0 1px 2px rgba(0,0,0,0.05); height:36px; transition:all 0.2s">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/></svg>
                        Riwayat
                    </button>
                    <button class="btn-add" @click="openModal('akanDatangAdd'); addRipple($event, $el)">
                        <i class="lucide-plus"></i> Tambah
                    </button>
                </div>
            </div>
            <div class="sec-body">
                @if($jadwalAkanDatang->count() > 0)
                    @foreach($jadwalAkanDatang as $j)
                        <div style="border:1px solid #bae6fd; border-radius:12px; padding:14px; margin-bottom:12px; background:#f0f9ff; display:flex; justify-content:space-between; align-items:flex-start">
                            <div style="display:flex; flex-direction:column; gap:6px; line-height:1.5; color:#0f172a">
                                <div style="font-weight:700; font-size:14px; color:#0369a1; display:flex; align-items:center; gap:6px">
                                    <i class="lucide-calendar-clock" style="font-size:16px"></i> Agenda Terencana
                                </div>
                                <div style="font-size:13px; color:#334155; margin-top:4px; display:flex; flex-direction:column; gap:3px;">
                                    <div><span style="color:#64748b">Agenda/Halangan:</span> <strong>{{ $j->judul }}</strong></div>
                                    <div>
                                        <span style="color:#64748b">Waktu:</span> <strong>{{ $j->tanggal_mulai->translatedFormat('l, d F Y') }}</strong>@if($j->tanggal_mulai != $j->tanggal_selesai) s/d <strong>{{ $j->tanggal_selesai->translatedFormat('l, d F Y') }}</strong>@endif
                                        &bull; 
                                        @if($j->is_fullday) <strong style="color:#475569">Seharian Penuh</strong> @else <strong>{{ substr($j->jam_mulai, 0, 5) }} s/d {{ substr($j->jam_selesai, 0, 5) }}</strong> @endif
                                    </div>
                                </div>
                                @if($j->keterangan)
                                    <div style="font-size:12.5px; color:#64748b; font-style:italic; border-top:1px dashed #bae6fd; padding-top:6px; margin-top:4px;">
                                        Keterangan: {{ $j->keterangan }}
                                    </div>
                                @endif
                            </div>
                            <div class="act-btns">
                                <button type="button" class="btn-edit"
                                    @click="editData = { id: {{ $j->id }}, judul: '{{ addslashes($j->judul) }}', tanggal_mulai: '{{ $j->tanggal_mulai->format('Y-m-d') }}', tanggal_selesai: '{{ $j->tanggal_selesai->format('Y-m-d') }}', jam_mulai: '{{ $j->jam_mulai ? substr($j->jam_mulai, 0, 5) : '' }}', jam_selesai: '{{ $j->jam_selesai ? substr($j->jam_selesai, 0, 5) : '' }}', keterangan: '{{ addslashes($j->keterangan) }}' }; editFullday = {{ $j->is_fullday ? 'true' : 'false' }}; openModal('akanDatangEdit')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                        <path d="m15 5 4 4" />
                                    </svg>
                                </button>
                                <button type="button" class="btn-del" title="Hapus" @click="deleteUrl = '{{ route('jadwal.akan-datang.destroy', $j->id) }}'; openModal('deleteConfirm')">
                                    <svg style="pointer-events:none;" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path style="pointer-events:none;" d="M3 6h18" /><path style="pointer-events:none;" d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" /><path style="pointer-events:none;" d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" /></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="sec-empty">Belum ada jadwal akan datang</div>
                @endif
            </div>
        </div>

        <!-- ═══ JADWAL DADAKAN ═══ -->
        <div class="sec">
            <div class="sec-head">
                <div class="sec-title">
                    <span class="sec-label">Jadwal Dadakan</span>
                    <span class="sec-count e">{{ $jadwalDadakan->count() }}</span>
                </div>
                <div style="display:flex; gap:8px">
                    <button type="button" @click="openModal('riwayatDadakan'); addRipple($event, $el)" style="position:relative; overflow:hidden; background:#fff; border:1px solid #e2e8f0; color:#475569; border-radius:10px; font-weight:600; font-size:13.5px; padding:0 14px; cursor:pointer; display:flex; align-items:center; gap:5px; box-shadow:0 1px 2px rgba(0,0,0,0.05); height:36px; transition:all 0.2s">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/></svg>
                        Riwayat
                    </button>
                    <button class="btn-add" @click="openModal('dadakanAdd'); addRipple($event, $el)">
                        <i class="lucide-plus"></i> Tambah
                    </button>
                </div>
            </div>
            <div class="sec-body">
                @if($jadwalDadakan->count() > 0)
                    @foreach($jadwalDadakan as $j)
                        <div style="border:1px solid #fecdd3; border-radius:12px; padding:14px; margin-bottom:12px; background:#fff1f2; display:flex; justify-content:space-between; align-items:flex-start">
                            <div style="display:flex; flex-direction:column; gap:6px; line-height:1.5; color:#0f172a">
                                <div style="font-weight:700; font-size:14px; color:#be123c; display:flex; align-items:center; gap:6px">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3" /><path d="M12 9v4" /><path d="M12 17h.01" /></svg> Pemberitahuan Khusus
                                </div>
                                <div style="font-size:13px; color:#334155; margin-top:4px; display:flex; flex-direction:column; gap:3px;">
                                    <div><span style="color:#64748b">Agenda/Halangan:</span> <strong>{{ $j->judul }}</strong></div>
                                    <div>
                                        <span style="color:#64748b">Waktu:</span> <strong>{{ $j->tanggal_mulai->translatedFormat('l, d F Y') }}</strong> 
                                        &bull; 
                                        @if($j->is_fullday) <strong style="color:#475569">Seharian Penuh</strong> @else <strong>{{ substr($j->jam_mulai, 0, 5) }} s/d {{ substr($j->jam_selesai, 0, 5) }}</strong> @endif
                                    </div>
                                </div>
                                @if($j->keterangan)
                                    <div style="font-size:12.5px; color:#64748b; font-style:italic; border-top:1px dashed #fecdd3; padding-top:6px; margin-top:4px;">
                                        Keterangan: {{ $j->keterangan }}
                                    </div>
                                @endif
                            </div>
                            <div class="act-btns">
                                <button type="button" class="btn-edit"
                                    @click="editData = { id: {{ $j->id }}, judul: '{{ addslashes($j->judul) }}', jam_selesai: '{{ $j->jam_selesai ? substr($j->jam_selesai, 0, 5) : '' }}', keterangan: '{{ addslashes($j->keterangan) }}' }; editFullday = {{ $j->is_fullday ? 'true' : 'false' }}; openModal('dadakanEdit')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                        <path d="m15 5 4 4" />
                                    </svg>
                                </button>
                                <button type="button" class="btn-del" title="Hapus" @click="deleteUrl = '{{ route('jadwal.dadakan.destroy', $j->id) }}'; openModal('deleteConfirm')">
                                    <svg style="pointer-events:none;" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path style="pointer-events:none;" d="M3 6h18" /><path style="pointer-events:none;" d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" /><path style="pointer-events:none;" d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" /></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="sec-empty">Belum ada jadwal dadakan hari ini</div>
                @endif
            </div>
        </div>

        {{-- ════════════════════════════════════════════
        SEMUA MODAL ada di sini, dikelola oleh
        activeModal pada x-data parent di atas.
        Hanya 1 yang tampil pada waktu bersamaan.
        ════════════════════════════════════════════ --}}

        <!-- ── Modal Tambah Mingguan ── -->
        <div x-show="activeModal === 'mingguanAdd'" x-cloak class="modal-bg" @click.self="closeModal()">
            <div class="modal" @click.stop>
                <div class="modal-head">
                    <h3><i class="lucide-calendar-range" style="color:#2563eb"></i> Tambah Jadwal Mingguan</h3>
                    <button class="modal-close" @click="closeModal()"><i class="lucide-x"></i></button>
                </div>
                <form method="POST" action="/jadwal-mingguan">
                    @csrf
                    <div class="modal-body">
                        <div class="fg">
                            <label>Hari</label>
                            <select name="hari" required>
                                <option value="">Pilih hari...</option>
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $h)
                                    <option value="{{ $h }}">{{ $h }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="fg-row">
                            <div class="fg"><label>Jam Mulai</label><input type="time" name="jam_mulai" required></div>
                            <div class="fg"><label>Jam Selesai</label><input type="time" name="jam_selesai" required></div>
                        </div>
                        <div class="fg">
                            <label>Kegiatan</label>
                            <select name="kegiatan" required>
                                <option value="Mengajar">Mengajar</option>
                                <option value="Rapat">Rapat</option>
                                <option value="Istirahat">Istirahat</option>
                                <option value="Luar Kampus">Luar Kampus</option>
                            </select>
                        </div>
                        <div class="fg"><label>Mata Kuliah</label><input type="text" name="mata_kuliah"
                                placeholder="Contoh: Algoritma (TI/II/2/M)"></div>
                        <div class="fg"><label>Ruangan</label><input type="text" name="ruangan"
                                placeholder="Contoh: Ruang 3/5"></div>
                        <div class="fg"><label>Keterangan</label><textarea name="keterangan"
                                placeholder="Opsional"></textarea></div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn-cancel" @click="closeModal()">Batal</button>
                        <button type="submit" class="btn btn-dark"><i class="lucide-check" style="font-size:12px"></i>
                            Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ── Modal Edit Mingguan ── -->
        <div x-show="activeModal === 'mingguanEdit'" x-cloak class="modal-bg" @click.self="closeModal()">
            <div class="modal" @click.stop>
                <div class="modal-head">
                    <h3><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                            <path d="m15 5 4 4" />
                        </svg> Edit Jadwal Mingguan</h3>
                    <button class="modal-close" @click="closeModal()"><i class="lucide-x"></i></button>
                </div>
                <form method="POST" :action="'/jadwal-mingguan/' + editData.id">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <div class="fg">
                            <label>Hari</label>
                            <select name="hari" required x-model="editData.hari">
                                <option value="">Pilih hari...</option>
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                                    <option value="{{ $h }}">{{ $h }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="fg-row">
                            <div class="fg"><label>Jam Mulai</label><input type="time" name="jam_mulai" required
                                    x-model="editData.jam_mulai"></div>
                            <div class="fg"><label>Jam Selesai</label><input type="time" name="jam_selesai" required
                                    x-model="editData.jam_selesai"></div>
                        </div>
                        <div class="fg">
                            <label>Kegiatan</label>
                            <select name="kegiatan" required x-model="editData.kegiatan">
                                <option value="Mengajar">Mengajar</option>
                                <option value="Rapat">Rapat</option>
                                <option value="Istirahat">Istirahat</option>
                                <option value="Luar Kampus">Luar Kampus</option>
                            </select>
                        </div>
                        <div class="fg"><label>Mata Kuliah</label><input type="text" name="mata_kuliah"
                                x-model="editData.mata_kuliah" placeholder="Contoh: Algoritma (TI/II/2/M)"></div>
                        <div class="fg"><label>Ruangan</label><input type="text" name="ruangan" x-model="editData.ruangan"
                                placeholder="Contoh: Ruang 3/5"></div>
                        <div class="fg"><label>Keterangan</label><textarea name="keterangan" x-model="editData.keterangan"
                                placeholder="Opsional"></textarea></div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn-cancel" @click="closeModal()">Batal</button>
                        <button type="submit" class="btn btn-dark"><i class="lucide-check" style="font-size:12px"></i>
                            Perbarui</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ── Modal Tambah Akan Datang ── -->
        <div x-show="activeModal === 'akanDatangAdd'" x-cloak class="modal-bg" @click.self="closeModal()"
            x-data="{ fullday: true }">
            <div class="modal" @click.stop>
                <div class="modal-head">
                    <h3><i class="lucide-calendar-clock" style="color:#7c3aed"></i> Tambah Jadwal Akan Datang</h3>
                    <button class="modal-close" @click="closeModal()"><i class="lucide-x"></i></button>
                </div>
                <form method="POST" action="/jadwal-akan-datang">
                    @csrf
                    <div class="modal-body">
                        <div class="fg"><label>Judul Kegiatan</label><input type="text" name="judul" required
                                placeholder="Contoh: Seminar AI, Workshop IoT"></div>
                        <div class="fg-row">
                            <div class="fg"><label>Tanggal Mulai</label><input type="date" name="tanggal_mulai" required>
                            </div>
                            <div class="fg"><label>Tanggal Selesai</label><input type="date" name="tanggal_selesai"
                                    required></div>
                        </div>
                        <div class="fg-check">
                            <input type="checkbox" id="fd_add_u" x-model="fullday" checked>
                            <input type="hidden" name="is_fullday" :value="fullday ? 1 : 0">
                            <label for="fd_add_u">Seharian Penuh</label>
                        </div>
                        <div class="fg-row" x-show="!fullday" x-cloak>
                            <div class="fg"><label>Jam Mulai</label><input type="time" name="jam_mulai"></div>
                            <div class="fg"><label>Jam Selesai</label><input type="time" name="jam_selesai"></div>
                        </div>
                        <div class="fg"><label>Keterangan</label><textarea name="keterangan"
                                placeholder="Opsional"></textarea></div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn-cancel" @click="closeModal()">Batal</button>
                        <button type="submit" class="btn btn-dark"><i class="lucide-check" style="font-size:12px"></i>
                            Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ── Modal Edit Akan Datang ── -->
        <div x-show="activeModal === 'akanDatangEdit'" x-cloak class="modal-bg" @click.self="closeModal()">
            <div class="modal" @click.stop>
                <div class="modal-head">
                    <h3><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="#7c3aed" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                            <path d="m15 5 4 4" />
                        </svg> Edit Jadwal Akan Datang</h3>
                    <button class="modal-close" @click="closeModal()"><i class="lucide-x"></i></button>
                </div>
                <form method="POST" :action="'/jadwal-akan-datang/' + editData.id">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <div class="fg"><label>Judul Kegiatan</label><input type="text" name="judul" required
                                x-model="editData.judul"></div>
                        <div class="fg-row">
                            <div class="fg"><label>Tanggal Mulai</label><input type="date" name="tanggal_mulai" required
                                    x-model="editData.tanggal_mulai"></div>
                            <div class="fg"><label>Tanggal Selesai</label><input type="date" name="tanggal_selesai" required
                                    x-model="editData.tanggal_selesai"></div>
                        </div>
                        <div class="fg-check">
                            <input type="checkbox" id="fd_edit_u" x-model="editFullday">
                            <input type="hidden" name="is_fullday" :value="editFullday ? 1 : 0">
                            <label for="fd_edit_u">Seharian Penuh</label>
                        </div>
                        <div class="fg-row" x-show="!editFullday" x-cloak>
                            <div class="fg"><label>Jam Mulai</label><input type="time" name="jam_mulai"
                                    x-model="editData.jam_mulai"></div>
                            <div class="fg"><label>Jam Selesai</label><input type="time" name="jam_selesai"
                                    x-model="editData.jam_selesai"></div>
                        </div>
                        <div class="fg"><label>Keterangan</label><textarea name="keterangan" x-model="editData.keterangan"
                                placeholder="Opsional"></textarea></div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn-cancel" @click="closeModal()">Batal</button>
                        <button type="submit" class="btn btn-dark"><i class="lucide-check" style="font-size:12px"></i>
                            Perbarui</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ── Modal Tambah Dadakan ─────────────────────────────────────
                 Jadwal Dadakan = HARI INI saja, tanggal di-set otomatis oleh server.
                 Field: Judul, Fullday, Jam Selesai (jika tidak fullday), Keterangan
            ─────────────────────────────────────────────────────────────── -->
        <div x-show="activeModal === 'dadakanAdd'" x-cloak class="modal-bg" @click.self="closeModal()"
            x-data="{ fullday: true }">
            <div class="modal" @click.stop>
                <div class="modal-head">
                    <h3><i class="lucide-alert-triangle" style="color:#be123c"></i> Tambah Jadwal Dadakan</h3>
                    <button class="modal-close" @click="closeModal()"><i class="lucide-x"></i></button>
                </div>
                <div class="modal-notice">
                    <i class="lucide-calendar-x2"></i>
                    Jadwal dadakan otomatis berlaku untuk <strong>hari ini</strong> —
                    {{ now()->translatedFormat('l, d F Y') }}
                </div>
                <form method="POST" action="/jadwal-dadakan">
                    @csrf
                    <div class="modal-body">
                        <div class="fg">
                            <label>Keterangan Singkat</label>
                            <input type="text" name="judul" required placeholder="Contoh: Macet, Sakit, Urusan Mendadak">
                        </div>
                        <div class="fg-check">
                            <input type="checkbox" id="fd_add_d" x-model="fullday" checked>
                            <input type="hidden" name="is_fullday" :value="fullday ? 1 : 0">
                            <label for="fd_add_d">Tidak hadir seharian penuh</label>
                        </div>
                        <div class="fg" x-show="!fullday" x-cloak>
                            <label>Diperkirakan hadir kembali pukul</label>
                            <input type="time" name="jam_selesai" placeholder="Contoh: 10:30">
                            <div style="font-size:11px;color:#a8a29e;margin-top:4px">Jadwal dadakan dimulai dari sekarang
                                sampai jam yang diisi di atas</div>
                        </div>
                        <div class="fg">
                            <label>Keterangan Tambahan</label>
                            <textarea name="keterangan"
                                placeholder="Opsional — Contoh: Masih di jalan karena macet di Jl. Sudirman"></textarea>
                        </div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn-cancel" @click="closeModal()">Batal</button>
                        <button type="submit" class="btn btn-red"><i class="lucide-alert-triangle"
                                style="font-size:12px"></i> Simpan Jadwal Dadakan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ── Modal Edit Dadakan ── -->
        <div x-show="activeModal === 'dadakanEdit'" x-cloak class="modal-bg" @click.self="closeModal()">
            <div class="modal" @click.stop>
                <div class="modal-head">
                    <h3><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="#be123c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                            <path d="m15 5 4 4" />
                        </svg> Edit Jadwal Dadakan</h3>
                    <button class="modal-close" @click="closeModal()"><i class="lucide-x"></i></button>
                </div>
                <form method="POST" :action="'/jadwal-dadakan/' + editData.id">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <div class="fg">
                            <label>Keterangan Singkat</label>
                            <input type="text" name="judul" required x-model="editData.judul">
                        </div>
                        <div class="fg-check">
                            <input type="checkbox" id="fd_edit_d" x-model="editFullday">
                            <input type="hidden" name="is_fullday" :value="editFullday ? 1 : 0">
                            <label for="fd_edit_d">Tidak hadir seharian penuh</label>
                        </div>
                        <div class="fg" x-show="!editFullday" x-cloak>
                            <label>Diperkirakan hadir kembali pukul</label>
                            <input type="time" name="jam_selesai" x-model="editData.jam_selesai">
                        </div>
                        <div class="fg">
                            <label>Keterangan Tambahan</label>
                            <textarea name="keterangan" x-model="editData.keterangan" placeholder="Opsional"></textarea>
                        </div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn-cancel" @click="closeModal()">Batal</button>
                        <button type="submit" class="btn btn-dark"><i class="lucide-check" style="font-size:12px"></i>
                            Perbarui</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Riwayat Akan Datang -->
        <div x-show="activeModal === 'riwayatAkanDatang'" x-cloak class="modal-bg" @click.self="closeModal()">
            <div class="modal modal-lg" @click.stop style="max-width:600px">
                <div class="modal-head"><h3>Riwayat Jadwal Akan Datang</h3><button @click="closeModal()" class="modal-close"><i class="lucide-x"></i></button></div>
                <div class="modal-body" style="max-height:60vh; overflow-y:auto; padding:16px;">
                    @if($riwayatAkanDatang->count() > 0)
                        <div style="display:flex;flex-direction:column;gap:12px;">
                        @foreach($riwayatAkanDatang as $j)
                            <div style="border:1px solid #e2e8f0; border-radius:12px; padding:12px; background:#f8fafc;">
                                <div style="font-weight:600; font-size:14px; color:#1e293b">{{ $j->judul }}</div>
                                <div style="font-size:12.5px; color:#64748b; margin-top:4px;">
                                    {{ $j->tanggal_mulai->translatedFormat('d M Y') }} - {{ $j->tanggal_selesai->translatedFormat('d M Y') }}
                                    @if(!$j->is_fullday)
                                        | {{ substr($j->jam_mulai,0,5) }} - {{ substr($j->jam_selesai,0,5) }}
                                    @endif
                                </div>
                                @if($j->keterangan)
                                    <div style="font-size:12.5px; font-style:italic; color:#475569; margin-top:6px; padding-top:6px; border-top:1px dashed #cbd5e1">{{ $j->keterangan }}</div>
                                @endif
                            </div>
                        @endforeach
                        </div>
                    @else
                        <div style="text-align:center; padding:20px; color:#94a3b8; font-size:14px;">Riwayat kosong</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Modal Riwayat Dadakan -->
        <div x-show="activeModal === 'riwayatDadakan'" x-cloak class="modal-bg" @click.self="closeModal()">
            <div class="modal" @click.stop style="max-width:500px">
                <div class="modal-head"><h3>Riwayat Jadwal Dadakan</h3><button @click="closeModal()" class="modal-close"><i class="lucide-x"></i></button></div>
                <div class="modal-body" style="max-height:60vh; overflow-y:auto; padding:16px;">
                    @if($riwayatDadakan->count() > 0)
                        <div style="display:flex;flex-direction:column;gap:12px;">
                        @foreach($riwayatDadakan as $j)
                            <div style="border:1px solid #e2e8f0; border-radius:12px; padding:12px; background:#f8fafc;">
                                <div style="font-weight:600; font-size:14px; color:#1e293b">{{ $j->judul }}</div>
                                <div style="font-size:12.5px; color:#64748b; margin-top:4px;">
                                    {{ $j->tanggal_mulai->translatedFormat('d M Y') }} ({{ $j->is_fullday ? 'Seharian' : substr($j->jam_mulai,0,5).' - '.substr($j->jam_selesai,0,5) }})
                                </div>
                                @if($j->keterangan)
                                    <div style="font-size:12.5px; font-style:italic; color:#475569; margin-top:6px; padding-top:6px; border-top:1px dashed #cbd5e1">{{ $j->keterangan }}</div>
                                @endif
                            </div>
                        @endforeach
                        </div>
                    @else
                        <div style="text-align:center; padding:20px; color:#94a3b8; font-size:14px;">Riwayat kosong</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ── Modal Konfirmasi Hapus ── -->
        <div x-show="activeModal === 'deleteConfirm'" x-cloak class="modal-bg" @click.self="closeModal()" style="z-index:9999">
            <div class="modal" @click.stop style="max-width:380px; text-align:center;">
                <div class="modal-head" style="justify-content:center; padding-bottom:0; border-bottom:none">
                    <div style="display:inline-flex; align-items:center; justify-content:center; width:56px; height:56px; border-radius:50%; background:#ffe4e6; color:#e11d48; margin-bottom:8px">
                        <i class="lucide-alert-triangle" style="font-size:24px"></i>
                    </div>
                </div>
                <div class="modal-body" style="padding:10px 24px 24px;">
                    <h3 style="font-size:18px; font-weight:700; color:#0f172a; margin-bottom:8px">Konfirmasi Hapus</h3>
                    <p style="color:#64748b; font-size:13.5px; margin:0; line-height:1.5">Tindakan ini tidak dapat dibatalkan. Jadwal ini akan dihapus secara permanen dari sistem.</p>
                </div>
                <div class="modal-foot" style="justify-content:center; gap:12px; border-top:1px solid #f1f5f9; padding:16px 24px;">
                    <button type="button" class="btn-cancel" @click="closeModal()" style="min-width:100px; padding:8px">Batal</button>
                    <form method="POST" :action="deleteUrl" style="margin:0">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-dark" style="background:#e11d48; border-color:#e11d48; min-width:100px; padding:8px 16px"><i class="lucide-trash-2" style="margin-right:4px;"></i> Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>


    </div>{{-- END global modal wrapper --}}

@endsection

@push('scripts')
    <script>
        function addRipple(event, el) {
            const rect = el.getBoundingClientRect();
            const x = event.clientX - rect.left;
            const y = event.clientY - rect.top;
            const size = Math.max(rect.width, rect.height) * 2;
            const span = document.createElement('span');
            span.classList.add('ripple-el');
            span.style.cssText = `width:${size}px;height:${size}px;left:${x - size / 2}px;top:${y - size / 2}px`;
            el.appendChild(span);
            span.addEventListener('animationend', () => span.remove());
        }
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.btn').forEach(btn => {
                btn.addEventListener('click', e => addRipple(e, btn));
            });
        });
    </script>
@endpush
