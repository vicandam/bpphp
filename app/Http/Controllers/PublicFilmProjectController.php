<?php

namespace App\Http\Controllers;

use App\Models\FilmProject;
use Illuminate\Http\Request;

class PublicFilmProjectController extends Controller
{
    /**
     * Display a listing of the film projects (public view).
     */
    public function index()
    {
        $filmProjects = FilmProject::all();
        return view('public.film_projects.index', compact('filmProjects'));
    }

    /**
     * Display the specified film project.
     */
    public function show(FilmProject $filmProject)
    {
        $filmProject->load('investments.user'); // Load investors
        return view('public.film_projects.show', compact('filmProject'));
    }
}
