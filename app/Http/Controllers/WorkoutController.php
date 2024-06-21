<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkoutRequest;
use App\Http\Requests\UpdateWorkoutRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class WorkoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->role == 'admin') {
            return view('dashboard.workouts.index', [
                'workouts' => Workout::all(),
            ]);
        } else {
            return view('pages.workouts.index', [
                'workouts' => Workout::all(),
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.workouts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkoutRequest $request)
    {
        // Ambil semua data dari request kecuali gambar
        $data = $request->except('image');

        // Cek jika ada file gambar diupload
        if ($request->hasFile('image')) {
            // Ambil file gambar
            $image = $request->file('image');

            // Buat nama file berdasarkan title dan ekstensi asli
            $imageName = str_slug($request->input('name')) . '.' . $image->getClientOriginalExtension();

            // Simpan gambar ke public/assets/images
            $image->move(public_path('assets/images'), $imageName);

            // Tambahkan nama file gambar ke data
            $data['image'] = $imageName;
        }

        // Simpan workout ke database
        Workout::create($data);

        return redirect()->route('workouts.index')->with('success', 'Workout created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Workout $workout)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Workout $workout)
    {
        return view('dashboard.workouts.edit', compact('workout'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(UpdateWorkoutRequest $request, Workout $workout)
    {
        // Ambil semua data yang sudah divalidasi dari request
        $data = $request->validated();

        // Cek jika ada file gambar diupload
        if ($request->hasFile('image')) {
            // Ambil file gambar
            $image = $request->file('image');

            // Buat nama file berdasarkan title dan ekstensi asli
            $imageName = str_slug($request->input('name')) . '.' . $image->getClientOriginalExtension();

            // Hapus gambar lama jika ada
            if ($workout->image) {
                $oldImagePath = public_path('assets/images/' . $workout->image);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            // Simpan gambar baru ke public/assets/images
            $image->move(public_path('assets/images'), $imageName);

            // Tambahkan nama file gambar baru ke data
            $data['image'] = $imageName;
        }

        // Update workout di database dengan data baru
        $workout->update($data);

        return redirect()->route('workouts.index')->with('success', 'Workout updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Workout $workout)
    {
        $workout->program_workouts()->delete();

        $workout->delete();

        return redirect()->route('workouts.index')->with('success', 'Workout deleted.');
    }
}
