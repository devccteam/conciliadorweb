<?php

namespace App\Imports;

use App\Models\ImportRecord;
use App\Models\ImportSession;
use App\Models\Layout;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class RecordsImport implements ToModel, WithStartRow
{
    protected $layout;
    protected $importSession;

    protected $previousDate;
    protected $previousHistory;

    public function __construct(Layout $layout, ImportSession $importSession)
    {
        $this->layout = $layout;
        $this->importSession = $importSession;
    }

    public function startRow(): int
    {
        return $this->layout->start_row;
    }
    
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $layout = $this->layout;

        $date = $this->getColumnValue($row, $layout->date_column);
        $history = $this->getColumnValue($row, $layout->history_column);
        $value = $this->getColumnValue($row, $layout->debit_value_column) ?? $this->getColumnValue($row, $layout->credit_value_column);

        if ($layout->consider_previous_date && !$date) {
            $date = $this->previousDate;
        }

        if ($layout->consider_previous_history && !$history) {
            $history = $this->previousHistory;
        }

        if (!$date || !$history || !$value || $value == 0) {
            return null;
        }

        $this->previousDate = $date;
        $this->previousHistory = $history;

        return new ImportRecord([
            'num_doc' => $this->getColumnValue($row, $layout->num_doc_column),
            'date' => $this->formatDate($date),
            'history' => $history,
            'debit_value' => $this->getColumnValue($row, $layout->debit_value_column),
            'credit_value' => $this->getColumnValue($row, $layout->credit_value_column),
            'interest' => $this->getColumnValue($row, $layout->interest_column),
            'fine' => $this->getColumnValue($row, $layout->fine_column),
            'discounts' => $this->getColumnValue($row, $layout->discounts_column),
            'other_values' => $this->getColumnValue($row, $layout->other_values_column),
            'client_supplier' => $this->getColumnValue($row, $layout->client_supplier_column),
            'bank' => $this->getColumnValue($row, $layout->bank_column),
            'import_session_id' => $this->importSession->id,
            'import_id' => $this->importSession->import_id,
        ]);
    }

    /**
     * Get the value of a column by its name.
     * 
     * @param array $row
     * @param string $columnName
     * 
     * @return mixed
     */
    private function getColumnValue($row, $columnName)
    {
        if (!$columnName) {
            return null;
        }

        $columnIndex = $this->getColumnIndex($columnName);
        return $row[$columnIndex] ?? null;
    }

    /**
     * Convert a column name to a zero-based index.
     * 
     * @param string $columnName
     * 
     * @return int
     */
    private function getColumnIndex($columnName): int
    {
        $columnName = strtoupper($columnName);
        $index = 0;

        for ($i = 0; $i < strlen($columnName); $i++) {
            $index *= 26;
            $index += ord($columnName[$i]) - ord('A') + 1;
        }

        return $index - 1;
    }

    // /**
    //  * Parse a date input string into a Carbon instance.
    //  * 
    //  * @param string $dateInput
    //  * 
    //  * @return Carbon
    //  * 
    //  * @throws Exception
    //  */
    // private function formatDate($dateInput)
    // {
    //     $formats = [
    //         'd-m-Y',   // 03-10-2024
    //         'Y-m-d',   // 2024-10-03
    //         'd/m/Y',   // 03/10/2024
    //         'Y/m/d',   // 2024/10/03
    //         'd-m-Y',   // 03-10-2024
    //         'dmY',     // 03102024
    //         'Ymd',     // 20241003
    //     ];

    //     foreach ($formats as $format) {
    //         try {
    //             $date = Carbon::createFromFormat($format, $dateInput);

    //             if ($date && $date->format($format) === $dateInput) {
    //                 return $date->toDateString();
    //             }
    //         } catch (Exception $e) {
    //             continue;
    //         }
    //     }
        
    //     throw new Exception('Invalid date format.');
    // }

    /**
     * Check if a row is a header row.
     * 
     * @param array $row
     * 
     * @return bool
     */
    private function isHeaderRow(array $row): bool
    {
        return is_string($row[0]) && strtolower($row[0]) === 'documento';
    }

    private function formatDate($dateInput)
    {
        return Carbon::createFromDate(1899, 12, 30)->addDays($dateInput)->toDateString();
    }
}
