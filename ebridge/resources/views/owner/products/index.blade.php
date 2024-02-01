<x-app-layout>
    <x-slot name="header">
    <div class="flex">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    商品管理
                </h2>
            </div>
            <div class="ml-auto">
                <butto onclick="location.href='{{ route('owner.products.create') }}'" class="text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">新規登録</button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <x-flash-message status="info" />

                    <div class="flex flex-wrap">
                        @foreach($ownerInfo as $owner)
                            @if($owner->shop !== null)
                                @foreach($owner->shop->product as $product)
                                    <div class="w-1/4 p-2 md:p-4">
                                        <a href="{{ route('owner.products.edit', ['product' => $product->id] ) }}">
                                            <div class="border rounded-md p-2 md:p-4">
                                                <x-thumbnail filename="{{ $product->imageFirst->filename ?? '' }}" type="products" />
                                                <div class="font-bold">{{ $product->name }}</div>
                                                @php
                                                    $totalQuantity = 0;
                                                @endphp
                                                @foreach($product->stock as $stock)
                                                    @php
                                                        $totalQuantity += $stock->quantity;
                                                    @endphp
                                                @endforeach
                                                <div class="text-gray-500">在庫数<span class="text-black ml-2">{{ $totalQuantity }}</span></div>
                                                <div class="text-lg font-bold">￥{{ number_format($product->price) }}</div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
