<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudioRequest;
use App\Http\Requests\UpdateStudioRequest;
use App\Models\Studio;
use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StudioController extends Controller
{
    use AuthorizesRequests;

    /**
     * Listado público de estudios.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Studio::class);

        $studios = Studio::query()
            ->with(['owner', 'tags'])
            ->where('is_active', true)
            ->latest()
            ->paginate(9);

        return view('studios.index', [
            'studios' => $studios,
        ]);
    }

    /**
     * Formulario de creación.
     */
    public function create(): View
    {
        $this->authorize('create', Studio::class);

        return view('studios.create', [
            'tags' => Tag::query()->orderBy('name', 'asc')->get(),
        ]);
    }

    /**
     * Guardar nuevo estudio.
     */
    public function store(StoreStudioRequest $request): RedirectResponse
    {
        $studio = $request->user()->studios()->create($request->validatedData());

        $studio->tags()->sync($request->validatedTags());

        return redirect()
            ->route('studios.show', $studio)
            ->with('status', 'Estudio creado correctamente.');
    }

    /**
     * Detalle del estudio.
     */
    public function show(Studio $studio): View
    {
        $this->authorize('view', $studio);

        $studio->load([
            'owner',
            'tags',
            'studioSessions' => fn ($query) => $query
                ->with('booker')
                ->latest('starts_at')
                ->limit(5),
        ]);

        return view('studios.show', [
            'studio' => $studio,
        ]);
    }

    /**
     * Formulario de edición.
     */
    public function edit(Studio $studio): View
    {
        $this->authorize('update', $studio);

        $studio->load('tags');

        return view('studios.edit', [
            'studio' => $studio,
            'tags' => Tag::query()->orderBy('name', 'asc')->get(),
            'selectedTags' => $studio->tags->pluck('id')->all(),
        ]);
    }

    /**
     * Actualizar estudio.
     */
    public function update(UpdateStudioRequest $request, Studio $studio): RedirectResponse
    {
        $studio->fill($request->validatedData());
        $studio->save();

        $studio->tags()->sync($request->validatedTags());

        return redirect()
            ->route('studios.show', $studio)
            ->with('status', 'Estudio actualizado correctamente.');
    }

    /**
     * Borrado lógico del estudio.
     */
    public function destroy(Studio $studio): RedirectResponse
    {
        $this->authorize('delete', $studio);

        Studio::destroy($studio->getKey());

        return redirect()
            ->route('studios.index')
            ->with('status', 'Estudio eliminado correctamente.');
    }
}
