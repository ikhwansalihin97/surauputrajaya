{{-- Medan seorang AJK. $prefix cth. "utama[0]" atau "ajk[2]"; $row = nilai semasa --}}
<div class="row g-2">
    <div class="col-md-5">
        <label class="form-label small fw-semibold mb-1">Nama Penuh <span class="text-danger">*</span></label>
        <input type="text" name="{{ $prefix }}[nama]" class="form-control form-control-sm" value="{{ $row['nama'] }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label small fw-semibold mb-1">No. K/P <span class="text-danger">*</span></label>
        <input type="text" name="{{ $prefix }}[ic_no]" inputmode="numeric" pattern="[0-9]*" maxlength="12" data-digits-only class="form-control form-control-sm" value="{{ $row['ic_no'] }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-semibold mb-1">No. Telefon <span class="text-danger">*</span></label>
        <input type="text" name="{{ $prefix }}[no_tel]" inputmode="numeric" pattern="[0-9]*" data-digits-only class="form-control form-control-sm" value="{{ $row['no_tel'] }}" required>
    </div>
    <div class="col-md-8">
        <label class="form-label small fw-semibold mb-1">Alamat</label>
        <input type="text" name="{{ $prefix }}[alamat]" class="form-control form-control-sm" value="{{ $row['alamat'] }}">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-semibold mb-1">E-mel</label>
        <input type="email" name="{{ $prefix }}[email]" class="form-control form-control-sm" value="{{ $row['email'] }}">
    </div>
</div>
