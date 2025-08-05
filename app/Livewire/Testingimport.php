<?php

namespace App\Livewire;

use Illuminate\Http\Request;
use App\Imports\ImportSupplier;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class Testingimport extends Component

{
    use WithFileUploads;
    public $suppliers;

    public function import_file()
    {
            //    dd($this->suppliers);

                try {

                  //**** DELETE ALL FILES IN DIRECTORY  
                    //  $directoryPath = 'temp'; // Example directory path within your configured disk

                    // // Get all files in the specified directory
                    // $files = Storage::allFiles($directoryPath);

                    // // Delete all retrieved files
                    // Storage::delete($files);
                //**** DELETE ALL FILES IN DIRECTORY
                    

                // ###########  UPLOAD EXCEL BERHASIL
                    // Save the uploaded file to a temporary location
                    // $path = $this->suppliers->store('temp');
                 

                    // Import the data using the YourExcelImport class
                    // Excel::import(new ImportSupplier, storage_path('app/' . $path));

                    // Delete the temporary file
                    // Storage::delete('app/temp/');
                // ###########  UPLOAD EXCEL BERHASIL



                //######    CEK LIST FILE DI DIRECTORY
                    $directory = 'temp'; // e.g., 'public/uploads' or 'private/documents'
                    $files = Storage::files($directory);
                    //$files[0] -> cek last file
                    dd($files[0]);

                //######    CEK LIST FILE DI DIRECTORY

                    session()->flash('message', 'Excel data imported successfully.');
                    redirect('/testingimport');
                } catch (\Throwable $th) {
                    //throw $th;
                }


    } 

    public function render()
    {
        
        return view('livewire.testingimport');
    }
}
