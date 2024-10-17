<?php

namespace App\Http\Controllers\Htmx;

use App\Support\Helpers;
use App\Models\Employee;
use App\Models\Computer;
use App\Models\EmployeeComputerHistory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HTMXImportController extends Controller
{
    public function import(Request $request)
    {
        if (auth()->guest()) {
            return Helpers::redirectToSignIn();
        }
        $jsonData = json_decode(file_get_contents($request->file('json_file')), true);
        
        foreach ($jsonData['pc_list'] as $pcData) {
            // Vérifier si l'utilisateur existe déjà, sinon le créer
            $employee = Employee::firstOrCreate(
                ['name' => $pcData['employee']]
            );

            // Vérifier si le PC existe déjà, sinon le créer ou le mettre à jour
            $comp = Computer::updateOrCreate(
                ['reference' => $pcData['pc']], // Critère pour identifier la machine
                ['employee_id' => $employee->id]        // Mise à jour des informations
            );

            EmployeeComputerHistory::updateOrCreate([
                'computer_id' => $comp->id,
                'employee_id' => $employee->id,
                'assigned_at' => now(),
            ]);
        }
        $existingReferences = Computer::pluck('reference')->toArray();

        $newReferences = array_map(function($pcData) {
            return $pcData['pc'];
        }, $jsonData['pc_list']);
        
        // Supprimer les PCs qui ne sont plus dans la nouvelle liste
        Computer::whereNotIn('reference', $newReferences)->delete();
        return view('home.partials.import', [
            'oob_swap' => true
        ])
        .view('home.partials.form-message', [
            'message' => 'Successfully updated.',
            'oob_swap' => true
        ])
        .view('components.navbar', ['navbar_active' => 'settings']);
    }
    public function importCsv(Request $request)
    {
        if (auth()->guest()) {
            return Helpers::redirectToSignIn();
        }
        // Read the CSV file
    $file = $request->file('csv_file');
    $filePath = $file->getRealPath();
    // Preprocess the CSV file to remove null bytes and unwanted characters
    $fileContent = file_get_contents($filePath);
    $fileContent = preg_replace('/\x00/', '', $fileContent);  // Remove null bytes

    // Save the cleaned content back to a temporary file for processing
    $tempFilePath = sys_get_temp_dir() . '/cleaned_csv.csv';
    file_put_contents($tempFilePath, $fileContent);

    // Open the cleaned CSV file for reading
    $csvFile = fopen($tempFilePath, 'r');
    $header = fgetcsv($csvFile);
    // Create arrays to track existing and new references
    $newReferences = [];

    // Begin transaction for safe insert/update
    DB::beginTransaction();

    try {
        // Process each row of the CSV
        while (($row = fgetcsv($csvFile)) !== false) {
            // Extract 'pc' and 'employee' values from each row
            $pcReference = $row[2];  // Assuming the 'pc' reference is in the first column
            $employeeName = $row[4]; // Assuming the 'employee' name is in the second column
            $garantie = $row[6];
            $localisation = $row[5];
            $achat = $row[7];
            $garantie_end = $row[8];
        
            // Store the reference for comparison later
            $newReferences[] = $pcReference;

            // Check if the employee already exists, otherwise create a new one
            $employee = Employee::firstOrCreate(
                ['name' => $employeeName]
            );

            // Check if the computer already exists, otherwise create or update it
            $computer = Computer::updateOrCreate(
                ['reference' => $pcReference,
            'localisation' => $localisation,
        'garantie' => $garantie,
    'date_achat' => $achat,
'date_fin_garantie' => $garantie_end],
                ['employee_id' => $employee->id]  // Update employee assignment
            );

            // Log the history in the employee_computer_history table
            EmployeeComputerHistory::updateOrCreate([
                'computer_id' => $computer->id,
                'employee_id' => $employee->id,
                'assigned_at' => now(),
            ]);
        }

        // Fetch all existing references from the database
        $existingReferences = Computer::pluck('reference')->toArray();

        // Remove PCs that are no longer present in the new list (i.e., delete those not in the CSV)
        Computer::whereNotIn('reference', $newReferences)->delete();

        // Commit the transaction
        DB::commit();

        // Return a success message with the updated view
        return view('home.partials.import', [
            'oob_swap' => true
        ])
        .view('home.partials.form-message', [
            'message' => 'Successfully updated.',
            'oob_swap' => true
        ]);

    } catch (\Exception $e) {
        // Rollback the transaction in case of any failure
        DB::rollback();
        dd($e);
        // Handle the error (you can also add logging here)
        return back()->with('error', 'Error during CSV import: ' . $e->getMessage());
    } finally {
        // Close the file
        fclose($csvFile);
    }
}
}
