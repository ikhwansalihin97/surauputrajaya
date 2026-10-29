<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Senarai Ahli Jawatankuasa Surau</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h3 { margin: 0; text-transform: uppercase; }
        .header p { margin: 2px 0 0 0; font-size: 10px; color: #555; }
        .meta-table { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .meta-table td { padding: 4px; vertical-align: top; }
        .ajk-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .ajk-table th, .ajk-table td { border: 1px solid #666; padding: 6px; text-align: left; vertical-align: top; }
        .ajk-table th { background-color: #f0f0f0; text-transform: uppercase; font-size: 10px; }
        .badge { display: inline-block; padding: 2px 5px; font-size: 9px; font-weight: bold; background: #e0e0e0; border-radius: 3px; }
        .badge-applicant { background: #d1e7dd; color: #0f5132; }
    </style>
</head>
<body>
    <div class="header">
        <h3>Senarai Ahli Jawatankuasa (AJK) Surau</h3>
        <p>Borang Lampiran Pendaftaran Surau - JAWI</p>
    </div>

    <table class="meta-table">
        <tr>
            <td width="20%"><strong>No. Rujukan</strong></td>
            <td width="30%">: {{ $application->reference_no }}</td>
            <td width="20%"><strong>Nama Surau</strong></td>
            <td width="30%">: {{ $application->nama_surau }}</td>
        </tr>
        <tr>
            <td><strong>Kategori Surau</strong></td>
            <td>: {{ $application->kategori_surau }}</td>
            <td><strong>Tarikh Cetak</strong></td>
            <td>: {{ date('d/m/Y') }}</td>
        </tr>
    </table>

    <table class="ajk-table">
        <thead>
            <tr>
                <th width="5%" style="text-align: center;">Bil.</th>
                <th width="30%">Maklumat Peribadi</th>
                <th width="20%">Jawatan</th>
                <th width="20%">No. Telefon & E-Mel</th>
                <th width="25%">Alamat Rumah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($application->committees as $index => $ajk)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $ajk->nama }}</strong><br>
                        <small>No. KP: {{ $ajk->ic_no }}</small>
                        @if($ajk->is_applicant)
                            <br><span class="badge badge-applicant">Pemohon Utama</span>
                        @endif
                    </td>
                    <td><span class="badge">{{ $ajk->jawatan }}</span></td>
                    <td>
                        Tel: {{ $ajk->no_tel ?? '-' }}<br>
                        E-mel: {{ $ajk->email ?? '-' }}
                    </td>
                    <td>{{ $ajk->alamat ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>