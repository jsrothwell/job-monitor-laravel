protected function schedule(Schedule $schedule)
{
    $schedule->command('jobs:scrape')->dailyAt('06:00');
}
