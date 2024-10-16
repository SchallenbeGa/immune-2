<?php

namespace App\Http\Controllers\Htmx;

use App\Support\Helpers;
use App\Models\Employee;
use App\Models\Computer;
use App\Models\EmployeeComputerHistory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();
        
        // Open the file for reading
        $csvFile = fopen($filePath, 'r');
        $header = fgetcsv($csvFile); // Assuming the first row is the header
        
        // Start transaction for atomic database operations
        DB::beginTransaction();

        try {
            // Loop through each row of the CSV file
            while (($row = fgetcsv($csvFile)) !== false) {
                // Extract data based on column positions (adjust indexes according to your CSV format)
                $pcName = $row[2]; // e.g., "pc" column
                $userName = $row[4]; // e.g., "user" column

                // Find or create the user in the users table
                $user = Employee::firstOrCreate(['name' => $userName]);

                // Check if the computer already exists
                $computer = Computer::where('reference', $pcName)->first();

                if (!$computer) {
                    // If the computer doesn't exist, create it and assign the current user
                    $computer = Computer::create([
                        'reference' => $pcName,
                        'employee_id' => $employee->id, // Assuming computers table has user_id to track the current user
                    ]);
                } else {
                    // If the computer already exists, update the current user
                    $computer->user_id = $employee->id;
                    $computer->save();
                }

                // Log the history of user assignment in the user_computer_history table
                UserComputerHistory::create([
                    'computer_id' => $computer->id,
                    'employee_id' => $employee->id,
                    'assigned_at' => now(), // Use current timestamp
                ]);
            }

            // Commit the transaction
            DB::commit();

            return back()->with('success', 'CSV imported successfully!');

        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollback();
            return back()->with('error', 'Failed to import CSV: ' . $e->getMessage());
        } finally {
            fclose($csvFile);
        }
    
        return view('home.partials.import', [
            'oob_swap' => true
        ])
        .view('home.partials.form-message', [
            'message' => 'Successfully updated.',
            'oob_swap' => true
        ])
        .view('components.navbar', ['navbar_active' => 'settings']);
    }
}
