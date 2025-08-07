<!-- Kontrol Tabel Atas -->
<div class="d-flex justify-content-between mb-2 align-items-center flex-wrap">
    <div class="mb-2">
        <label>
            Show
            <select id="entriesPerPage" class="form-select d-inline-block w-auto" onchange="loadCustomers()">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
            </select>
            entries
        </label>
    </div>

    <div class="mb-2">
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" id="searchInput" class="form-control" placeholder="Cari Data...">
        </div>
    </div>
</div>
