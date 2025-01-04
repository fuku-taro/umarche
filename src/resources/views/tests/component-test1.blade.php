<x-tests.app>
  <x-slot name="header">ヘッダー１</x-slot>
  コンポーネント１
  <x-tests.card title="タイトル" content="本文" :message="$message"/>
  <x-tests.card title="CSS" class="bg-red-300"/>
</x-tests.app>