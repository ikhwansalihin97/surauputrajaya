<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationAppointment;
use Carbon\Carbon;

class CalendarInviteService
{
    /**
     * Dapatkan masa mula dan tamat dalam zon waktu Asia/Kuala_Lumpur
     */
    public static function getAppointmentDateTimes(ApplicationAppointment $appointment): array
    {
        $tarikh = $appointment->tarikh_temujanji instanceof Carbon
            ? $appointment->tarikh_temujanji->format('Y-m-d')
            : Carbon::parse($appointment->tarikh_temujanji)->format('Y-m-d');

        $masa = $appointment->masa_temujanji instanceof Carbon
            ? $appointment->masa_temujanji->format('H:i:s')
            : Carbon::parse($appointment->masa_temujanji)->format('H:i:s');

        $start = Carbon::parse("{$tarikh} {$masa}", 'Asia/Kuala_Lumpur');
        // Anggaran tempoh lawatan tapak adalah 1 jam
        $end = $start->copy()->addHour();

        return [$start, $end];
    }

    /**
     * Jana kandungan fail .ics (RFC 5545)
     */
    public static function generateIcs(Application $application, ApplicationAppointment $appointment): string
    {
        [$start, $end] = self::getAppointmentDateTimes($appointment);

        $dtStamp = Carbon::now('UTC')->format('Ymd\THis\Z');
        $dtStart = $start->copy()->setTimezone('UTC')->format('Ymd\THis\Z');
        $dtEnd   = $end->copy()->setTimezone('UTC')->format('Ymd\THis\Z');

        $uid = 'temujanji-' . $appointment->id . '-' . $application->id . '@esurau.jawi.gov.my';
        $summary = 'Lawatan Tapak Surau - ' . $application->nama_surau . ' (' . $application->reference_no . ')';
        $location = $appointment->tempat ?? 'Lokasi Tapak Surau';

        $rawDescription = "Temujanji Lawatan Tapak Permohonan Surau\n"
            . "No. Rujukan: " . $application->reference_no . "\n"
            . "Nama Surau: " . $application->nama_surau . "\n"
            . "Pemohon: " . $application->pemohon_nama . "\n"
            . "Lokasi: " . $location . "\n"
            . ($appointment->catatan ? "Catatan: " . $appointment->catatan . "\n" : "")
            . "\nDianjurkan oleh Bahagian Pengurusan Masjid & Surau JAWI.";

        // Escape aksara khas mengikut spesifikasi RFC 5545
        $escapedSummary = self::escapeIcsText($summary);
        $escapedLocation = self::escapeIcsText($location);
        $escapedDescription = self::escapeIcsText($rawDescription);

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Sistem e-Surau JAWI//Lawatan Tapak//MY',
            'CALSCALE:GREGORIAN',
            'METHOD:REQUEST',
            'BEGIN:VEVENT',
            'UID:' . $uid,
            'DTSTAMP:' . $dtStamp,
            'DTSTART:' . $dtStart,
            'DTEND:' . $dtEnd,
            'SUMMARY:' . $escapedSummary,
            'DESCRIPTION:' . $escapedDescription,
            'LOCATION:' . $escapedLocation,
            'STATUS:CONFIRMED',
            'ORGANIZER;CN="JAWI":mailto:no-reply@jawi.gov.my',
            'SEQUENCE:0',
            'TRANSP:OPAQUE',
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        return implode("\r\n", $lines) . "\r\n";
    }

    /**
     * Jana pautan terus Google Calendar untuk pengguna web / telefon
     */
    public static function generateGoogleCalendarUrl(Application $application, ApplicationAppointment $appointment): string
    {
        [$start, $end] = self::getAppointmentDateTimes($appointment);

        $dates = $start->copy()->setTimezone('UTC')->format('Ymd\THis\Z')
            . '/'
            . $end->copy()->setTimezone('UTC')->format('Ymd\THis\Z');

        $title = 'Lawatan Tapak Surau - ' . $application->nama_surau . ' (' . $application->reference_no . ')';
        $location = $appointment->tempat ?? 'Lokasi Tapak Surau';

        $details = "Temujanji Lawatan Tapak Permohonan Surau\n"
            . "No. Rujukan: " . $application->reference_no . "\n"
            . "Nama Surau: " . $application->nama_surau . "\n"
            . "Pemohon: " . $application->pemohon_nama . "\n"
            . "Lokasi: " . $location . "\n"
            . ($appointment->catatan ? "Catatan: " . $appointment->catatan . "\n" : "")
            . "\nBahagian Pengurusan Masjid & Surau JAWI";

        $params = [
            'action'   => 'TEMPLATE',
            'text'     => $title,
            'dates'    => $dates,
            'details'  => $details,
            'location' => $location,
        ];

        return 'https://calendar.google.com/calendar/render?' . http_build_query($params);
    }

    /**
     * Escape aksara khas untuk teks iCal
     */
    private static function escapeIcsText(string $text): string
    {
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace(';', '\;', $text);
        $text = str_replace(',', '\,', $text);
        $text = str_replace(["\r\n", "\n", "\r"], '\n', $text);

        return $text;
    }
}
