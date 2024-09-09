<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CompareAddresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compare-addresses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compare addresses from two text files and output matched addresses as CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file1 = storage_path('app/files/client data.txt');
        $file2 = storage_path('app/files/listing data.txt');
        $outputFile = storage_path('app/matched-data.csv');

        $addresses1 = file($file1, FILE_IGNORE_NEW_LINES);
        $addresses2 = file($file2, FILE_IGNORE_NEW_LINES);

        $matchedAddresses = [];

        foreach ($addresses1 as $address1) {
            foreach ($addresses2 as $address2) {
                $distance = levenshtein($address1, $address2);
                $threshold = 3;
                if ($distance <= $threshold) {
                    $matchedAddresses[] = [
                        'Address 1' => $address1,
                        'Address 2' => $address2,
                        'Matched' => ($distance == 0) ? 'Exact' : 'Similar',
                    ];
                }
            }
        }
        $this->outputCsv($outputFile, $matchedAddresses);
    }


    /**
     * Output the matched addresses as CSV.
     *
     * @param string $outputFile
     * @param array $matchedAddresses
     */

    private function outputCsv($outputFile, $matchedAddresses)
    {
        $fp = fopen($outputFile, 'w');
        fputcsv($fp, ['Address 1', 'Address 2', 'Matched']);

        foreach ($matchedAddresses as $address) {
            fputcsv($fp, $address);
        }

        fclose($fp);
    }
}
