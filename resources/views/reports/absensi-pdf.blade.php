<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Absensi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #444; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .text-red { color: red; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Absensi Guru</h1>
        <p>Periode: {{ $monthName }} {{ $year }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25%; text-align: left;">Nama Guru</th>
                <th style="width: 15%; text-align: left;">NIDN</th>
                <th style="width: 10%;">Sakit</th>
                <th style="width: 10%;">Izin</th>
                <th style="width: 10%;">Hadir</th>
                <th style="width: 15%;">Tepat Waktu</th>
                <th style="width: 15%;">Telat</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporanData as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['nidn'] ?? '-' }}</td>
                    <td class="text-center">{{ $row['total_sakit'] ?: '-' }}</td>
                    <td class="text-center">{{ $row['total_izin'] ?: '-' }}</td>
                    <td class="text-center" style="font-weight: bold;">{{ $row['total_hadir'] }}</td>
                    <td class="text-center">{{ $row['total_tepat_waktu'] }}</td>
                    <td class="text-center {{ $row['total_telat'] > 0 ? 'text-red' : '' }}">
                        {{ $row['total_telat'] ?: '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>