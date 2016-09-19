@extends('layouts.master')

@section('title')
    Trending quotes
@endsection

@section('styles')
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1" crossorigin="anonymous">
@endsection

@section('content')
    @if (!empty(Request::segment(1)))
        <section class="filter-bar">
            Установлен фильтр по автору! <a href="{{ route('index') }}">Показать все заметки</a>
        </section>
    @endif
    @if (count($errors))
        <section class="info-box fail">
                @foreach ($errors->all() as $error)
                    <ul>
                        <li>{{ $error }}</li>
                    </ul>
                @endforeach
        </section>
    @endif
    @if (Session::has('success'))
        <section class="info-box success">
            {{ Session::get('success') }}
        </section>
    @endif
    <section class="quotes">
        <h1>Последние заметки</h1>
        @for ($i = 0; $i < count($quotes); $i++)
            <article class="quote{{ $i % 3 === 0 ? ' first-in-line' : (($i + 1) % 3 === 0 ? ' last-in-line' : '') }}">
                <div class="delete">
                    <a href="{{ route('delete', ['quote_id' => $quotes[$i]->id]) }}">
                        <span class="fa fa-times"></span>
                    </a>
                </div>
                {{ $quotes[$i]->quote }}
                <div class="info">
                    Created by <a href="{{ route('index', ['author' => $quotes[$i]->author->name]) }}">
                        {{ $quotes[$i]->author->name }}
                    </a> on {{ $quotes[$i]->created_at }}
                </div>
            </article>
        @endfor
        <div class="pagination">
            @if ($quotes->currentPage() !== 1)
                <a href="{{ $quotes->previousPageUrl() }}">
                    <span class="class fa fa-chevron-left"></span>
                </a>
            @endif
            @if ($quotes->currentPage() !== $quotes->lastPage() && $quotes->hasPages())
                    <a href="{{ $quotes->nextPageUrl() }}">
                        <span class="class fa fa-chevron-right"></span>
                    </a>
            @endif
        </div>
    </section>
    <section class="edit-quote">
        <h1>Добавить заметку</h1>
        <form method="post" action="{{ route('create') }}">
            <div class="input-group">
                <label for="author">Ваше имя</label>
                <input type="text" name="author" id="author" placeholder="Ваше имя">
            </div>
            <div class="input-group">
                <label for="quote">Заметка</label>
                <textarea name="quote" id="quote" rows="5" placeholder="Заметка"></textarea>
            </div>
            <button type="submit" class="btn">Добавить</button>
            <input type="hidden" name="_token" value="{{ Session::token() }}">
        </form>
    </section>
@endsection