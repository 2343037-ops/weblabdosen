@extends('layouts.app')
@section('title', 'Dashboard Dosen')

@push('styles')
<link rel="stylesheet" href="/css/dashboard.css">
@endpush

@section('content')

    @php $dosen = $dosen ?? null; @endphp

    <!-- HERO -->
    <div class="dash-hero">
        <h2>{{ $dosen->nama ?? 'Dosen' }}</h2>
        <div class="info">
            <span><i class="lucide-badge-check"></i> {{ $dosen->jabatan ?? '' }}</span>
            <span class="sp"></span>
            <span><i class="lucide-hash"></i> NIDN: {{ $dosen->nidn ?? '' }}</span>
            <span class="sp"></span>
            <span><i class="lucide-calendar"></i> {{ now()->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>

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
                    <p>Status diperbarui otomatis berdasarkan jadwal. Jam kerja: 08:00 – 17:00 WITA. Di luar jam kerja = Tidak Di Ruangan.</p>
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
                    <option value="Tidak Di Ruangan" {{ ($dosen->status ?? '') === 'Tidak Di Ruangan' ? 'selected' : '' }}>Tidak Di Ruangan</option>
                </select>
                <button type="submit" class="btn btn-dark"><i class="lucide-refresh-cw" style="font-size:12px"></i> Perbarui</button>
            </form>
        @endif

        @if(session('success'))
            <div class="status-msg" style="margin-top:10px"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
        @endif
    </div>

    <!-- ═══ WHATSAPP PROFIL ═══ -->
    <div class="wa-box">
        <h3>
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
            Nomor WhatsApp Dosen
        </h3>

        @if($dosen->telepon)
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;flex-wrap:wrap">
                <span style="font-size:12px;color:#57534e;font-weight:600">Nomor aktif:</span>
                <span class="wa-preview">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="#16a34a"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                    {{ $dosen->telepon }}
                </span>
                <span style="font-size:11px;color:#a8a29e">— Tombol WA sudah aktif di halaman publik</span>
            </div>
        @endif

        <form method="POST" action="{{ route('dosen.updateProfil') }}" class="wa-form">
            @csrf
            <input type="tel" name="telepon"
                   value="{{ $dosen->telepon ?? '' }}"
                   placeholder="Contoh: 08123456789"
                   maxlength="20">
            <button type="submit" class="btn-wa-save">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Simpan Nomor WA
            </button>
        </form>

        <div class="wa-hint">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
            Nomor ini akan ditampilkan sebagai tombol "Hubungi via WhatsApp" untuk mahasiswa di halaman publik. Kosongkan untuk menyembunyikan tombol.
        </div>

        @if(session('success_profil'))
            <div class="status-msg" style="margin-top:10px">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                {{ session('success_profil') }}
            </div>
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
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th>Waktu</th>
                                <th>Kegiatan</th>
                                <th>Mata Kuliah</th>
                                <th>Ruangan</th>
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
                                        <td>
                                            <div class="act-btns">
                                                <button type="button" class="btn-edit"
                                                    @click="editData = { id: {{ $j->id }}, hari: '{{ $j->hari }}', jam_mulai: '{{ substr($j->jam_mulai, 0, 5) }}', jam_selesai: '{{ substr($j->jam_selesai, 0, 5) }}', kegiatan: '{{ $j->kegiatan }}', mata_kuliah: '{{ addslashes($j->mata_kuliah) }}', ruangan: '{{ addslashes($j->ruangan) }}', keterangan: '{{ addslashes($j->keterangan) }}' }; openModal('mingguanEdit')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                                </button>
                                                <form method="POST" action="/jadwal-mingguan/{{ $j->id }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn-del"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
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
                <button class="btn-add" @click="openModal('akanDatangAdd'); addRipple($event, $el)">
                    <i class="lucide-plus"></i> Tambah
                </button>
            </div>
            <div class="sec-body">
                @if($jadwalAkanDatang->count() > 0)
                    @foreach($jadwalAkanDatang as $j)
                        <div class="ev u">
                            <div class="ev-left">
                                <div class="ev-title u">{{ $j->judul }}</div>
                                <div class="ev-meta">
                                    <span class="ev-mi u"><i class="lucide-calendar"></i>
                                        {{ $j->tanggal_mulai->translatedFormat('d F Y') }}{{ $j->tanggal_mulai != $j->tanggal_selesai ? ' – ' . $j->tanggal_selesai->translatedFormat('d F Y') : '' }}</span>
                                    <span class="ev-mi u"><i class="lucide-clock-3"></i>
                                        {{ $j->is_fullday ? 'Seharian' : substr($j->jam_mulai, 0, 5) . ' – ' . substr($j->jam_selesai, 0, 5) }}</span>
                                </div>
                                @if($j->keterangan)
                                <div class="ev-note">{{ $j->keterangan }}</div>@endif
                            </div>
                            <div class="act-btns">
                                <button type="button" class="btn-edit"
                                    @click="editData = { id: {{ $j->id }}, judul: '{{ addslashes($j->judul) }}', tanggal_mulai: '{{ $j->tanggal_mulai->format('Y-m-d') }}', tanggal_selesai: '{{ $j->tanggal_selesai->format('Y-m-d') }}', jam_mulai: '{{ $j->jam_mulai ? substr($j->jam_mulai, 0, 5) : '' }}', jam_selesai: '{{ $j->jam_selesai ? substr($j->jam_selesai, 0, 5) : '' }}', keterangan: '{{ addslashes($j->keterangan) }}' }; editFullday = {{ $j->is_fullday ? 'true' : 'false' }}; openModal('akanDatangEdit')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                </button>
                                <form method="POST" action="/jadwal-akan-datang/{{ $j->id }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-del"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg></button>
                                </form>
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
                <button class="btn-add" @click="openModal('dadakanAdd'); addRipple($event, $el)">
                    <i class="lucide-plus"></i> Tambah
                </button>
            </div>
            <div class="sec-body">
                @if($jadwalDadakan->count() > 0)
                    @foreach($jadwalDadakan as $j)
                        <div class="ev e">
                            <div class="ev-left">
                                <div class="ev-title e">{{ $j->judul }}</div>
                                <div class="ev-meta">
                                    <span class="ev-mi e"><i class="lucide-calendar"></i>
                                        {{ $j->tanggal_mulai->translatedFormat('d F Y') }}</span>
                                    <span class="ev-mi e"><i class="lucide-clock-3"></i>
                                        {{ $j->is_fullday ? 'Seharian' : 'Mulai ' . substr($j->jam_mulai, 0, 5) . ' s/d ' . substr($j->jam_selesai, 0, 5) }}</span>
                                </div>
                                @if($j->keterangan)
                                <div class="ev-note">{{ $j->keterangan }}</div>@endif
                            </div>
                            <div class="act-btns">
                                <button type="button" class="btn-edit"
                                    @click="editData = { id: {{ $j->id }}, judul: '{{ addslashes($j->judul) }}', jam_selesai: '{{ $j->jam_selesai ? substr($j->jam_selesai, 0, 5) : '' }}', keterangan: '{{ addslashes($j->keterangan) }}' }; editFullday = {{ $j->is_fullday ? 'true' : 'false' }}; openModal('dadakanEdit')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                </button>
                                <form method="POST" action="/jadwal-dadakan/{{ $j->id }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-del"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg></button>
                                </form>
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
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
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
                                <option value="Bimbingan">Bimbingan</option>
                                <option value="Rapat">Rapat</option>
                                <option value="Istirahat">Istirahat</option>
                                <option value="Luar Kampus">Luar Kampus</option>
                            </select>
                        </div>
                        <div class="fg"><label>Mata Kuliah</label><input type="text" name="mata_kuliah" placeholder="Contoh: Algoritma (TI/II/2/M)"></div>
                        <div class="fg"><label>Ruangan</label><input type="text" name="ruangan" placeholder="Contoh: Ruang 3/5"></div>
                        <div class="fg"><label>Keterangan</label><textarea name="keterangan" placeholder="Opsional"></textarea></div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn-cancel" @click="closeModal()">Batal</button>
                        <button type="submit" class="btn btn-dark"><i class="lucide-check" style="font-size:12px"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ── Modal Edit Mingguan ── -->
        <div x-show="activeModal === 'mingguanEdit'" x-cloak class="modal-bg" @click.self="closeModal()">
            <div class="modal" @click.stop>
                <div class="modal-head">
                    <h3><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg> Edit Jadwal Mingguan</h3>
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
                            <div class="fg"><label>Jam Mulai</label><input type="time" name="jam_mulai" required x-model="editData.jam_mulai"></div>
                            <div class="fg"><label>Jam Selesai</label><input type="time" name="jam_selesai" required x-model="editData.jam_selesai"></div>
                        </div>
                        <div class="fg">
                            <label>Kegiatan</label>
                            <select name="kegiatan" required x-model="editData.kegiatan">
                                <option value="Mengajar">Mengajar</option>
                                <option value="Bimbingan">Bimbingan</option>
                                <option value="Rapat">Rapat</option>
                                <option value="Istirahat">Istirahat</option>
                                <option value="Luar Kampus">Luar Kampus</option>
                            </select>
                        </div>
                        <div class="fg"><label>Mata Kuliah</label><input type="text" name="mata_kuliah" x-model="editData.mata_kuliah" placeholder="Contoh: Algoritma (TI/II/2/M)"></div>
                        <div class="fg"><label>Ruangan</label><input type="text" name="ruangan" x-model="editData.ruangan" placeholder="Contoh: Ruang 3/5"></div>
                        <div class="fg"><label>Keterangan</label><textarea name="keterangan" x-model="editData.keterangan" placeholder="Opsional"></textarea></div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn-cancel" @click="closeModal()">Batal</button>
                        <button type="submit" class="btn btn-dark"><i class="lucide-check" style="font-size:12px"></i> Perbarui</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ── Modal Tambah Akan Datang ── -->
        <div x-show="activeModal === 'akanDatangAdd'" x-cloak class="modal-bg" @click.self="closeModal()" x-data="{ fullday: true }">
            <div class="modal" @click.stop>
                <div class="modal-head">
                    <h3><i class="lucide-calendar-clock" style="color:#7c3aed"></i> Tambah Jadwal Akan Datang</h3>
                    <button class="modal-close" @click="closeModal()"><i class="lucide-x"></i></button>
                </div>
                <form method="POST" action="/jadwal-akan-datang">
                    @csrf
                    <div class="modal-body">
                        <div class="fg"><label>Judul Kegiatan</label><input type="text" name="judul" required placeholder="Contoh: Seminar AI, Workshop IoT"></div>
                        <div class="fg-row">
                            <div class="fg"><label>Tanggal Mulai</label><input type="date" name="tanggal_mulai" required></div>
                            <div class="fg"><label>Tanggal Selesai</label><input type="date" name="tanggal_selesai" required></div>
                        </div>
                        <div class="fg-check">
                            <input type="checkbox" id="fd_add_u" x-model="fullday" checked>
                            <input type="hidden" name="is_fullday" :value="fullday ? 1 : 0">
                            <label for="fd_add_u">Seharian (fullday)</label>
                        </div>
                        <div class="fg-row" x-show="!fullday" x-cloak>
                            <div class="fg"><label>Jam Mulai</label><input type="time" name="jam_mulai"></div>
                            <div class="fg"><label>Jam Selesai</label><input type="time" name="jam_selesai"></div>
                        </div>
                        <div class="fg"><label>Keterangan</label><textarea name="keterangan" placeholder="Opsional"></textarea></div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn-cancel" @click="closeModal()">Batal</button>
                        <button type="submit" class="btn btn-dark"><i class="lucide-check" style="font-size:12px"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ── Modal Edit Akan Datang ── -->
        <div x-show="activeModal === 'akanDatangEdit'" x-cloak class="modal-bg" @click.self="closeModal()">
            <div class="modal" @click.stop>
                <div class="modal-head">
                    <h3><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg> Edit Jadwal Akan Datang</h3>
                    <button class="modal-close" @click="closeModal()"><i class="lucide-x"></i></button>
                </div>
                <form method="POST" :action="'/jadwal-akan-datang/' + editData.id">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <div class="fg"><label>Judul Kegiatan</label><input type="text" name="judul" required x-model="editData.judul"></div>
                        <div class="fg-row">
                            <div class="fg"><label>Tanggal Mulai</label><input type="date" name="tanggal_mulai" required x-model="editData.tanggal_mulai"></div>
                            <div class="fg"><label>Tanggal Selesai</label><input type="date" name="tanggal_selesai" required x-model="editData.tanggal_selesai"></div>
                        </div>
                        <div class="fg-check">
                            <input type="checkbox" id="fd_edit_u" x-model="editFullday">
                            <input type="hidden" name="is_fullday" :value="editFullday ? 1 : 0">
                            <label for="fd_edit_u">Seharian (fullday)</label>
                        </div>
                        <div class="fg-row" x-show="!editFullday" x-cloak>
                            <div class="fg"><label>Jam Mulai</label><input type="time" name="jam_mulai" x-model="editData.jam_mulai"></div>
                            <div class="fg"><label>Jam Selesai</label><input type="time" name="jam_selesai" x-model="editData.jam_selesai"></div>
                        </div>
                        <div class="fg"><label>Keterangan</label><textarea name="keterangan" x-model="editData.keterangan" placeholder="Opsional"></textarea></div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn-cancel" @click="closeModal()">Batal</button>
                        <button type="submit" class="btn btn-dark"><i class="lucide-check" style="font-size:12px"></i> Perbarui</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ── Modal Tambah Dadakan ─────────────────────────────────────
             Jadwal Dadakan = HARI INI saja, tanggal di-set otomatis oleh server.
             Field: Judul, Fullday, Jam Selesai (jika tidak fullday), Keterangan
        ─────────────────────────────────────────────────────────────── -->
        <div x-show="activeModal === 'dadakanAdd'" x-cloak class="modal-bg" @click.self="closeModal()" x-data="{ fullday: true }">
            <div class="modal" @click.stop>
                <div class="modal-head">
                    <h3><i class="lucide-alert-triangle" style="color:#be123c"></i> Tambah Jadwal Dadakan</h3>
                    <button class="modal-close" @click="closeModal()"><i class="lucide-x"></i></button>
                </div>
                <div class="modal-notice">
                    <i class="lucide-calendar-x2"></i>
                    Jadwal dadakan otomatis berlaku untuk <strong>hari ini</strong> — {{ now()->translatedFormat('l, d F Y') }}
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
                            <label for="fd_add_d">Tidak hadir seharian (fullday)</label>
                        </div>
                        <div class="fg" x-show="!fullday" x-cloak>
                            <label>Diperkirakan hadir kembali pukul</label>
                            <input type="time" name="jam_selesai" placeholder="Contoh: 10:30">
                            <div style="font-size:11px;color:#a8a29e;margin-top:4px">Jadwal dadakan dimulai dari sekarang sampai jam yang diisi di atas</div>
                        </div>
                        <div class="fg">
                            <label>Keterangan Tambahan</label>
                            <textarea name="keterangan" placeholder="Opsional — Contoh: Masih di jalan karena macet di Jl. Sudirman"></textarea>
                        </div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn-cancel" @click="closeModal()">Batal</button>
                        <button type="submit" class="btn btn-red"><i class="lucide-alert-triangle" style="font-size:12px"></i> Simpan Jadwal Dadakan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ── Modal Edit Dadakan ── -->
        <div x-show="activeModal === 'dadakanEdit'" x-cloak class="modal-bg" @click.self="closeModal()">
            <div class="modal" @click.stop>
                <div class="modal-head">
                    <h3><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#be123c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg> Edit Jadwal Dadakan</h3>
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
                            <label for="fd_edit_d">Tidak hadir seharian (fullday)</label>
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
                        <button type="submit" class="btn btn-dark"><i class="lucide-check" style="font-size:12px"></i> Perbarui</button>
                    </div>
                </form>
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
        span.style.cssText = `width:${size}px;height:${size}px;left:${x - size/2}px;top:${y - size/2}px`;
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
