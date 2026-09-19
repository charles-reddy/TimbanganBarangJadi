<div>
    @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif

    @if (session('message'))
        <div class="alert alert-success" role="alert">{{ session('message') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($spmID)
        <section class="mb-4 p-3 bg-body rounded shadow-sm">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h3 class="h4 mb-1">Revisi Bukti Pengecekan Karung</h3>
                    <p class="text-muted mb-0">Pilih hanya foto yang perlu diganti. Foto lainnya tidak akan berubah.</p>
                </div>
                <button type="button" class="btn btn-outline-secondary" wire:click="batal">Kembali ke daftar</button>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">SPM</label>
                    <input type="text" class="form-control" value="{{ $spmNo }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nomor Polisi</label>
                    <input type="text" class="form-control" value="{{ $carID }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Driver</label>
                    <input type="text" class="form-control" value="{{ $driver }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Customer</label>
                    <input type="text" class="form-control" value="{{ $custName }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Item</label>
                    <input type="text" class="form-control" value="{{ $itemName }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">DO</label>
                    <input type="text" class="form-control" value="{{ $doNo }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Qty Karung</label>
                    <input type="text" class="form-control" value="{{ $b10QtyKarung }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Rata-rata Berat</label>
                    <input type="text" class="form-control" value="{{ $avgKarung }}" disabled>
                </div>
            </div>

            <form wire:submit="simpanRevisi">
                <div class="row g-3">
                    @foreach (range(1, 3) as $nomor)
                        @php
                            $fotoBaru = $this->{'buktiAppKarung' . $nomor};
                            $fotoSebelumnya = $fotoLama[$nomor] ?? null;
                        @endphp
                        <div class="col-lg-4" wire:key="bukti-karung-{{ $spmID }}-{{ $nomor }}">
                            <div class="card h-100">
                                <div class="card-header">
                                    <strong>Bukti Cek Ulang {{ $nomor }}</strong>
                                </div>
                                <div class="card-body">
                                    <p class="fw-semibold mb-2">Foto sebelumnya</p>
                                    @if ($fotoSebelumnya)
                                        <img class="img-thumbnail w-100 mb-3"
                                            style="height: 280px; object-fit: contain;"
                                            src="{{ asset('storage/' . ltrim($fotoSebelumnya, '/')) }}"
                                            alt="Foto sebelumnya Bukti Cek Ulang {{ $nomor }}">
                                    @else
                                        <div class="border rounded bg-light d-flex align-items-center justify-content-center text-muted mb-3"
                                            style="height: 280px;">
                                            Belum ada foto
                                        </div>
                                    @endif

                                    <label for="buktiAppKarung{{ $nomor }}" class="form-label fw-semibold">Foto
                                        pengganti</label>
                                    <input id="buktiAppKarung{{ $nomor }}" type="file"
                                        class="form-control @error('buktiAppKarung' . $nomor) is-invalid @enderror"
                                        accept="image/png,image/jpeg" wire:model="buktiAppKarung{{ $nomor }}">
                                    @error('buktiAppKarung' . $nomor)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <div wire:loading wire:target="buktiAppKarung{{ $nomor }}"
                                        class="text-primary mt-2">
                                        Mengunggah foto...
                                    </div>

                                    @if ($fotoBaru)
                                        <p class="fw-semibold mt-3 mb-2">Preview pengganti</p>
                                        <img class="img-thumbnail w-100" style="height: 280px; object-fit: contain;"
                                            src="{{ $fotoBaru->temporaryUrl() }}"
                                            alt="Preview pengganti Bukti Cek Ulang {{ $nomor }}">
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                        wire:target="simpanRevisi,buktiAppKarung1,buktiAppKarung2,buktiAppKarung3">
                        Simpan Revisi
                    </button>
                    <button type="button" class="btn btn-secondary" wire:click="batal">Batal</button>
                </div>
            </form>
        </section>
    @else
        <section class="p-3 bg-body rounded shadow-sm">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
                <div>
                    <h3 class="h4 mb-1">Bukti Pengecekan Karung Sudah Diupload</h3>
                    <p class="text-muted mb-0">Hanya transaksi hari ini dan kemarin yang dapat direvisi.</p>
                </div>
                <div style="min-width: 280px;">
                    <label for="pencarian-transaksi" class="form-label">Cari SPM atau nomor polisi</label>
                    <input id="pencarian-transaksi" type="search" class="form-control"
                        placeholder="Masukkan SPM atau nomor polisi" wire:model.live.debounce.400ms="katakunci">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal SPM</th>
                            <th>SPM</th>
                            <th>Driver</th>
                            <th>Nomor Polisi</th>
                            <th>Customer</th>
                            <th>Item</th>
                            <th>Qty Karung</th>
                            <th>Rata-rata</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksi as $key => $item)
                            <tr wire:key="transaksi-bukti-karung-{{ $item->id }}">
                                <td>{{ $transaksi->firstItem() + $key }}</td>
                                <td class="text-nowrap">
                                    {{ \Carbon\Carbon::parse($item->tglSpm)->format('d-m-Y H:i') }}</td>
                                <td>{{ $item->spmNo }}</td>
                                <td>{{ $item->driver }}</td>
                                <td>{{ $item->carID }}</td>
                                <td>{{ $item->custName }}</td>
                                <td>{{ $item->itemName }}</td>
                                <td>{{ $item->b10QtyKarung }}</td>
                                <td>{{ number_format($item->avgKarung, 2) }}</td>
                                <td class="text-nowrap">
                                    <button type="button" class="btn btn-primary btn-sm"
                                        wire:click="pilihTransaksi({{ $item->id }})">
                                        Revisi Foto
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">Transaksi dengan bukti lengkap
                                    tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $transaksi->links() }}
        </section>
    @endif
</div>
