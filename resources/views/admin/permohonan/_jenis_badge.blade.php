{{-- Lencana jenis permohonan: dipaparkan hanya bagi permohonan KEMASKINI maklumat surau berdaftar --}}
@if($application->isRevision())
    <span class="badge rounded-pill align-middle ms-1"
          style="background-color:#fef3c7; color:#92400e; border:1px solid #fde68a; font-size:0.65rem; letter-spacing:0.04em;"
          title="Kemaskini maklumat bagi surau berdaftar{{ $application->parent ? ' (' . $application->parent->reference_no . ')' : '' }}">
        <i class="fa-solid fa-pen-to-square me-1"></i>KEMASKINI
    </span>
@endif
