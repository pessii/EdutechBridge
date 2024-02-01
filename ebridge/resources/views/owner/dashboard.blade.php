<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ダッシュボード') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <div class="flex flex-wrap">

                        <div class="w-1/4 p-2 md:p-4">
                            <div class="border rounded-md p-2 md:p-4">
                                <div class="text-gray-700">カテゴリー(日,週,月,年)</div>
                            </div>
                        </div>

                        <div class="w-1/4 p-2 md:p-4">
                            <div class="border rounded-md p-2 md:p-4">
                                <div class="text-gray-700">売上</div>
                            </div>
                        </div>

                        <div class="w-1/4 p-2 md:p-4">
                            <div class="border rounded-md p-2 md:p-4">
                                <div class="text-gray-700">利益率</div>
                            </div>
                        </div>

                        <div class="w-1/4 p-2 md:p-4">
                            <div class="border rounded-md p-2 md:p-4">
                                <div class="text-gray-700">注文数</div>
                            </div>
                        </div>

                    </div>

                    <div class="flex flex-wrap">

                        <div class="w-1/2 p-2 md:p-4">
                            <div class="border rounded-md p-2 md:p-4">
                                <div class="text-gray-700">新規個客数</div>
                            </div>
                        </div>

                        <div class="w-1/2 p-2 md:p-4">
                            <div class="border rounded-md p-2 md:p-4">
                                <div class="text-gray-700">リピーター数</div>
                            </div>
                        </div>

                    </div>

                    <div class="flex flex-wrap">

                        <div class="w-1/2 p-2 md:p-4">
                            <div class="border rounded-md p-2 md:p-4">
                                <div class="text-gray-700">在庫数残りわずかな商品</div>
                            </div>
                        </div>

                        <div class="w-1/2 p-2 md:p-4">
                            <div class="border rounded-md p-2 md:p-4">
                                <div class="text-gray-700">商品ランキング</div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
