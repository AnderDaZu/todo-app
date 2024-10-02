<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $field = 'created_at';
        $order = 'desc';

        if ( $request->sort )
        {
            $sorts = [
                'created_at', '-created_at', 'due_date', '-due_date'
            ];

            if ( in_array($request->sort, $sorts) )
            {
                if ( str_contains($request->sort, '-') )
                {
                    $field = str_replace('-', '', $request->sort);
                    $order = 'desc';
                } else {
                    $field = $request->sort;
                    $order = 'asc';
                }
            }
        }

        $notes = Note::with('tags')->orderBy($field, $order)->get();

        return response()->json([
            'message' => 'ok',
            'data' => $notes,
            'total' => $notes->count(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validateData($request);

        $tags = [];

        foreach ($request->tags ?? [] as $tag) {
            $tag = Tag::firstOrCreate([
                'name' => $tag
            ]);
            $tags[] = $tag->id;
        }

        $note = Note::create([
            'title' => $request->title,
            'description' => $request->description,
            // 'user_id' => $request->user_id,
            'user_id' => Auth::user()->id,
        ]);

        $note->tags()->attach($tags);

        if ( $request->hasFile('image') )
        {
            $note->image_path = $request->image->store('notes');
            $note->save();
        }

        if( $request->due_date )
        {
            $note->due_date = $request->due_date;
            $note->save();
        }

        return response()->json([
            'message' => 'Nota creada con exito.',
            'data' => $note
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        // $note = Note::with('tags')->findOrFail($id);

        return response()->json([
            'message' => 'ok',
            'data' => $note->load('tags')
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        $this->validateData($request);

        // Actualizar datos
        $note->update([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date ?? $note->due_date,
        ]);

        // Actualizar etiquetas
        $tags = [];
        foreach ($request->tags ?? [] as $tag) {
            $tag = Tag::firstOrCreate([
                'name' => $tag
            ]);
            $tags[] = $tag->id;
        }
        $note->tags()->sync($tags);

        // Actualizar imagen
        if ( $request->hasFile('image') )
        {
            $this->updateImage($note, $request->file('image'));
        }

        return response()->json([
            'message' => 'Nota actualizada con exito.',
            'data' => $note->load('tags')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        if ( $note->image_path && Storage::exists($note->image_path) )
        {
            Storage::delete($note->image_path);
        }
        
        $note->delete();

        return response()->json([
            'message' => 'Nota eliminada con exito.',
            'data' => $note
        ], 200);
    }

    private function updateImage(Note $note, $image)
    {
        Storage::delete($note->image_path);
        $note->image_path = $image->store('notes');
        $note->save();
    }

    private function validateData($request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:2',
            'tags' => 'required|array',
            'image' => 'nullable|image|max:2048',
            'due_date' => 'nullable|date',
        ]);

        if ( $validator->fails() ) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos.',
                'errors' => $validator->errors(),
            ], 422);
        }
    }
}
