<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Dosen — Lab Komputer STMIK Widya Cipta Dharma</title>
    <meta name="description"
        content="Informasi jadwal dan status keberadaan dosen Lab Komputer STMIK Widya Cipta Dharma Samarinda">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lucide-static@latest/font/lucide.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta http-equiv="refresh" content="60">
    <link rel="stylesheet" href="/css/status.css">

</head>

<body>

    <header class="hdr">
        <div class="mx">
            <div class="hdr-in">
                <div class="hdr-l">
                    <div class=""><i class="lucide-calendar-days"></i></div>
                    <div>
                        <h1>Informasi keberadaan Dosen Lab Komputer</h1>
                        <p class="sub">STMIK Widya Cipta Dharma, Samarinda</p>
                    </div>
                </div>
                <div class="hdr-date"><i class="lucide-clock-3"></i> {{ now()->translatedFormat('l, d F Y') }}</div>
            </div>
        </div>
    </header>

    <main class="mx">
        <!-- STATUS CARDS — click to scroll -->
        <div class="cards">
            @foreach($dosenList as $idx => $dosen)
                @php
                    $ok = $dosen->status === 'Di Ruangan';
                    // Siapkan data WA untuk card atas juga
                    $cardPhone = '';
                    $cardPesan = '';
                    if ($dosen->telepon) {
                        $cardPhone = preg_replace('/[^0-9]/', '', $dosen->telepon);
                        if (str_starts_with($cardPhone, '0'))
                            $cardPhone = '62' . substr($cardPhone, 1);
                        $cardPesan = rawurlencode(
                            "Permisi Bapak/Ibu {$dosen->nama},\n\n" .
                            "Saya [Nama Anda] dari prodi [Nama Prodi] kelas [Kelas], mohon maaf mengganggu waktunya.\n\n" .
                            "Saya ingin bertanya mengenai keperluan perkuliahan/bimbingan. Apakah Bapak/Ibu ada waktu untuk saya temui hari ini? Terima kasih banyak sebelumnya."
                        );
                    }
                @endphp
                <div class="sc {{ $ok ? 'ok' : 'away' }}"
                    onclick="document.getElementById('dosen-{{ $idx }}').scrollIntoView({behavior:'smooth',block:'start'})">
                    <div class="bar"></div>
                    <span class="role {{ $dosen->jabatan === 'Kepala Lab' ? 'rk' : 'rs' }}">{{ $dosen->jabatan }}</span>
                    <div class="nm">{{ $dosen->nama }}</div>
                    <div class="nd">NIDN: {{ $dosen->nidn }}</div>
                    {{-- Spacer mendorong badge ke bawah --}}
                    <div style="flex:1; min-height:10px"></div>
                    <div class="bdg {{ $ok ? 'ok' : 'away' }}"><span class="dt"></span>{{ $dosen->status }}</div>
                    <span class="arrow"><i class="lucide-arrow-down"></i></span>
                </div>
            @endforeach
        </div>

        <!-- DOSEN SECTIONS — each with own search -->
        @foreach($dosenList as $idx => $dosen)
            @php
                $ok = $dosen->status === 'Di Ruangan';
                $hasDadakan = $dosen->jadwalDadakan->count() > 0;
                $hasUpcoming = $dosen->jadwalAkanDatang->count() > 0;
                $hasWeekly = $dosen->jadwalMingguan->count() > 0;
                $hariMap = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'];
                // Format tanggal ke Y-m-d agar tidak terkena timezone UTC dari serialisasi Carbon
                $jadwalAkanDatangJs = $dosen->jadwalAkanDatang->map(fn($j) => [
                    'judul' => $j->judul,
                    'tanggal_mulai' => $j->tanggal_mulai->format('Y-m-d'),
                    'tanggal_selesai' => $j->tanggal_selesai->format('Y-m-d'),
                    'is_fullday' => $j->is_fullday,
                    'jam_mulai' => $j->jam_mulai,
                    'jam_selesai' => $j->jam_selesai,
                    'keterangan' => $j->keterangan,
                ]);
                $jadwalDadakanJs = $dosen->jadwalDadakan->map(fn($j) => [
                    'judul' => $j->judul,
                    'tanggal_mulai' => $j->tanggal_mulai->format('Y-m-d'),
                    'tanggal_selesai' => $j->tanggal_selesai->format('Y-m-d'),
                    'is_fullday' => $j->is_fullday,
                    'jam_mulai' => $j->jam_mulai,
                    'jam_selesai' => $j->jam_selesai,
                    'keterangan' => $j->keterangan,
                ]);
            @endphp
            <div class="db" id="dosen-{{ $idx }}" x-data="{
                                                                openW: false,
                                                                cari: '',
                                                                searching: false,
                                                                hariCari: '',
                                                                tglLabel: '',
                                                                mingguanFiltered: [],
                                                                akanDatangFiltered: [],
                                                                dadakanFiltered: [],
                                                                doSearch() {
                                                                    if (!this.cari) { this.searching = false; return; }
                                                                    this.searching = true;
                                                                    let [y,m,dd] = this.cari.split('-').map(Number); let d = new Date(y, m-1, dd);
                                                                    let days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                                                                    this.hariCari = days[d.getDay()];
                                                                    this.tglLabel = this.hariCari + ', ' + dd + ' ' + ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][m-1] + ' ' + y;

                                                                    let ds = this.cari;
                                                                    this.mingguanFiltered = @js($dosen->jadwalMingguan).filter(j => j.hari === this.hariCari);
                                                                    this.akanDatangFiltered = @js($jadwalAkanDatangJs).filter(j => ds >= j.tanggal_mulai && ds <= j.tanggal_selesai);
                                                                    this.dadakanFiltered = @js($jadwalDadakanJs).filter(j => ds >= j.tanggal_mulai && ds <= j.tanggal_selesai);
                                                                },
                                                                resetSearch() { this.cari = ''; this.searching = false; }
                                                            }">
                <!-- Header -->
                <div class="db-top">
                    <div class="di">
                        <div>
                            <div class="nm">{{ $dosen->nama }}</div>
                            <div class="dm">
                                <span
                                    class="role {{ $dosen->jabatan === 'Kepala Lab' ? 'rk' : 'rs' }}">{{ $dosen->jabatan }}</span>
                                <span class="sp"></span>
                                <span>NIDN: {{ $dosen->nidn }}</span>
                            </div>
                        </div>
                    </div>
                    @php
                        // Format nomor telepon untuk WA
                        $waPhone = '';
                        $waPesan = '';
                        if ($dosen->telepon) {
                            $waPhone = preg_replace('/[^0-9]/', '', $dosen->telepon);
                            if (str_starts_with($waPhone, '0'))
                                $waPhone = '62' . substr($waPhone, 1);
                            $waPesan = rawurlencode(
                                "Permisi Bapak/Ibu {$dosen->nama},\n\n" .
                                "Saya [Nama Anda] dari prodi [Nama Prodi] kelas [Kelas], mohon maaf mengganggu waktunya.\n\n" .
                                "Saya ingin bertanya mengenai keperluan perkuliahan/bimbingan. Apakah Bapak/Ibu ada waktu untuk saya temui hari ini? Terima kasih banyak sebelumnya."
                            );
                        }
                    @endphp
                    <div class="db-actions">
                        @if($dosen->telepon)
                            <a href="https://wa.me/{{ $waPhone }}?text={{ $waPesan }}" target="_blank" class="btn-wa"
                                onclick="addRipple(event, this)" title="Hubungi {{ $dosen->nama }} via WhatsApp">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z" />
                                </svg>
                                Hubungi via WA
                            </a>
                        @endif
                        <div class="bdg {{ $ok ? 'ok' : 'away' }}"><span class="dt"></span>{{ $dosen->status }}</div>
                    </div>
                </div>

                <!-- Per-dosen search -->
                <div class="ds-search">
                    <label><i class="lucide-search"></i> Cari jadwal</label>
                    <input type="date" x-model="cari" @change="doSearch()">
                    <button class="btn-sm" @click="doSearch(); addRipple($event, $el)">
                        <i class="lucide-search" style="font-size:11px"></i> Cari
                    </button>
                    <button class="btn-rst" x-show="searching" x-cloak @click="resetSearch()"
                        x-transition.duration.150ms>Reset</button>
                </div>

                <!-- Search result banner -->
                <template x-if="searching">
                    <div class="ds-banner">
                        <i class="lucide-calendar-search"></i> Jadwal di tanggal <strong x-text="tglLabel"></strong>
                    </div>
                </template>

                <!-- ═══ SEARCH MODE ═══ -->
                <template x-if="searching">
                    <div>
                        <!-- Dadakan filtered -->
                        <template x-if="dadakanFiltered.length > 0">
                            <div class="ss">
                                <div class="sh">
                                    <div class="si e"><svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path
                                                d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3" />
                                            <path d="M12 9v4" />
                                            <path d="M12 17h.01" />
                                        </svg></div>
                                    <span class="sl e">Jadwal Dadakan</span>
                                    <span class="sn e" x-text="dadakanFiltered.length"></span>
                                </div>
                                <template x-for="j in dadakanFiltered" :key="j.id">
                                    <div class="ec e">
                                        <div class="et e" x-text="j.judul"></div>
                                        <div class="ed">
                                            <span class="ei e"><i class="lucide-calendar"></i> <span
                                                    x-text="j.tanggal_mulai.substring(0,10)"></span></span>
                                            <span class="ei e"><i class="lucide-clock-3"></i> <span
                                                    x-text="j.is_fullday ? 'Seharian' : (j.jam_mulai||'').substring(0,5)+' – '+(j.jam_selesai||'').substring(0,5)"></span></span>
                                        </div>
                                        <div class="en" x-show="j.keterangan" x-text="j.keterangan"></div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Akan Datang filtered -->
                        <template x-if="akanDatangFiltered.length > 0">
                            <div class="ss">
                                <div class="sh">
                                    <div class="si u"><i class="lucide-calendar-clock"></i></div>
                                    <span class="sl u">Jadwal Akan Datang</span>
                                    <span class="sn u" x-text="akanDatangFiltered.length"></span>
                                </div>
                                <template x-for="j in akanDatangFiltered" :key="j.id">
                                    <div class="ec u">
                                        <div class="et u" x-text="j.judul"></div>
                                        <div class="ed">
                                            <span class="ei u"><i class="lucide-calendar"></i> <span
                                                    x-text="j.tanggal_mulai.substring(0,10)"></span></span>
                                            <span class="ei u"><i class="lucide-clock-3"></i> <span
                                                    x-text="j.is_fullday ? 'Seharian' : (j.jam_mulai||'').substring(0,5)+' – '+(j.jam_selesai||'').substring(0,5)"></span></span>
                                        </div>
                                        <div class="en" x-show="j.keterangan" x-text="j.keterangan"></div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Mingguan filtered -->
                        <template x-if="mingguanFiltered.length > 0">
                            <div class="ss">
                                <div class="sh">
                                    <span class="sl w">Jadwal Mingguan — <span x-text="hariCari"></span></span>
                                    <span class="sn w" x-text="mingguanFiltered.length"></span>
                                </div>
                                <table class="wt">
                                    <thead>
                                        <tr>
                                            <th>Waktu</th>
                                            <th>Kegiatan</th>
                                            <th>Mata Kuliah</th>
                                            <th>Ruangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="j in mingguanFiltered" :key="j.id">
                                            <tr>
                                                <td class="tm"
                                                    x-text="(j.jam_mulai||'').substring(0,5)+' – '+(j.jam_selesai||'').substring(0,5)">
                                                </td>
                                                <td x-text="j.kegiatan"></td>
                                                <td class="sb" x-text="j.mata_kuliah || '—'"></td>
                                                <td class="rm" x-text="j.ruangan || '—'"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </template>

                        <!-- Empty -->
                        <template
                            x-if="dadakanFiltered.length === 0 && akanDatangFiltered.length === 0 && mingguanFiltered.length === 0">
                            <div class="ss">
                                <div class="em">Tidak ada jadwal di tanggal ini</div>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- ═══ DEFAULT MODE ═══ -->
                <template x-if="!searching">
                    <div>
                        @if($hasDadakan)
                            <div class="ss">
                                <div class="sh">

                                    <span class="sl e">Jadwal Dadakan</span>
                                    <span class="sn e">{{ $dosen->jadwalDadakan->count() }}</span>
                                </div>
                                @foreach($dosen->jadwalDadakan as $j)
                                    <div class="ec e">
                                        <div class="et e">{{ $j->judul }}</div>
                                        <div class="ed">
                                            <span class="ei e"><i class="lucide-calendar"></i>
                                                {{ $j->tanggal_mulai->translatedFormat('d F Y') }}{{ $j->tanggal_mulai != $j->tanggal_selesai ? ' – ' . $j->tanggal_selesai->translatedFormat('d F Y') : '' }}</span>
                                            <span class="ei e"><i class="lucide-clock-3"></i>
                                                {{ $j->is_fullday ? 'Seharian' : substr($j->jam_mulai, 0, 5) . ' – ' . substr($j->jam_selesai, 0, 5) }}</span>
                                        </div>
                                        @if($j->keterangan)
                                        <div class="en">{{ $j->keterangan }}</div>@endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($hasUpcoming)
                            <div class="ss">
                                <div class="sh">
                                    <div class="si u"><svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M8 2v4" />
                                            <path d="M16 2v4" />
                                            <rect width="18" height="18" x="3" y="4" rx="2" />
                                            <path d="M3 10h18" />
                                            <path d="M8 14h.01" />
                                            <path d="M12 14h.01" />
                                            <path d="M16 14h.01" />
                                            <path d="M8 18h.01" />
                                            <path d="M12 18h.01" />
                                        </svg></div>
                                    <span class="sl u">Jadwal Akan Datang</span>
                                    <span class="sn u">{{ $dosen->jadwalAkanDatang->count() }}</span>
                                </div>
                                @foreach($dosen->jadwalAkanDatang as $j)
                                    <div class="ec u">
                                        <div class="et u">{{ $j->judul }}</div>
                                        <div class="ed">
                                            <span class="ei u"><i class="lucide-calendar"></i>
                                                {{ $j->tanggal_mulai->translatedFormat('d F Y') }}{{ $j->tanggal_mulai != $j->tanggal_selesai ? ' – ' . $j->tanggal_selesai->translatedFormat('d F Y') : '' }}</span>
                                            <span class="ei u"><i class="lucide-clock-3"></i>
                                                {{ $j->is_fullday ? 'Seharian' : substr($j->jam_mulai, 0, 5) . ' – ' . substr($j->jam_selesai, 0, 5) }}</span>
                                        </div>
                                        @if($j->keterangan)
                                        <div class="en">{{ $j->keterangan }}</div>@endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($hasWeekly)
                            <div class="ss">
                                <div class="sh sh-toggle" @click="openW = !openW; addRipple($event, $el)" role="button"
                                    tabindex="0" @keydown.enter="openW = !openW" :aria-expanded="openW">

                                    <div class="si w"><svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M8 2v4" />
                                            <path d="M16 2v4" />
                                            <rect width="18" height="18" x="3" y="4" rx="2" />
                                            <path d="M3 10h18" />
                                        </svg></div>
                                    <span class="sl w">Jadwal Mingguan</span>
                                    <span class="sn w">{{ $dosen->jadwalMingguan->count() }}</span>
                                    <button class="st" :class="openW ? 'open' : ''" tabindex="-1" aria-hidden="true"
                                        style="margin-left:auto;pointer-events:none">
                                        <i class="lucide-chevron-down"></i>
                                    </button>
                                </div>
                                <div x-show="openW" x-cloak style="overflow:hidden" x-transition.duration.250ms>
                                    <table class="wt">
                                        <thead>
                                            <tr>
                                                <th>Hari</th>
                                                <th>Waktu</th>
                                                <th>Kegiatan</th>
                                                <th>Mata Kuliah</th>
                                                <th>Ruangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dosen->jadwalMingguan->groupBy('hari') as $hari => $items)
                                                @foreach($items as $jIdx => $j)
                                                    <tr>
                                                        @if($jIdx === 0)
                                                        <td class="dy" rowspan="{{ $items->count() }}">{{ $hari }}</td>@endif
                                                        <td class="tm">{{ substr($j->jam_mulai, 0, 5) }} –
                                                            {{ substr($j->jam_selesai, 0, 5) }}
                                                        </td>
                                                        <td>{{ $j->kegiatan }}</td>
                                                        <td class="sb">{{ $j->mata_kuliah ?: '—' }}</td>
                                                        <td class="rm">{{ $j->ruangan ?: '—' }}</td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        @if(!$hasDadakan && !$hasUpcoming && !$hasWeekly)
                            <div class="ss">
                                <div class="em">Belum ada jadwal terdaftar</div>
                            </div>
                        @endif
                    </div>
                </template>
            </div>
        @endforeach
    </main>

    <footer class="ft">
        <p id="live-indicator" style="display:flex;align-items:center;justify-content:center;gap:6px">
            <span
                style="width:7px;height:7px;border-radius:50%;background:#16a34a;display:inline-block;animation:livePulse 1.5s ease-in-out infinite"></span>
            Status diperbarui otomatis — <span id="next-update">10</span>s
        </p>
        <p style="margin-top:4px">Untuk dosen: <a href="{{ route('login') }}">Login ke Dashboard <i
                    class="lucide-arrow-right" style="font-size:11px"></i></a></p>
        <div class="ln"></div>
        <p>&copy; {{ date('Y') }} Lab Komputer — STMIK Widya Cipta Dharma, Samarinda</p>
    </footer>

    <script>
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

        // ── Stagger dosen blocks entrance ──
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.db').forEach((el, i) => {
                el.style.animationDelay = (i * 0.08 + 0.1) + 's';
            });
        });

        // ══════════════════════════════════════════════
        //  REALTIME STATUS POLLING — update setiap 10s
        //  Fetch /api/dosen-status → update badge & card
        //  tanpa full page reload
        // ══════════════════════════════════════════════
        (function startRealtime() {
            const INTERVAL = 60; // detik waktu reload/polling
            let countdown = INTERVAL;

            // Countdown display
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

                    // Buat map nidn → status
                    const statusMap = {};
                    data.forEach(d => statusMap[d.nidn] = d.status);

                    // Update top status CARDS (.sc)
                    document.querySelectorAll('.cards .sc').forEach(card => {
                        const nidnEl = card.querySelector('.nd');
                        if (!nidnEl) return;
                        const nidn = nidnEl.textContent.replace('NIDN:', '').trim();
                        const status = statusMap[nidn];
                        if (!status) return;

                        const isOk = status === 'Di Ruangan';
                        card.classList.toggle('ok', isOk);
                        card.classList.toggle('away', !isOk);

                        const bdg = card.querySelector('.bdg');
                        if (bdg) {
                            bdg.className = 'bdg ' + (isOk ? 'ok' : 'away');
                            // Preserve dot span, update text
                            const dot = bdg.querySelector('.dt');
                            bdg.textContent = status;
                            if (dot) bdg.prepend(dot);
                        }
                    });

                    // Update status badge di detail dosen (.db)
                    document.querySelectorAll('.db').forEach(db => {
                        // Ambil nidn dari elemen .nidn-val atau dari text
                        const nidnEl = db.querySelector('.di .dm span:last-child') ||
                            db.querySelector('[data-nidn]');
                        if (!nidnEl) return;
                        const nidn = (nidnEl.dataset && nidnEl.dataset.nidn)
                            ? nidnEl.dataset.nidn
                            : nidnEl.textContent.replace('NIDN:', '').trim();
                        const status = statusMap[nidn];
                        if (!status) return;

                        const isOk = status === 'Di Ruangan';
                        // Update .st-badge atau .bdg-ok/.bdg-away di detail
                        const stBdg = db.querySelector('.st-badge, .st-bdg');
                        if (stBdg) {
                            stBdg.className = 'st-badge ' + (isOk ? 'ok' : 'away');
                            const dot = stBdg.querySelector('span');
                            stBdg.textContent = status;
                            if (dot) stBdg.prepend(dot);
                        }
                    });

                    countdown = INTERVAL;
                    if (counter) counter.textContent = countdown;

                } catch (e) {
                    // Gagal polling — tidak crash, akan coba lagi berikutnya
                }
            }

            // Jalankan pertama kali setelah 10 detik, lalu setiap 10 detik
            setInterval(syncStatus, INTERVAL * 1000);
        })();
    </script>
    <style>
        @keyframes livePulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: .4;
                transform: scale(1.4);
            }
        }
    </style>
</body>

</html>