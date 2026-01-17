<?php

namespace App\Console\Commands;

use App\Models\Badge;
use App\Models\VisitRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixVisitData extends Command
{
    protected $signature = 'visit:fix';
    protected $description = 'Fix inconsistent visit data';

    public function handle()
    {
        $this->info('Checking for inconsistent data...');
        
        // Find checked_in visits without badge assignment
        $problematicVisits = VisitRequest::where('status', 'checked_in')
            ->doesntHave('badgeAssignment')
            ->get();
            
        $this->info("Found {$problematicVisits->count()} checked-in visits without badge assignment");
        
        if ($problematicVisits->count() > 0) {
            $this->table(
                ['ID', 'Visitor', 'Division', 'Created At'],
                $problematicVisits->map(function ($visit) {
                    return [
                        $visit->id,
                        $visit->visitor->full_name,
                        $visit->division->division_name ?? 'N/A',
                        $visit->created_at->format('Y-m-d H:i'),
                    ];
                })->toArray()
            );
            
            if ($this->confirm('Do you want to fix these visits?')) {
                DB::beginTransaction();
                
                try {
                    foreach ($problematicVisits as $visit) {
                        // Find available badge
                        $availableBadge = Badge::where('status', 'available')->first();
                        
                        if ($availableBadge) {
                            // Create badge assignment
                            DB::table('badge_assignments')->insert([
                                'visit_request_id' => $visit->id,
                                'badge_id' => $availableBadge->id,
                                'assigned_by' => 7, // Default security user
                                'assigned_at' => now()->subHours(rand(1, 3)),
                                'returned_at' => null,
                            ]);
                            
                            // Update badge status
                            $availableBadge->update(['status' => 'in_use']);
                            
                            // Create visit log
                            DB::table('visit_logs')->insert([
                                'visit_request_id' => $visit->id,
                                'action' => 'check_in',
                                'performed_by' => 7,
                                'timestamp' => now()->subHours(rand(1, 3)),
                                'notes' => 'Auto-fixed by system',
                            ]);
                            
                            $this->info("Fixed visit ID: {$visit->id} with badge: {$availableBadge->badge_code}");
                        } else {
                            // No available badges, change status to registered
                            $visit->update(['status' => 'registered']);
                            $this->warn("No available badges. Changed visit ID: {$visit->id} status to 'registered'");
                        }
                    }
                    
                    DB::commit();
                    $this->info('Data fixed successfully!');
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error('Failed to fix data: ' . $e->getMessage());
                }
            }
        } else {
            $this->info('No inconsistent data found.');
        }
        
        return Command::SUCCESS;
    }
}