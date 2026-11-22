{{-- Jadual AJK (baca sahaja). Perlukan $ajk --}}
<div class="table-responsive">
    <table class="table table-sm align-middle mb-0 small">
        <thead class="table-light text-muted text-uppercase">
            <tr>
                <th class="ps-3">Jawatan</th>
                <th>Nama</th>
                <th>No. K/P</th>
                <th>No. Telefon</th>
                <th>E-mel</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ajk as $c)
                <tr>
                    <td class="ps-3"><span class="badge bg-light text-dark border">{{ $c->jawatan }}</span></td>
                    <td class="fw-semibold">{{ $c->nama }}</td>
                    <td class="font-monospace">{{ $c->ic_no }}</td>
                    <td>{{ $c->no_tel }}</td>
                    <td>{{ $c->email ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-3">Tiada AJK direkodkan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
