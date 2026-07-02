@extends('layout')

@section('bodyClass', 'is-preload')

@section('content')
	<header>
		<h2>{{ $content['pagetitle'] }}</h2>
		<p>{{ $content['description'] }}</p>
	</header>
	@foreach(evo()->getDocumentChildren($content['id'], 1, 0, '*', '', 'menuindex', 'ASC') as $doc)
		@include('partials.article-item', ['item' => [
			'pagetitle' => $doc['pagetitle'],
			'description' => $doc['description'],
			'introtext' => $doc['introtext'],
			'link' => evo()->makeUrl($doc['id']),
			'createdon' => date('d.m.Y', $doc['createdon']),
			'thumb' => '',
		]])
	@endforeach
@endsection

@section('aside')
	@include('partials.sidebar')
@endsection
