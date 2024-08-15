<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container-fluid">
        <a class="navbar-brand yellow fw-bold logo" href="{{ route('dasbor') }}">Roemah 54</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
            aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
            <div class="btn-group me-2">
                <button type="button" class="dropdown-toggle fw-bold text-light" data-bs-toggle="dropdown"
                    aria-expanded="false" style="background-color: transparent;">
                    {{ auth()->user()->username }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item text-center"
                            href="{{ route('pengguna.detailpengguna', encrypt(auth()->user()->id)) }}">Lihat Profil</a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-100 btn btn-link text-danger text-decoration-none fw-bold">
                                Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            <div class="btn-group">
                @if (DB::table('lokasis')->where('jenisruangan_id', 2)->where('status', 0)->get()->count() > 0)
                    <a href="{{ route('sewa') }}" class="btn btn-danger fw-bold d-flex align-items-center justify-content-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                            class="bi bi-plus-lg" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                        </svg>
                        Sewa Kamar</a>
                @endif
            </div>
        </div>
    </div>
</nav>
