@props([
  'title' => '',
  'content' => '初期値',
  'message' => '初期値',
  ])

<div {{ $attributes->merge([
  'class' => 'border-2 shadow-md p-2'
]) }} >
{{-- <div {{ $attributes }} class="border-2 shadow-md p-2" > --}}
  <div>{{ $title }}</div>
  <div>画像</div>
  <div>{{ $content }}</div>
  <div>{{ $message }}</div>
</div>