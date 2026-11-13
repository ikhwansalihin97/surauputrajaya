@php $surau = $mesyuarat->sesi->surau; @endphp
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Surat Jemputan Mesyuarat Agung Tahunan</title>
    <style>
        @page { margin: 1.6cm 2.2cm 1.4cm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5pt; line-height: 1.45; color: #111; }
    </style>
</head>
<body>
    {{-- Kepala surat --}}
    <div style="text-align:center; border-bottom:2px solid #111; padding-bottom:8px; margin-bottom:14px;">
        <div style="font-size:14pt; font-weight:bold; text-transform:uppercase;">{{ $surau->nama_surau }}</div>
        @if($surau->alamat_surat || $surau->alamat_surau)
            <div style="font-size:9.5pt;">{{ $surau->alamat_surat ?: $surau->alamat_surau }}</div>
        @endif
        @if($surau->no_tel_surau)
            <div style="font-size:9.5pt;">Tel: {{ $surau->no_tel_surau }}</div>
        @endif
    </div>

    <div style="text-align:right; margin-bottom:14px;">{{ now()->locale('ms')->translatedFormat('j F Y') }}</div>

    @include('surau.agm._surat-jemputan')
</body>
</html>
