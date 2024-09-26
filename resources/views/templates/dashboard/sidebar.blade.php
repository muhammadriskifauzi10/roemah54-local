<div id="menu">
    <div class="trigger-menu">
        <button type="button" class="btn btn-dark d-flex align-items-center gap-1" id="trigger-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list"
                viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5" />
            </svg>
            <span>Menu</span>
        </button>
    </div>

    <div class="list-menu">
        <div class="list-group border-0">
            <a href="{{ route('dasbor') }}"
                class="list-group-item list-group-item-action border-0 {{ request()->is('dasbor*') ? 'active' : '' }}">
                Dasbor
            </a>
            {{-- <a href="{{ route('lokasi') }}"
                class="list-group-item list-group-item-action border-0 {{ request()->is('lokasi*') ? 'active' : '' }}">
                Daftar Lokasi
            </a> --}}

            {{-- kamar --}}
            <div class="fw-bold my-3">Kamar</div>
            {{-- <a href="{{ route('tipekamar') }}"
                   class="list-group-item list-group-item-action border-0 {{ request()->is('tipekamar*') ? 'active' : '' }}">
                   Tipe Kamar
               </a> --}}
            <a href="{{ route('kamar') }}"
                class="list-group-item list-group-item-action border-0 {{ request()->is('kamar*') ? 'active' : '' }}">
                Daftar Kamar
            </a>
            <a href="{{ route('harga') }}"
                class="list-group-item list-group-item-action border-0 {{ request()->is('harga*') ? 'active' : '' }}">
                Harga Kamar
            </a>
            {{-- <a href="{{ route('asrama.mahasiswa') }}"
                   class="list-group-item list-group-item-action border-0 {{ request()->is('asrama*') ? 'active' : '' }}">
                   Kamar Asrama
               </a> --}}

            {{-- penyewa --}}
            <div class="fw-bold my-3">Penyewa</div>
            <a href="{{ route('daftarpenyewa') }}"
                class="list-group-item list-group-item-action border-0 {{ request()->is('daftarpenyewa*') ? 'active' : '' }}">
                Daftar Penyewa
            </a>
            <a href="{{ route('penyewaankamar') }}"
                class="list-group-item list-group-item-action border-0 {{ request()->is('penyewaankamar*') ? 'active' : '' }}">
                Penyewaan Kamar
            </a>
            {{-- <a href="{{ route('dendacheckout') }}"
                class="list-group-item list-group-item-action border-0 {{ request()->is('dendacheckout*') ? 'active' : '' }}">
                Denda Checkout
            </a> --}}

            {{-- layanan --}}
            {{-- <div class="fw-bold my-3">Layanan</div>
            <a href="{{ route('ritel') }}"
                class="list-group-item list-group-item-action border-0 {{ request()->is('ritel*') ? 'active' : '' }}">
                Jasa / Penjualan Ritel
            </a> --}}

            {{-- laporan --}}
            <div class="fw-bold my-3">Laporan</div>
            <a href="{{ route('transaksi') }}"
                class="list-group-item list-group-item-action border-0 {{ request()->is('transaksi*') ? 'active' : '' }}">
                Transaksi
            </a>

            {{-- inventaris --}}
            {{-- <div class="fw-bold my-3">Inventaris</div>
            <a href="{{ route('inventaris.kategori') }}"
                class="list-group-item list-group-item-action border-0 {{ request()->is('inventaris/kategori*') ? 'active' : '' }}">
                Kategori
            </a>
            <a href="{{ route('inventaris.barang') }}"
                class="list-group-item list-group-item-action border-0 {{ request()->is('inventaris/barang*') ? 'active' : '' }}">
                Barang Inventaris
            </a>
            <a href="{{ route('inventaris.penggunaanbarang') }}"
                class="list-group-item list-group-item-action border-0 {{ request()->is('inventaris/penggunaanbarang*') ? 'active' : '' }}">
                Penggunaan Barang Inventaris
            </a>
            <a href="{{ route('inventaris.log') }}"
                class="list-group-item list-group-item-action border-0 {{ request()->is('inventaris/log*') ? 'active' : '' }}">
                Log Barang Inventaris
            </a> --}}

            {{-- manajemen pengguna --}}
            @if(auth()->user()->can('tambah lokasi'))
                <div class="fw-bold my-3">Manajemen Pengguna</div>
                {{-- <a href="{{ route('role') }}"
                    class="list-group-item list-group-item-action border-0 {{ request()->is('role*') ? 'active' : '' }}">
                    Role
                </a> --}}
                <a href="{{ route('pengguna') }}"
                    class="list-group-item list-group-item-action border-0 {{ request()->is('pengguna*') ? 'active' : '' }}">
                    Pengguna
                </a>
            @endif
        </div>
    </div>
</div>
