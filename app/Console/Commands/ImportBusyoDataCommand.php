<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BusyoData;
use Illuminate\Support\Facades\Log;  // Log ファサードを追加

class ImportBusyoDataCommand extends Command
{
    protected $signature = 'app:import-busyo-data-command';
    protected $description = 'Import busyo-data';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        BusyoData::truncate();

        $csv_path = storage_path('app/public/csv/Sample_Busyo_Data.csv');
        $converted_csv_path = storage_path('app/public/csv/Sample_Busyo_Data_utf8.csv');
        
        // ファイルの内容を取得
        $content = file_get_contents($csv_path);
        
        // エンコーディングを確認して必要に応じて変換
        $encoding = mb_detect_encoding($content, "UTF-8, SJIS-win, EUC-JP", true);
        if ($encoding != "UTF-8") {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }
        
        // 変換した内容を新しいファイルに書き出す
        file_put_contents($converted_csv_path, $content);

        
        
        // CSVファイルを開く
        $file = new \SplFileObject($converted_csv_path);
        $file->setFlags(\SplFileObject::READ_CSV);  // CSVファイルとして読み込む
        $file->setCsvControl(',', '"', '\\');       // デリミタ、エンクロージャ、エスケープキャラクタを設定

        // CSVを配列に変換し、2行目から処理する
        $rows = explode("\n", $content);
        array_shift($rows); // 1行目を削除
        
        foreach ($rows as $row) {
            // 空行の場合はスキップ
            if (empty($row)) {
                continue;
            }
            
            // CSVを配列に変換
            $data = str_getcsv($row);
        
            // データの処理
            if (!empty($data[0])) { // 部署名が空でない場合のみ処理
                \App\Models\BusyoData::create([
                    'busyo_name' => $data[0],
                    'busyo_number' => $data[1] ?? null // 部署コードがない場合は NULL をセット
                ]);
            }
        }
    }
}
