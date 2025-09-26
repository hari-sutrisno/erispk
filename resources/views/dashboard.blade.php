<x-app-layout>
    @push('title')
        Dashboard | e-RISPK
    @endpush
    @section('page_title', 'Dashboard')

    @section('main')
        <div
            class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 xl:px-10 xl:py-12 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mx-auto w-full max-w-[630px] text-center">
                <h3 class="text-theme-xl mb-4 font-semibold text-gray-800 sm:text-2xl dark:text-white/90">
                    Card Title Here
                </h3>

                <p class="text-sm text-gray-500 sm:text-base dark:text-gray-400">
                    Start putting content on grids or panels, you can also use
                    different combinations of grids.Please check out the dashboard
                    and other pages
                </p>
            </div>
        </div>
    @endsection

</x-app-layout>
