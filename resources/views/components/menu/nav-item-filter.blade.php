@if(getAuthLevel()=="super-admin")
{{-- <li class="nav-item">
    <div class="form-group"> --}}

        <select class="custom-select custom-select-sm custom-select bg-primary btn-navbar" id="filter_level">
          <option value="gudang-umum">Gudang Umum</option>
          <option value="pembelian">Pembelian</option>
          <option value="direksi">Direksi</option>
        </select>
      {{-- </div>
</li> --}}
@else
    <div class="input-group input-group-sm">
        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
            <button class="btn btn-navbar" type="submit">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
@endif
