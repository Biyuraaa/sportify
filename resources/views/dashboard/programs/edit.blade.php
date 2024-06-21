@extends('layouts.template')
@section('content')
<div class="container mt-5">
    <h1 class="mb-4 text-white">Edit Program</h1>

    <div class="mb-3">
        <a href="{{ route('programs.index') }}" class="btn btn-primary">Back</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="text-white">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('programs.update', $program->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="title" class="form-label text-white">Program Name</label>
            <input type="text" id="title" name="title" class="form-control" value="{{ $program->title }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label text-white">Program Description</label>
            <textarea id="description" name="description" class="form-control" rows="3" required>{{ $program->description }}</textarea>
        </div>

        <div class="mb-3">
            <label for="duration" class="form-label text-white">Program Duration</label>
            <input type="number" id="duration" name="duration" class="form-control" value="{{ $program->duration }}" required>
        </div>
        <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
        <div id="workout-container">
            @foreach($program->program_workouts as $index => $program_workout)
                <div class="mb-3 workout">
                    <label for="workout-{{ $index }}" class="form-label text-white">Workout {{ $index + 1 }}</label>
                    <select id="workout-{{ $index }}" name="workouts[{{ $index }}][id]" class="form-select" required>
                        @foreach($workouts as $workout)
                            <option value="{{ $workout->id }}" {{ $workout->id == $program_workout->workout_id ? 'selected' : '' }}>{{ $workout->name }}</option>
                        @endforeach
                    </select>
                    <div class="row mt-2">
                        <div class="col">
                            <label for="reps-{{ $index }}" class="form-label text-white">Reps</label>
                            <input type="number" id="reps-{{ $index }}" name="workouts[{{ $index }}][reps]" class="form-control" value="{{ $program_workout->reps }}" required>
                        </div>
                        <div class="col">
                            <label for="sets-{{ $index }}" class="form-label text-white">Sets</label>
                            <input type="number" id="sets-{{ $index }}" name="workouts[{{ $index }}][sets]" class="form-control" value="{{ $program_workout->sets }}" required>
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger mt-2 remove-workout">Remove Workout</button>
                </div>
            @endforeach
        </div>

        <button type="button" id="add-workout" class="btn btn-secondary mb-3">Add Workout</button>
        <button type="submit" class="btn btn-primary mb-3">Update Program</button>
    </form>
</div>

<script>
    document.getElementById('add-workout').addEventListener('click', function() {
        var workoutContainer = document.getElementById('workout-container');
        var workoutCount = workoutContainer.getElementsByClassName('workout').length;

        var newWorkout = document.createElement('div');
        newWorkout.className = 'mb-3 workout';

        var newLabel = document.createElement('label');
        newLabel.htmlFor = 'workout-' + workoutCount;
        newLabel.textContent = 'Workout ' + (workoutCount + 1);
        newLabel.className = 'form-label text-white';

        var newSelect = document.createElement('select');
        newSelect.id = 'workout-' + workoutCount;
        newSelect.name = 'workouts[' + workoutCount + '][id]';
        newSelect.className = 'form-select';
        newSelect.required = true;

        @foreach($workouts as $workout)
            var option = document.createElement('option');
            option.value = "{{ $workout->id }}";
            option.textContent = "{{ $workout->name }}";
            newSelect.appendChild(option);
        @endforeach

        var repsLabel = document.createElement('label');
        repsLabel.htmlFor = 'reps-' + workoutCount;
        repsLabel.textContent = 'Reps';
        repsLabel.className = 'form-label text-white';

        var repsInput = document.createElement('input');
        repsInput.type = 'number';
        repsInput.id = 'reps-' + workoutCount;
        repsInput.name = 'workouts[' + workoutCount + '][reps]';
        repsInput.className = 'form-control text-black';
        repsInput.required = true;

        var setsLabel = document.createElement('label');
        setsLabel.htmlFor = 'sets-' + workoutCount;
        setsLabel.textContent = 'Sets';
        setsLabel.className = 'form-label text-white';

        var setsInput = document.createElement('input');
        setsInput.type = 'number';
        setsInput.id = 'sets-' + workoutCount;
        setsInput.name = 'workouts[' + workoutCount + '][sets]';
        setsInput.className = 'form-control text-black';
        setsInput.required = true;

        var rowDiv = document.createElement('div');
        rowDiv.className = 'row mt-2';

        var colDiv1 = document.createElement('div');
        colDiv1.className = 'col';

        var colDiv2 = document.createElement('div');
        colDiv2.className = 'col';

        colDiv1.appendChild(repsLabel);
        colDiv1.appendChild(repsInput);

        colDiv2.appendChild(setsLabel);
        colDiv2.appendChild(setsInput);

        rowDiv.appendChild(colDiv1);
        rowDiv.appendChild(colDiv2);

        newWorkout.appendChild(newLabel);
        newWorkout.appendChild(newSelect);
        newWorkout.appendChild(rowDiv);

        workoutContainer.appendChild(newWorkout);
    });
document.querySelectorAll('.remove-workout').forEach(function(button) {
    button.addEventListener('click', function() {
        this.parentElement.remove();
    });
});

</script>
@endsection
