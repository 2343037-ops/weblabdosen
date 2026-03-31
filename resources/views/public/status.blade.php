<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi kehadiran Dosen STMIK Widya Cipta Dharma</title>
    <meta name="description"
        content="Informasi jadwal dan status keberadaan dosen Lab Komputer STMIK Widya Cipta Dharma Samarinda">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lucide-static@latest/font/lucide.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="/css/status.css">
</head>

<body x-data="dosenModal()">

    <header class="hdr">
        <div class="mx">
            <div class="hdr-in">
                <div class="hdr-l">
                    <div>
                        <h1>Informasi Kehadiran Dosen</h1>
                        <p class="sub">STMIK Widya Cipta Dharma, Samarinda</p>
                    </div>
                </div>
                <div class="hdr-date"><i class="lucide-clock-3"></i> {{ now()->translatedFormat('l, d F Y') }}</div>
            </div>
        </div>
    </header>

    <main class="mx">

        {{-- ══ Info Bar ══ --}}
        <div class="info-bar">
            <div class="info-top">
                <div class="info-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span><strong>Jam Kerja:</strong> 08:00 – 17:00 WITA</span>
                </div>
                <div class="info-item info-hint">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                    <span>Klik kartu untuk detail jadwal lengkap</span>
                </div>
            </div>
        </div>

        @php
            $dosenDiRuangan   = $dosenList->filter(fn($d) => $d->status === 'Di Ruangan');
            $dosenTidakDiRuangan = $dosenList->filter(fn($d) => $d->status !== 'Di Ruangan');
        @endphp

        {{-- ══ Helper macro untuk render card ══ --}}
        @php
            function buildCardPhone($telepon) {
                if (!$telepon) return '';
                $p = preg_replace('/[^0-9]/', '', $telepon);
                return str_starts_with($p, '0') ? '62' . substr($p, 1) : $p;
            }
            function buildCardPesan($nama) {
                return rawurlencode(
                    "Permisi Bapak/Ibu {$nama},\n\n" .
                    "Saya [Nama Anda] dari prodi [Nama Prodi] kelas [Kelas], mohon maaf mengganggu waktunya.\n\n" .
                    "Saya ingin bertanya mengenai keperluan perkuliahan/bimbingan. Apakah Bapak/Ibu ada waktu untuk saya temui hari ini? Terima kasih banyak sebelumnya."
                );
            }
        @endphp

        {{-- ══ SECTION: DI RUANGAN ══ --}}
        <div class="section-group ok-group">
            <div class="section-head ok-head">
                <span class="section-dot ok"></span>
                <h2>Di Ruangan</h2>
                <span class="section-count ok-count">{{ $dosenDiRuangan->count() }} Dosen</span>
                <span class="section-desc">Dosen tersedia dan dapat ditemui sekarang</span>
            </div>

            @if($dosenDiRuangan->isEmpty())
                <div class="section-empty">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <p>Belum ada dosen yang berada di ruangan saat ini</p>
                </div>
            @else
                <div class="cards">
                    @foreach($dosenDiRuangan as $idx => $dosen)
                        @php
                            $ok = true;
                            $cardPhone = buildCardPhone($dosen->telepon);
                            $cardPesan = $dosen->telepon ? buildCardPesan($dosen->nama) : '';
                            $hasDadakan  = $dosen->jadwalDadakan->count() > 0;
                            $hasUpcoming = $dosen->jadwalAkanDatang->count() > 0;
                            $hasWeekly   = $dosen->jadwalMingguan->count() > 0;
                            $jadwalMingguanJs   = $dosen->jadwalMingguan->map(fn($j) => ['hari'=>$j->hari,'jam_mulai'=>$j->jam_mulai,'jam_selesai'=>$j->jam_selesai,'kegiatan'=>$j->kegiatan,'mata_kuliah'=>$j->mata_kuliah,'ruangan'=>$j->ruangan,'keterangan'=>$j->keterangan]);
                            $jadwalAkanDatangJs = $dosen->jadwalAkanDatang->map(fn($j) => ['judul'=>$j->judul,'tanggal_mulai'=>$j->tanggal_mulai->format('Y-m-d'),'tanggal_selesai'=>$j->tanggal_selesai->format('Y-m-d'),'tanggal_mulai_fmt'=>$j->tanggal_mulai->translatedFormat('d F Y'),'tanggal_selesai_fmt'=>$j->tanggal_selesai->translatedFormat('d F Y'),'is_fullday'=>$j->is_fullday,'jam_mulai'=>$j->jam_mulai,'jam_selesai'=>$j->jam_selesai,'keterangan'=>$j->keterangan]);
                            $jadwalDadakanJs    = $dosen->jadwalDadakan->map(fn($j) => ['judul'=>$j->judul,'tanggal_mulai'=>$j->tanggal_mulai->format('Y-m-d'),'tanggal_selesai'=>$j->tanggal_selesai->format('Y-m-d'),'tanggal_mulai_fmt'=>$j->tanggal_mulai->translatedFormat('d F Y'),'tanggal_selesai_fmt'=>$j->tanggal_selesai->translatedFormat('d F Y'),'is_fullday'=>$j->is_fullday,'jam_mulai'=>$j->jam_mulai,'jam_selesai'=>$j->jam_selesai,'keterangan'=>$j->keterangan]);
                        @endphp
                        <div class="sc ok" @click="openModal({id:{{ $loop->index }},nama:@js($dosen->nama),nidn:@js($dosen->nidn),nik:@js($dosen->nik),jabatan:@js($dosen->jabatan),status:@js($dosen->status),ruangan:@js($dosen->ruangan),telepon:@js($dosen->telepon),waPhone:@js($cardPhone),waPesan:@js($cardPesan),tampilkan_nidn:{{ ($dosen->tampilkan_nidn??true)?'true':'false' }},tampilkan_nik:{{ ($dosen->tampilkan_nik??false)?'true':'false' }},jadwalMingguan:@js($jadwalMingguanJs),jadwalAkanDatang:@js($jadwalAkanDatangJs),jadwalDadakan:@js($jadwalDadakanJs),hasDadakan:{{ $hasDadakan?'true':'false' }},hasUpcoming:{{ $hasUpcoming?'true':'false' }},hasWeekly:{{ $hasWeekly?'true':'false' }}})">
                            <div class="bar"></div>
                            <span class="role">{{ $dosen->jabatan }}</span>
                            <div class="nm">{{ $dosen->nama }}</div>
                            <div class="nd">
                                @if($dosen->tampilkan_nidn ?? true)NIDN: {{ $dosen->nidn }}@endif
                                @if(($dosen->tampilkan_nik ?? false) && $dosen->nik)
                                    @if($dosen->tampilkan_nidn ?? true) &nbsp;·&nbsp; @endif NIK: {{ $dosen->nik }}
                                @endif
                            </div>
                            <div style="flex:1;min-height:10px"></div>
                            <div class="sc-meta">
                                <div class="bdg ok"><span class="dt"></span>Di Ruangan</div>
                                @if($dosen->ruangan)
                                    <div class="card-room">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                        {{ $dosen->ruangan }}
                                    </div>
                                @endif
                            </div>
                            <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg></span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ══ SECTION: TIDAK DI RUANGAN ══ --}}
        <div class="section-group away-group" style="margin-top:32px">
            <div class="section-head away-head">
                <span class="section-dot away"></span>
                <h2>Tidak Di Ruangan</h2>
                <span class="section-count away-count">{{ $dosenTidakDiRuangan->count() }} Dosen</span>
                <span class="section-desc">Dosen sedang ada kegiatan / jadwal</span>
            </div>

            @if($dosenTidakDiRuangan->isEmpty())
                <div class="section-empty">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <p>Semua dosen sedang berada di ruangan</p>
                </div>
            @else
                <div class="cards">
                    @foreach($dosenTidakDiRuangan as $idx => $dosen)
                        @php
                            $ok = false;
                            $cardPhone = buildCardPhone($dosen->telepon);
                            $cardPesan = $dosen->telepon ? buildCardPesan($dosen->nama) : '';
                            $hasDadakan  = $dosen->jadwalDadakan->count() > 0;
                            $hasUpcoming = $dosen->jadwalAkanDatang->count() > 0;
                            $hasWeekly   = $dosen->jadwalMingguan->count() > 0;
                            $jadwalMingguanJs   = $dosen->jadwalMingguan->map(fn($j) => ['hari'=>$j->hari,'jam_mulai'=>$j->jam_mulai,'jam_selesai'=>$j->jam_selesai,'kegiatan'=>$j->kegiatan,'mata_kuliah'=>$j->mata_kuliah,'ruangan'=>$j->ruangan,'keterangan'=>$j->keterangan]);
                            $jadwalAkanDatangJs = $dosen->jadwalAkanDatang->map(fn($j) => ['judul'=>$j->judul,'tanggal_mulai'=>$j->tanggal_mulai->format('Y-m-d'),'tanggal_selesai'=>$j->tanggal_selesai->format('Y-m-d'),'tanggal_mulai_fmt'=>$j->tanggal_mulai->translatedFormat('d F Y'),'tanggal_selesai_fmt'=>$j->tanggal_selesai->translatedFormat('d F Y'),'is_fullday'=>$j->is_fullday,'jam_mulai'=>$j->jam_mulai,'jam_selesai'=>$j->jam_selesai,'keterangan'=>$j->keterangan]);
                            $jadwalDadakanJs    = $dosen->jadwalDadakan->map(fn($j) => ['judul'=>$j->judul,'tanggal_mulai'=>$j->tanggal_mulai->format('Y-m-d'),'tanggal_selesai'=>$j->tanggal_selesai->format('Y-m-d'),'tanggal_mulai_fmt'=>$j->tanggal_mulai->translatedFormat('d F Y'),'tanggal_selesai_fmt'=>$j->tanggal_selesai->translatedFormat('d F Y'),'is_fullday'=>$j->is_fullday,'jam_mulai'=>$j->jam_mulai,'jam_selesai'=>$j->jam_selesai,'keterangan'=>$j->keterangan]);
                        @endphp
                        <div class="sc away" @click="openModal({id:{{ $loop->index }},nama:@js($dosen->nama),nidn:@js($dosen->nidn),nik:@js($dosen->nik),jabatan:@js($dosen->jabatan),status:@js($dosen->status),ruangan:@js($dosen->ruangan),telepon:@js($dosen->telepon),waPhone:@js($cardPhone),waPesan:@js($cardPesan),tampilkan_nidn:{{ ($dosen->tampilkan_nidn??true)?'true':'false' }},tampilkan_nik:{{ ($dosen->tampilkan_nik??false)?'true':'false' }},jadwalMingguan:@js($jadwalMingguanJs),jadwalAkanDatang:@js($jadwalAkanDatangJs),jadwalDadakan:@js($jadwalDadakanJs),hasDadakan:{{ $hasDadakan?'true':'false' }},hasUpcoming:{{ $hasUpcoming?'true':'false' }},hasWeekly:{{ $hasWeekly?'true':'false' }}})">
                            <div class="bar"></div>
                            <span class="role">{{ $dosen->jabatan }}</span>
                            <div class="nm">{{ $dosen->nama }}</div>
                            <div class="nd">
                                @if($dosen->tampilkan_nidn ?? true)NIDN: {{ $dosen->nidn }}@endif
                                @if(($dosen->tampilkan_nik ?? false) && $dosen->nik)
                                    @if($dosen->tampilkan_nidn ?? true) &nbsp;·&nbsp; @endif NIK: {{ $dosen->nik }}
                                @endif
                            </div>
                            <div style="flex:1;min-height:10px"></div>
                            <div class="sc-meta">
                                <div class="bdg away"><span class="dt"></span>Tidak Di Ruangan</div>
                                @if($dosen->ruangan)
                                    <div class="card-room">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                        {{ $dosen->ruangan }}
                                    </div>
                                @endif
                            </div>
                            <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg></span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </main>

    {{-- ══════════════════════════════════════════════════════
         MODAL POPUP — Detail Dosen
         ══════════════════════════════════════════════════════ --}}
    <div class="modal-overlay" x-show="showModal" x-cloak @click.self="closeModal()"
        x-transition:enter="modal-enter"
        x-transition:enter-start="modal-enter-from"
        x-transition:enter-end="modal-enter-to"
        x-transition:leave="modal-leave"
        x-transition:leave-start="modal-leave-from"
        x-transition:leave-end="modal-leave-to">

        <div class="modal-box" x-show="showModal" x-cloak
            x-transition:enter="modalbox-enter"
            x-transition:enter-start="modalbox-enter-from"
            x-transition:enter-end="modalbox-enter-to"
            x-transition:leave="modalbox-leave"
            x-transition:leave-start="modalbox-leave-from"
            x-transition:leave-end="modalbox-leave-to"
            @keydown.escape.window="closeModal()">

            {{-- ── Modal Header ── --}}
            <div class="modal-hdr" :class="selected && selected.status === 'Di Ruangan' ? 'ok' : 'away'">
                <div class="modal-hdr-info">
                    <div>
                        <div class="modal-nama" x-text="selected ? selected.nama : ''"></div>
                        <div class="modal-sub">
                            {{-- NIDN / NIK sesuai toggle --}}
                            <template x-if="selected && selected.tampilkan_nidn && selected.nidn">
                                <span class="modal-nidn" x-text="'NIDN: ' + selected.nidn"></span>
                            </template>
                            <template x-if="selected && selected.tampilkan_nik && selected.nik">
                                <span class="modal-nidn" x-text="'NIK: ' + selected.nik"></span>
                            </template>
                        </div>
                        {{-- Ruangan --}}
                        <template x-if="selected && selected.ruangan">
                            <div class="modal-room">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                <span x-text="selected.ruangan"></span>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="modal-hdr-right">
                    <div class="bdg" :class="selected && selected.status === 'Di Ruangan' ? 'ok' : 'away'">
                        <span class="dt"></span>
                        <span x-text="selected ? selected.status : ''"></span>
                    </div>
                    <button class="modal-close" @click="closeModal()" title="Tutup">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- ── Modal Body — Jadwal ── --}}
            <div class="modal-body" x-data="{ openW: false, cari: '', searching: false, hariCari: '', tglLabel: '', mingguanFiltered: [], akanDatangFiltered: [], dadakanFiltered: [],
                doSearch(sel) {
                    if (!this.cari) { this.searching = false; return; }
                    this.searching = true;
                    let [y,m,dd] = this.cari.split('-').map(Number); let d = new Date(y, m-1, dd);
                    let days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                    this.hariCari = days[d.getDay()];
                    this.tglLabel = this.hariCari + ', ' + dd + ' ' + ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][m-1] + ' ' + y;
                    let ds = this.cari;
                    if (sel) {
                        this.mingguanFiltered = sel.jadwalMingguan.filter(j => j.hari === this.hariCari);
                        this.akanDatangFiltered = sel.jadwalAkanDatang.filter(j => ds >= j.tanggal_mulai && ds <= j.tanggal_selesai);
                        this.dadakanFiltered = sel.jadwalDadakan.filter(j => ds >= j.tanggal_mulai && ds <= j.tanggal_selesai);
                    }
                },
                resetSearch() { this.cari = ''; this.searching = false; }
            }">

                {{-- ── WA Button di Modal ── --}}
                <template x-if="selected && selected.waPhone">
                    <div class="modal-wa-wrap">
                        <a :href="'https://wa.me/' + selected.waPhone + '?text=' + selected.waPesan"
                            target="_blank" class="btn-wa btn-wa-lg"
                            :title="'Hubungi ' + selected.nama + ' via WhatsApp'">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z" />
                            </svg>
                            Hubungi via WhatsApp
                        </a>
                    </div>
                </template>

                {{-- Search bar --}}
                <div class="ds-search modal-search">
                    <label><i class="lucide-search"></i> Cari jadwal</label>
                    <input type="date" x-model="cari" @change="doSearch(selected)">
                    <button class="btn-rst" x-show="searching" x-cloak @click="resetSearch()"
                        x-transition.duration.150ms>Reset</button>
                </div>

                {{-- Search banner --}}
                <template x-if="searching">
                    <div class="ds-banner">
                        <i class="lucide-calendar-search"></i> Jadwal di tanggal <strong x-text="tglLabel"></strong>
                    </div>
                </template>

                {{-- ═══ SEARCH MODE ═══ --}}
                <template x-if="searching">
                    <div>
                        <template x-if="dadakanFiltered.length > 0">
                            <div class="ss">
                                <div class="sh">
                                    <div class="si e">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3" />
                                            <path d="M12 9v4" /><path d="M12 17h.01" />
                                        </svg>
                                    </div>
                                    <span class="sl e">Jadwal Dadakan</span>
                                    <span class="sn e" x-text="dadakanFiltered.length"></span>
                                </div>
                                <template x-for="j in dadakanFiltered" :key="j.judul + j.tanggal_mulai">
                                    <div class="alert-box alert-red">
                                        <div class="alert-title">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3" /><path d="M12 9v4" /><path d="M12 17h.01" /></svg>
                                            Pemberitahuan Khusus
                                        </div>
                                        <div class="alert-p">
                                            Dosen memiliki perubahan jadwal/berhalangan hadir pada <strong><span x-text="j.tanggal_mulai_fmt + (j.tanggal_mulai !== j.tanggal_selesai ? ' s/d ' + j.tanggal_selesai_fmt : '')"></span></strong> <strong><span x-text="j.is_fullday ? 'seharian' : 'dari pukul ' + (j.jam_mulai||'').substring(0,5) + ' s/d ' + (j.jam_selesai||'').substring(0,5)"></span></strong> karena agenda: <strong><span x-text="j.judul"></span></strong>.
                                        </div>
                                        <div class="alert-keterangan" x-show="j.keterangan">Keterangan: <span x-text="j.keterangan"></span></div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="akanDatangFiltered.length > 0">
                            <div class="ss">
                                <div class="sh">
                                    <div class="si u"><i class="lucide-calendar-clock"></i></div>
                                    <span class="sl u">Jadwal Akan Datang</span>
                                    <span class="sn u" x-text="akanDatangFiltered.length"></span>
                                </div>
                                <template x-for="j in akanDatangFiltered" :key="j.judul + j.tanggal_mulai">
                                    <div class="alert-box alert-blue">
                                        <div class="alert-title">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4" /><path d="M16 2v4" /><rect width="18" height="18" x="3" y="4" rx="2" /><path d="M3 10h18" /><path d="M8 14h.01" /><path d="M12 14h.01" /><path d="M16 14h.01" /><path d="M8 18h.01" /><path d="M12 18h.01" /></svg>
                                            Agenda Terencana
                                        </div>
                                        <div class="alert-p">
                                            Pada tanggal <strong><span x-text="j.tanggal_mulai_fmt + (j.tanggal_mulai !== j.tanggal_selesai ? ' s/d ' + j.tanggal_selesai_fmt : '')"></span></strong>, Dosen memiliki agenda luar kampus / berhalangan hadir: <strong><span x-text="j.judul"></span></strong>
                                            <span x-show="!j.is_fullday"> (dari pukul <strong x-text="(j.jam_mulai||'').substring(0,5)"></strong> s/d <strong x-text="(j.jam_selesai||'').substring(0,5)"></strong>)</span>.
                                        </div>
                                        <div class="alert-keterangan" x-show="j.keterangan">Keterangan: <span x-text="j.keterangan"></span></div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="mingguanFiltered.length > 0">
                            <div class="ss">
                                <div class="sh">
                                    <span class="sl w">Jadwal Mingguan — <span x-text="hariCari"></span></span>
                                    <span class="sn w" x-text="mingguanFiltered.length"></span>
                                </div>
                                <div class="table-responsive">
                                    <table class="wt">
                                        <thead>
                                            <tr><th>Waktu</th><th>Kegiatan</th><th>Mata Kuliah</th><th>Ruangan</th><th>Keterangan</th></tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="j in mingguanFiltered" :key="j.hari + j.jam_mulai">
                                                <tr>
                                                    <td class="tm" x-text="(j.jam_mulai||'').substring(0,5)+' – '+(j.jam_selesai||'').substring(0,5)"></td>
                                                    <td style="white-space: nowrap" x-text="j.kegiatan"></td>
                                                    <td class="sb" x-text="j.mata_kuliah || '—'"></td>
                                                    <td class="rm" x-text="j.ruangan || '—'"></td>
                                                    <td class="rm" style="color:#78716c;" x-text="j.keterangan || '—'"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </template>

                        <template x-if="dadakanFiltered.length === 0 && akanDatangFiltered.length === 0 && mingguanFiltered.length === 0">
                            <div class="ss"><div class="em">Tidak ada jadwal di tanggal ini</div></div>
                        </template>
                    </div>
                </template>

                {{-- ═══ DEFAULT MODE ═══ --}}
                <template x-if="!searching">
                    <div>
                        {{-- Jadwal Dadakan --}}
                        <template x-if="selected && selected.hasDadakan">
                            <div class="ss">
                                <div class="sh">
                                    <div class="si e">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3" />
                                            <path d="M12 9v4" /><path d="M12 17h.01" />
                                        </svg>
                                    </div>
                                    <span class="sl e">Jadwal Dadakan</span>
                                    <span class="sn e" x-text="selected.jadwalDadakan.length"></span>
                                </div>
                                <template x-for="j in selected.jadwalDadakan" :key="j.judul + j.tanggal_mulai">
                                    <div class="alert-box alert-red">
                                        <div class="alert-title">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3" /><path d="M12 9v4" /><path d="M12 17h.01" /></svg>
                                            Pemberitahuan Khusus
                                        </div>
                                        <div class="alert-p" style="display:flex; flex-direction:column; gap:4px; margin-top:4px;">
                                            <div><span style="color:#64748b">Agenda/Halangan:</span> <strong><span x-text="j.judul"></span></strong></div>
                                            <div>
                                                <span style="color:#64748b">Waktu:</span> <strong><span x-text="j.tanggal_mulai_fmt + (j.tanggal_mulai !== j.tanggal_selesai ? ' s/d ' + j.tanggal_selesai_fmt : '')"></span></strong> 
                                                &bull; 
                                                <template x-if="j.is_fullday">
                                                    <strong style="color:#475569">Seharian Penuh</strong>
                                                </template>
                                                <template x-if="!j.is_fullday">
                                                    <strong><span x-text="(j.jam_mulai||'').substring(0,5) + ' s/d ' + (j.jam_selesai||'').substring(0,5)"></span></strong>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="alert-keterangan" x-show="j.keterangan">Keterangan: <span x-text="j.keterangan"></span></div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- Jadwal Akan Datang --}}
                        <template x-if="selected && selected.hasUpcoming">
                            <div class="ss">
                                <div class="sh">
                                    <div class="si u">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M8 2v4" /><path d="M16 2v4" />
                                            <rect width="18" height="18" x="3" y="4" rx="2" />
                                            <path d="M3 10h18" /><path d="M8 14h.01" /><path d="M12 14h.01" />
                                            <path d="M16 14h.01" /><path d="M8 18h.01" /><path d="M12 18h.01" />
                                        </svg>
                                    </div>
                                    <span class="sl u">Jadwal Akan Datang</span>
                                    <span class="sn u" x-text="selected.jadwalAkanDatang.length"></span>
                                </div>
                                <template x-for="j in selected.jadwalAkanDatang" :key="j.judul + j.tanggal_mulai">
                                    <div class="alert-box alert-blue">
                                        <div class="alert-title">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4" /><path d="M16 2v4" /><rect width="18" height="18" x="3" y="4" rx="2" /><path d="M3 10h18" /><path d="M8 14h.01" /><path d="M12 14h.01" /><path d="M16 14h.01" /><path d="M8 18h.01" /><path d="M12 18h.01" /></svg>
                                            Agenda Terencana
                                        </div>
                                        <div class="alert-p" style="display:flex; flex-direction:column; gap:4px; margin-top:4px;">
                                            <div><span style="color:#64748b">Agenda/Halangan:</span> <strong><span x-text="j.judul"></span></strong></div>
                                            <div>
                                                <span style="color:#64748b">Waktu:</span> <strong><span x-text="j.tanggal_mulai_fmt + (j.tanggal_mulai !== j.tanggal_selesai ? ' s/d ' + j.tanggal_selesai_fmt : '')"></span></strong> 
                                                &bull; 
                                                <template x-if="j.is_fullday">
                                                    <strong style="color:#475569">Seharian Penuh</strong>
                                                </template>
                                                <template x-if="!j.is_fullday">
                                                    <strong><span x-text="(j.jam_mulai||'').substring(0,5) + ' s/d ' + (j.jam_selesai||'').substring(0,5)"></span></strong>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="alert-keterangan" x-show="j.keterangan">Keterangan: <span x-text="j.keterangan"></span></div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- Jadwal Mingguan --}}
                        <template x-if="selected && selected.hasWeekly">
                            <div class="ss">
                                <div class="sh sh-toggle" @click="openW = !openW" role="button" tabindex="0"
                                    @keydown.enter="openW = !openW" :aria-expanded="openW">
                                    <div class="si w">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M8 2v4" /><path d="M16 2v4" />
                                            <rect width="18" height="18" x="3" y="4" rx="2" />
                                            <path d="M3 10h18" />
                                        </svg>
                                    </div>
                                    <span class="sl w">Jadwal Mingguan</span>
                                    <span class="sn w" x-text="selected.jadwalMingguan.length"></span>
                                    <button class="st" :class="openW ? 'open' : ''" tabindex="-1" aria-hidden="true"
                                        style="margin-left:auto;pointer-events:none">
                                        <i class="lucide-chevron-down"></i>
                                    </button>
                                </div>
                                <div x-show="openW" x-cloak style="overflow:hidden" x-transition.duration.250ms>
                                    <div class="table-responsive">
                                        <table class="wt">
                                            <thead>
                                                <tr><th>Hari</th><th>Waktu</th><th>Kegiatan</th><th>Mata Kuliah</th><th>Ruangan</th><th>Keterangan</th></tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="(group, hari) in groupBy(selected.jadwalMingguan, 'hari')" :key="hari">
                                                    <template x-for="(j, jIdx) in group" :key="j.jam_mulai">
                                                        <tr>
                                                            <td class="dy" :style="jIdx < group.length - 1 ? 'border-bottom: none;' : ''" x-text="jIdx === 0 ? hari : ''"></td>
                                                            <td class="tm" x-text="(j.jam_mulai||'').substring(0,5)+' – '+(j.jam_selesai||'').substring(0,5)"></td>
                                                            <td style="white-space: nowrap" x-text="j.kegiatan"></td>
                                                            <td class="sb" x-text="j.mata_kuliah || '—'"></td>
                                                            <td class="rm" x-text="j.ruangan || '—'"></td>
                                                            <td class="rm" style="color:#78716c;" x-text="j.keterangan || '—'"></td>
                                                        </tr>
                                                    </template>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="selected && !selected.hasDadakan && !selected.hasUpcoming && !selected.hasWeekly">
                            <div class="ss"><div class="em">Belum ada jadwal terdaftar</div></div>
                        </template>
                    </div>
                </template>
            </div>{{-- end modal-body --}}
        </div>{{-- end modal-box --}}
    </div>{{-- end modal-overlay --}}

    <footer class="ft">
        <p id="live-indicator" style="display:flex;align-items:center;justify-content:center;gap:6px">
            <span
                style="width:7px;height:7px;border-radius:50%;background:#16a34a;display:inline-block;animation:livePulse 1.5s ease-in-out infinite"></span>
            Status diperbarui otomatis — <span id="next-update">60</span>s
        </p>
        <p style="margin-top:4px">Untuk dosen: <a href="{{ route('login') }}">Login ke Dashboard <i
                    class="lucide-arrow-right" style="font-size:11px"></i></a></p>
        <div class="ln"></div>
        <p>&copy; {{ date('Y') }} Lab Komputer — STMIK Widya Cipta Dharma, Samarinda</p>
    </footer>

    <script>
        // ── Alpine root component ──
        function dosenModal() {
            return {
                showModal: false,
                selected: null,

                openModal(data) {
                    this.selected = data;
                    this.showModal = true;
                    document.body.style.overflow = 'hidden';
                },
                closeModal() {
                    this.showModal = false;
                    document.body.style.overflow = '';
                    setTimeout(() => { this.selected = null; }, 300);
                },
                // Helper groupBy untuk Alpine template
                groupBy(arr, key) {
                    const order = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
                    const map = {};
                    arr.forEach(item => {
                        if (!map[item[key]]) map[item[key]] = [];
                        map[item[key]].push(item);
                    });
                    // Sort by day order
                    const sorted = {};
                    order.forEach(h => { if (map[h]) sorted[h] = map[h]; });
                    Object.keys(map).forEach(h => { if (!sorted[h]) sorted[h] = map[h]; });
                    return sorted;
                }
            };
        }

        // ── Ripple helper ──
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

        // ── Stagger card entrance ──
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.cards .sc').forEach((el, i) => {
                el.style.animationDelay = (i * 0.08 + 0.05) + 's';
            });
        });

        // ══════════════════════════════════════════════
        //  REALTIME STATUS POLLING — setiap 60 detik
        // ══════════════════════════════════════════════
        (function startRealtime() {
            const INTERVAL = 60;
            let countdown = INTERVAL;
            const counter = document.getElementById('next-update');
            setInterval(() => {
                countdown--;
                if (counter) counter.textContent = countdown < 0 ? INTERVAL : countdown;
            }, 1000);

            async function syncStatus() {
                try {
                    const res = await fetch('/api/dosen-status', { cache: 'no-store' });
                    if (!res.ok) return;
                    const data = await res.json();
                    const statusMap = {};
                    data.forEach(d => statusMap[d.nidn] = d.status);

                    document.querySelectorAll('.cards .sc').forEach(card => {
                        const nidn = card.dataset.nidn;
                        const status = statusMap[nidn];
                        if (!status) return;
                        const isOk = status === 'Di Ruangan';
                        card.classList.toggle('ok', isOk);
                        card.classList.toggle('away', !isOk);
                        const bdg = card.querySelector('.bdg');
                        if (bdg) {
                            bdg.className = 'bdg ' + (isOk ? 'ok' : 'away');
                            const dot = bdg.querySelector('.dt');
                            bdg.textContent = status;
                            if (dot) bdg.prepend(dot);
                        }
                    });
                    countdown = INTERVAL;
                    if (counter) counter.textContent = countdown;
                } catch (e) {}
            }
            setInterval(syncStatus, INTERVAL * 1000);
        })();
    </script>

    <style>
        @keyframes livePulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: .4; transform: scale(1.4); }
        }
    </style>
</body>
</html>
