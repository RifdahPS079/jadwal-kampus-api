protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {

        Jadwal::where('status', 'batal')
            ->whereNotNull('batas_ganti')
            ->where('batas_ganti', '<=', now())
            ->update([
                'status' => 'aktif',
                'dibatalkan_pada' => null,
                'batas_ganti' => null,
            ]);

    })->daily();
}