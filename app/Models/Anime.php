<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $formFields)
 */
class Anime extends Model
{
    use HasFactory;

    protected $table = 'table_animes';
    protected $fillable = [
        'title',
        'episodes',
        'description',
        'studio',
        'image',
        'start_aired_date',
        'end_aired_date'
    ];
    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'table_anime_genres', 'anime_id', 'genre_id')->withTimestamps();
        /* belongsToMany means that the relationship is many-to-many.
         * The first argument is the name of the related model.
         * The second argument is the name of the pivot table (the table that is used to store the relationships).
         * The third argument is the foreign key of the model
         * on which you are defining the relationship, while
         * the fourth argument is the foreign key of the model.
         */
    }
    public function scopeFilter($query, $genre)
    {
        return $query->when($genre, function ($query) use ($genre) {
            // This is a conditional statement provided by Laravel's query builder
            // when checks if the $genre variable is not null
            // if its truthy, it executes the join SQL statement using Laravel query builder

            // SQL query which joins the tables
            return $query->join('table_anime_genres', 'table_animes.id', '=', 'table_anime_genres.anime_id')
                ->join('table_genres', 'table_anime_genres.genre_id', '=', 'table_genres.id')
                ->where('table_genres.genre', $genre);

            // if its falsy, it returns the query as is which is select all from table_animes
        }, function ($query) {
            return $query;
        })->select('table_animes.*');

        /*
         * SQL Equivalent:
         * SELECT table_animes.* FROM table_animes
         * JOIN table_anime_genres ON table_animes.id = table_anime_genres.anime_id
         * JOIN table_genres ON table_anime_genres.genre_id = table_genres.id
         * WHERE table_genres.genre = 'Adventure';
         */
    }




    public function scopeSearch($query, $search)
    {
        return $query->where('title', 'like', '%' . $search . '%')
            ->orWhere('description', 'like', '%' . $search . '%');

        /*
         * SQL equivalent
         * SELECT * FROM table_animes WHERE title like '%Frieren%' or description like '%Elf%';
         */
    }
}
