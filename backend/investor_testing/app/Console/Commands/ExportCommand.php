<?php /** @noinspection PhpMissingFieldTypeInspection */

/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 19.08.2021
 * Time: 18:49
 */

namespace Newton\InvestorTesting\Console\Commands;

use Throwable;

use Newton\InvestorTesting\Packages\Export\TestExportRepository;

use Illuminate\Console\Command;

class ExportCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'export' .
    '{filePath       : File path}' .
    '{--separator=;  : CSV separator}';

    /**
     * @var string
     */
    protected $description = 'Export data to CSV';

    /**
     * @throws Throwable
     */
    public function handle(
        TestExportRepository $testExportRepository
    ) {
        $rows = $testExportRepository->getPassedTestsIterator();

        $fileName = $this->argument('filePath');
        $fileNameTmp = "{$fileName}.tmp";
        $filePointer = fopen($fileNameTmp, 'w');

        $separator = $this->option('separator');
        $thereIsNoData = true;
        foreach ($rows as $fields) {
            if ($thereIsNoData) {
                fputcsv($filePointer, array_keys($fields->toArray()), $separator);
                $thereIsNoData = false;
            }
            fputcsv($filePointer, $fields->toArray(), $separator);
        }
        fclose($filePointer);

        if ($thereIsNoData) {
            unlink($fileNameTmp);
            $this->warn('there is no data');
            return;
        }

        rename($fileNameTmp, $fileName);

        $this->info('export completed');
    }
}
