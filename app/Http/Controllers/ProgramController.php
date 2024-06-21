<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProgramRequest;
use App\Http\Requests\UpdateProgramRequest;
use App\Models\ProgramWorkout;
use Illuminate\Support\Facades\Auth;
use App\Models\Workout;
use Illuminate\Support\Facades\DB;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->role == 'admin') {
            return view('dashboard.programs.index', [
                'programs' => Program::all(),
            ]);
        } else {
            return view('pages.programs.index', [
                'programs' => Program::all(),
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Auth::user()->role == 'admin') {
            $workouts = Workout::all();
            return view('dashboard.programs.create', compact('workouts'));
        } else {
            $workouts = Workout::all();
            return view('pages.programs.create', compact('workouts'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProgramRequest $request)
    {
        DB::beginTransaction();

        try {
            // Membuat entri baru di tabel 'programs' dan menyimpan data yang terkait
            $program = Program::create([
                'title' => $request->title,
                'description' => $request->description,
                'duration' => $request->duration,
                'user_id' => $request->user_id,
            ]);

            // Menyimpan setiap workout yang terkait dengan program
            foreach ($request->workouts as $workout) {
                ProgramWorkout::create([
                    'program_id' => $program->id,
                    'workout_id' => $workout['id'],
                    'reps' => $workout['reps'],
                    'sets' => $workout['sets'],
                ]);
            }

            DB::commit();

            return redirect()->route('programs.index')->with('success', 'Program created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            // Kembalikan user ke halaman create dengan pesan error dan input sebelumnya
            return redirect()->route('programs.create')->with('error', 'Program failed to create: ' . $e->getMessage())->withInput();
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(Program $program)
    {
        if (Auth::user()->role == 'admin') {
            $program = Program::with(['user', 'program_workouts.workout'])->findOrFail($program->id);

            return view('dashboard.programs.show', compact('program'));
        } else {
            $program = Program::with(['user', 'program_workouts.workout'])->findOrFail($program->id);

            return view('pages.programs.show', compact('program'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program)
    {
        $program = Program::with('program_workouts.workout')->findOrFail($program->id);
        $workouts = Workout::all();
        return view('dashboard.programs.edit', compact('program', 'workouts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProgramRequest $request, Program $program)
    {
        $request->validated();

        DB::beginTransaction();

        try {

            $program->update([
                'title' => $request->title,
                'description' => $request->description,
                'duration' => $request->duration,
                'user_id' => $request->user_id,
            ]);

            $program->program_workouts()->delete();

            foreach ($request->workouts as $workout) {
                ProgramWorkout::create([
                    'program_id' => $program->id,
                    'workout_id' => $workout['id'],
                    'reps' => $workout['reps'],
                    'sets' => $workout['sets'],
                ]);
            }

            DB::commit();

            return redirect()->route('programs.index')->with('success', 'Program updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('programs.edit', $program->id)->with('error', 'Program failed to update: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program)
    {
        $program->program_workouts()->delete();
        $program->delete();

        return redirect()->route('programs.index')->with('success', 'Program deleted successfully');
    }
}
