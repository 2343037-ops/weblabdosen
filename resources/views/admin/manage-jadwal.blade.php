@extends('layouts.app')
@section('title', 'Kelola Jadwal - ' . $dosen->nama)

@push('styles')
    <link rel="stylesheet" href="/css/dashboard.css">
    <style>
        .admin-hero { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 24px; display: flex; align-items: center; gap: 16px; border-left: 4px solid #3b82f6;}
        .admin-hero-icon { width: 48px; height: 48px; border-radius: 50%; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold;}
    </style>
@endpush

@section('content')

    <div class="admin-hero">
        <div class="admin-hero-icon">
            <i class="lucide-user"></i>
        </div>
        <div>
            <div style="font-size: 13px; font-weight: 600; color: #64748b; letter-spacing: 0.5px; text-transform: uppercase;">Kelola Jadwal Dosen</div>
            <h2 style="font-size: 22px; margin: 4px 0 2px;">{{ $dosen->nama }}</h2>
            <div style="font-size: 13px; color: #64748b;">NIDN: {{ $dosen->nidn ?: '-' }} | Email: {{ $dosen->email }}</div>
        </div>
    </div>

    @if(session('success'))
        <div class="status-msg" style="margin-bottom:20px"><i class="lucide-check-circle"></i> {{ session('success') }}</div>
    @endif

    {{-- MODAL MANAGER --}}
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
                <button class="btn-add" @click="openModal('mingguanAdd')">
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
                                                    @click="editData = { id: {{ $j->id }}, hari: '{{ $j->hari }}', jam_mulai: '{{ substr($j->jam_mulai, 0, 5) }}', jam_selesai: '{{ substr($j->jam_selesai, 0, 5) }}', kegiatan: '{{ $j->kegiatan }}', mata_kuliah: '{{ addslashes($j->mata_kuliah) }}', ruangan: '{{ addslashes($j->ruangan) }}', keterangan: '{{ addslashes($j->keterangan) }}' }; openModal('mingguanEdit')" title="Edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" /><path d="m15 5 4 4" /></svg>
                                                </button>
                                                <form method="POST" action="{{ route('admin.jadwal.mingguan.destroy', [$dosen->id, $j->id]) }}" onsubmit="return confirm('Hapus jadwal ini?')" title="Hapus">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn-del">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18" /><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" /><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" /></svg>
                                                    </button>
                                                </form>
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
                </div>
                <div style="display:flex; gap:8px">
                    <button type="button" @click="openModal('riwayatAkanDatang')" style="position:relative; overflow:hidden; background:#fff; border:1px solid #e2e8f0; color:#475569; border-radius:10px; font-weight:600; font-size:13.5px; padding:0 14px; cursor:pointer; display:flex; align-items:center; gap:5px; box-shadow:0 1px 2px rgba(0,0,0,0.05); height:36px; transition:all 0.2s">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/></svg>
                        Riwayat
                    </button>
                    <button class="btn-add" @click="openModal('akanDatangAdd')">
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
                                <div style="font-size:13px; color:#334155">
                                    Pada tanggal <strong>{{ $j->tanggal_mulai->translatedFormat('d F Y') }}{{ $j->tanggal_mulai != $j->tanggal_selesai ? ' s/d ' . $j->tanggal_selesai->translatedFormat('d F Y') : '' }}</strong>, Dosen memiliki agenda luar kampus / berhalangan hadir: <strong>{{ $j->judul }}</strong>
                                    @if(!$j->is_fullday)
                                        (dari pukul <strong>{{ substr($j->jam_mulai, 0, 5) }}</strong> s/d <strong>{{ substr($j->jam_selesai, 0, 5) }}</strong>).
                                    @endif
                                </div>
                                @if($j->keterangan)
                                    <div style="font-size:12.5px; color:#64748b; font-style:italic; border-top:1px dashed #bae6fd; padding-top:6px; margin-top:4px;">
                                        Keterangan: {{ $j->keterangan }}
                                    </div>
                                @endif
                            </div>
                            <div class="act-btns">
                                <button type="button" class="btn-edit" @click="editData = { id: {{ $j->id }}, judul: '{{ addslashes($j->judul) }}', tanggal_mulai: '{{ $j->tanggal_mulai->format('Y-m-d') }}', tanggal_selesai: '{{ $j->tanggal_selesai->format('Y-m-d') }}', jam_mulai: '{{ $j->jam_mulai ? substr($j->jam_mulai,0,5) : '' }}', jam_selesai: '{{ $j->jam_selesai ? substr($j->jam_selesai,0,5) : '' }}', keterangan: '{{ addslashes($j->keterangan) }}'}; editFullday = {{ $j->is_fullday?'true':'false' }}; openModal('akanDatangEdit')" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" /><path d="m15 5 4 4" /></svg>
                                </button>
                                <form method="POST" action="{{ route('admin.jadwal.akan-datang.destroy', [$dosen->id, $j->id]) }}" onsubmit="return confirm('Hapus jadwal ini?')" title="Hapus">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-del">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18" /><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" /><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" /></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="sec-empty">Belum ada jadwal</div>
                @endif
            </div>
        </div>

        <!-- ═══ JADWAL DADAKAN ═══ -->
        <div class="sec">
            <div class="sec-head">
                <div class="sec-title">
                    <span class="sec-label">Jadwal Dadakan</span>
                </div>
                <div style="display:flex; gap:8px">
                    <button type="button" @click="openModal('riwayatDadakan')" style="position:relative; overflow:hidden; background:#fff; border:1px solid #e2e8f0; color:#475569; border-radius:10px; font-weight:600; font-size:13.5px; padding:0 14px; cursor:pointer; display:flex; align-items:center; gap:5px; box-shadow:0 1px 2px rgba(0,0,0,0.05); height:36px; transition:all 0.2s">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/></svg>
                        Riwayat
                    </button>
                    <button class="btn-add" @click="openModal('dadakanAdd')">
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
                                <button type="button" class="btn-edit" @click="editData = { id: {{ $j->id }}, judul: '{{ addslashes($j->judul) }}', jam_selesai: '{{ $j->jam_selesai ? substr($j->jam_selesai, 0, 5) : '' }}', keterangan: '{{ addslashes($j->keterangan) }}' }; editFullday = {{ $j->is_fullday ? 'true' : 'false' }}; openModal('dadakanEdit')" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" /><path d="m15 5 4 4" /></svg>
                                </button>
                                <form method="POST" action="{{ route('admin.jadwal.dadakan.destroy', [$dosen->id, $j->id]) }}" onsubmit="return confirm('Hapus jadwal ini?')" title="Hapus">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-del">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18" /><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" /><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" /></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="sec-empty">Belum ada jadwal</div>
                @endif
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════ --}}
        {{-- MODAL TAMBAH & EDIT (Shorthand) --}}
        
        <!-- Modal Tambah Mingguan -->
        <div x-show="activeModal === 'mingguanAdd'" x-cloak class="modal-bg" @click.self="closeModal()">
            <div class="modal" @click.stop>
                <div class="modal-head"><h3>Tambah Jadwal Mingguan</h3><button @click="closeModal()" class="modal-close"><i class="lucide-x"></i></button></div>
                <form method="POST" action="{{ route('admin.jadwal.mingguan.store', $dosen->id) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="fg"><label>Hari</label><select name="hari" required><option value="">Pilih hari...</option>@foreach(['Senin','Selasa','Rabu','Kamis','Jumat'] as $h)<option value="{{ $h }}">{{ $h }}</option>@endforeach</select></div>
                        <div class="fg-row"><div class="fg"><label>Jam Mulai</label><input type="time" name="jam_mulai" required></div><div class="fg"><label>Jam Selesai</label><input type="time" name="jam_selesai" required></div></div>
                        <div class="fg"><label>Kegiatan</label><select name="kegiatan" required><option value="Mengajar">Mengajar</option><option value="Istirahat">Istirahat</option><option value="Rapat">Rapat</option><option value="Luar Kampus">Luar Kampus</option></select></div>
                        <div class="fg"><label>Mata Kuliah</label><input type="text" name="mata_kuliah"></div><div class="fg"><label>Ruangan</label><input type="text" name="ruangan"></div>
                    </div>
                    <div class="modal-foot"><button type="button" @click="closeModal()" class="btn-cancel">Batal</button><button type="submit" class="btn btn-dark">Simpan</button></div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Mingguan -->
        <div x-show="activeModal === 'mingguanEdit'" x-cloak class="modal-bg" @click.self="closeModal()">
            <div class="modal" @click.stop>
                <div class="modal-head"><h3>Edit Jadwal Mingguan</h3><button @click="closeModal()" class="modal-close"><i class="lucide-x"></i></button></div>
                <form method="POST" :action="'/admin/dosen/{{ $dosen->id }}/jadwal-mingguan/' + editData.id">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <div class="fg"><label>Hari</label><select name="hari" required x-model="editData.hari">@foreach(['Senin','Selasa','Rabu','Kamis','Jumat'] as $h)<option value="{{ $h }}">{{ $h }}</option>@endforeach</select></div>
                        <div class="fg-row"><div class="fg"><label>Jam Mulai</label><input type="time" name="jam_mulai" required x-model="editData.jam_mulai"></div><div class="fg"><label>Jam Selesai</label><input type="time" name="jam_selesai" required x-model="editData.jam_selesai"></div></div>
                        <div class="fg"><label>Kegiatan</label><select name="kegiatan" required x-model="editData.kegiatan"><option value="Mengajar">Mengajar</option><option value="Istirahat">Istirahat</option><option value="Rapat">Rapat</option><option value="Luar Kampus">Luar Kampus</option></select></div>
                        <div class="fg"><label>Mata Kuliah</label><input type="text" name="mata_kuliah" x-model="editData.mata_kuliah"></div><div class="fg"><label>Ruangan</label><input type="text" name="ruangan" x-model="editData.ruangan"></div>
                    </div>
                    <div class="modal-foot"><button type="submit" class="btn btn-dark">Simpan</button></div>
                </form>
            </div>
        </div>

        <!-- Tambah Akan Datang -->
        <div x-show="activeModal === 'akanDatangAdd'" x-cloak class="modal-bg" @click.self="closeModal()" x-data="{ fullday: true }">
            <div class="modal" @click.stop>
                <div class="modal-head"><h3>Tambah Jadwal Akan Datang</h3><button @click="closeModal()" class="modal-close"><i class="lucide-x"></i></button></div>
                <form method="POST" action="{{ route('admin.jadwal.akan-datang.store', $dosen->id) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="fg"><label>Agenda / Halangan</label><input type="text" name="judul" required placeholder="Misal: Rapat, Sakit, dsb"></div>
                        <div class="fg-row"><div class="fg"><label>Tgl Mulai</label><input type="date" name="tanggal_mulai" required></div><div class="fg"><label>Tgl Selesai</label><input type="date" name="tanggal_selesai" required></div></div>
                        <div class="fg-check"><input type="checkbox" id="fad" x-model="fullday" checked><input type="hidden" name="is_fullday" :value="fullday ? 1 : 0"><label for="fad">Seharian Penuh</label></div>
                        <div class="fg-row" x-show="!fullday" x-cloak><div class="fg"><label>Jam Mulai</label><input type="time" name="jam_mulai"></div><div class="fg"><label>Jam Selesai</label><input type="time" name="jam_selesai"></div></div>
                        <div class="fg">
                            <label>Keterangan <span style="font-weight:400; color:#94a3b8; font-size:12px">(opsional)</span></label>
                            <textarea name="keterangan" rows="3" placeholder="Misal: Saya sedang bertugas keluar atau sedang sakit..."></textarea>
                        </div>
                    </div>
                    <div class="modal-foot"><button type="submit" class="btn btn-dark">Simpan</button></div>
                </form>
            </div>
        </div>

        <!-- Edit Akan Datang -->
        <div x-show="activeModal === 'akanDatangEdit'" x-cloak class="modal-bg" @click.self="closeModal()">
            <div class="modal" @click.stop>
                <div class="modal-head"><h3>Edit Jadwal Akan Datang</h3><button @click="closeModal()" class="modal-close"><i class="lucide-x"></i></button></div>
                <form method="POST" :action="'/admin/dosen/{{ $dosen->id }}/jadwal-akan-datang/' + editData.id">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <div class="fg"><label>Agenda / Halangan</label><input type="text" name="judul" required x-model="editData.judul"></div>
                        <div class="fg-row"><div class="fg"><label>Tgl Mulai</label><input type="date" name="tanggal_mulai" required x-model="editData.tanggal_mulai"></div><div class="fg"><label>Tgl Selesai</label><input type="date" name="tanggal_selesai" required x-model="editData.tanggal_selesai"></div></div>
                        <div class="fg-check"><input type="checkbox" id="fae" x-model="editFullday"><input type="hidden" name="is_fullday" :value="editFullday ? 1 : 0"><label for="fae">Seharian Penuh</label></div>
                        <div class="fg-row" x-show="!editFullday" x-cloak><div class="fg"><label>Jam Mulai</label><input type="time" name="jam_mulai" x-model="editData.jam_mulai"></div><div class="fg"><label>Jam Selesai</label><input type="time" name="jam_selesai" x-model="editData.jam_selesai"></div></div>
                        <div class="fg">
                            <label>Keterangan <span style="font-weight:400; color:#94a3b8; font-size:12px">(opsional)</span></label>
                            <textarea name="keterangan" x-model="editData.keterangan" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-foot"><button type="submit" class="btn btn-dark">Simpan</button></div>
                </form>
            </div>
        </div>
        
        <!-- Tambah Dadakan -->
        <div x-show="activeModal === 'dadakanAdd'" x-cloak class="modal-bg" @click.self="closeModal()" x-data="{ fullday: true }">
            <div class="modal" @click.stop>
                <div class="modal-head"><h3>Tambah Jadwal Dadakan (Hari Ini)</h3><button @click="closeModal()" class="modal-close"><i class="lucide-x"></i></button></div>
                <form method="POST" action="{{ route('admin.jadwal.dadakan.store', $dosen->id) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="fg"><label>Agenda / Halangan</label><input type="text" name="judul" required placeholder="Misal: Macet, Sakit Mendadak"></div>
                        <div class="fg-check"><input type="checkbox" id="fdd" x-model="fullday" checked><input type="hidden" name="is_fullday" :value="fullday ? 1 : 0"><label for="fdd">Seharian Penuh</label></div>
                        <div class="fg-row" x-show="!fullday" x-cloak><div class="fg"><label>Jam Selesai (Diperkirakan)</label><input type="time" name="jam_selesai"></div></div>
                        <div class="fg">
                            <label>Keterangan Tambahan <span style="font-weight:400; color:#94a3b8; font-size:12px">(opsional)</span></label>
                            <textarea name="keterangan" rows="3" placeholder="Misal: Saya akan terlambat karena macet..."></textarea>
                        </div>
                    </div>
                    <div class="modal-foot"><button type="submit" class="btn btn-dark">Simpan</button></div>
                </form>
            </div>
        </div>

        <!-- Edit Dadakan -->
        <div x-show="activeModal === 'dadakanEdit'" x-cloak class="modal-bg" @click.self="closeModal()">
            <div class="modal" @click.stop>
                <div class="modal-head"><h3>Edit Jadwal Dadakan</h3><button @click="closeModal()" class="modal-close"><i class="lucide-x"></i></button></div>
                <form method="POST" :action="'/admin/dosen/{{ $dosen->id }}/jadwal-dadakan/' + editData.id">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <div class="fg"><label>Agenda / Halangan</label><input type="text" name="judul" required x-model="editData.judul"></div>
                        <div class="fg-check"><input type="checkbox" id="fde" x-model="editFullday"><input type="hidden" name="is_fullday" :value="editFullday ? 1 : 0"><label for="fde">Seharian Penuh</label></div>
                        <div class="fg-row" x-show="!editFullday" x-cloak><div class="fg"><label>Jam Selesai (Diperkirakan)</label><input type="time" name="jam_selesai" x-model="editData.jam_selesai"></div></div>
                        <div class="fg">
                            <label>Keterangan Tambahan <span style="font-weight:400; color:#94a3b8; font-size:12px">(opsional)</span></label>
                            <textarea name="keterangan" x-model="editData.keterangan" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-foot"><button type="submit" class="btn btn-dark">Simpan</button></div>
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

    </div>
@endsection
