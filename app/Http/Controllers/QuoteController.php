<?php

namespace App\Http\Controllers;

use App\Author;
use App\Quote;
use Illuminate\Http\Request;

use App\Http\Requests;

class QuoteController extends Controller
{
    public function getIndex($author = null)
    {
        if (!is_null($author)) {
            $quote_author = Author::where('name', $author)->first();
            if ($quote_author) {
                $quotes = $quote_author->quotes()->orderBy('created_at', 'desc')->paginate(6);
            }
        } else {
            $quotes = Quote::orderBy('created_at', 'desc')->paginate(6);
        }

        return view('index', ['quotes' => $quotes]);
    }

    public function postQuote(Request $request)
    {
        $rules = [
            'author' => 'required|max:60|alpha',
            'quote' => 'required|max:500'
        ];

        $messages = [
            'author.required' => 'Укажите имя!',
            'author.max' => 'Имя не должно быть больше :max символов!',
            'author.alpha' => 'Имя может содержать только буквы!',
            'quote.required' => 'Введите текст заметки!',
            'quote.max' => 'Текст заметки не может быть больше :max символов'
        ];

        $this->validate($request, $rules, $messages);

        $authorText = ucfirst($request['author']);
        $quoteText = $request['quote'];

        $author = Author::where('name', $authorText)->first();
        if (!$author) {
            $author = new Author();
            $author->name = $authorText;
            $author->save();
        }

        $quote = new Quote();
        $quote->quote = $quoteText;
        $author->quotes()->save($quote);

        return redirect()->route('index')->with([
            'success' => 'Заметка сохранена!'
        ]);
    }

    public function getDeleteQuote($quote_id)
    {
        $quote = Quote::find($quote_id);
        // или так:
        // $quote = Quote::where('id', $quote_id)->first();
        $author_deleted = false;

        if (count($quote->author->quotes) === 1) {
            $quote->author->delete();
            $author_deleted = true;
        }

        $quote->delete();

        $msg = $author_deleted ? 'Заметка и автор удалены!' : 'Заметка удалена!';
        return redirect()->route('index')->with(['success' => $msg]);
    }
}
