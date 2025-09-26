<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="pageName"></h2>

    <nav>
        <ol class="flex items-center gap-1.5">
            <li>
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 dark:text-gray-400">Dashboard</a>
            </li>
            <x-breadcrumb-arrow />
            <li class="text-sm text-gray-800 dark:text-white/90" x-text="pageName ?? 'Blank Page'"></li>
            <template x-if="subPageName">
                <li class="inline-flex items-center gap-1.5 text-sm text-gray-800 dark:text-white/90">
                    <x-breadcrumb-arrow />
                    <span x-text="subPageName"></span>
                </li>
            </template>

        </ol>
    </nav>
</div>
