@if(getAuthLevel()=="super-admin")
<li class="nav-item">
    <div class="form-group">
        <select class="custom-select custom-select-sm custom-select" id="filter_level">
          <option value="gudang-umum">Gudang Umum</option>
          <option value="pembelian">Pembelian</option>
          <option value="direksi">Direksi</option>
        </select>
      </div>
</li>
@endif
