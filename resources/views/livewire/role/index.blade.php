<div wire:init="bootstrap">

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="max-w-full overflow-x-auto p-4">
            {{-- Header --}}
            <div class="flex justify-between items-center">
                <h1 class="text-base font-medium text-gray-800 dark:text-white/90">
                    {{ $page_subtitle }}
                </h1>
                <a href="{{ route('roles.create') }}"
                    class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600">
                    <x-fas-plus class="w-3 h-3" />
                    Add
                </a>
            </div>

            {{-- Filter Bar --}}
            <div
                class="bg-neutral-100 dark:bg-white/[0.03] p-4 rounded-lg shadow-sm flex flex-wrap gap-4 items-end mt-3">
                {{-- Search --}}
                <div class="flex-1 min-w-[250px]">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Search
                    </label>
                    <x-text-input type="text" id="search" placeholder="Search by name or guardname"
                        wire:model.live.debounce.400ms="search" />
                </div>

                {{-- Per Page --}}
                <div class="min-w-[120px]">
                    <label for="perPage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Per Page
                    </label>
                    <select id="perPage" wire:model.live="perPage"
                        class="h-11 w-full rounded-lg border bg-white border-gray-300 px-4 py-2.5 pr-11 text-sm text-gray-800
                               dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            {{-- Table --}}
            <div
                class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] mt-4">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 dark:bg-white/[0.04]">
                        <tr>
                            <th class="px-4 py-2 text-start"></th>
                            <th class="px-4 py-2 cursor-pointer text-start text-theme-xs font-medium text-gray-500 dark:text-gray-400"
                                wire:click="sortBy('roles.name')">
                                Name {!! $this->sortField === 'roles.name' ? ($this->sortDirection === 'asc' ? ' &#129057;' : ' &#129059;') : '' !!}
                            </th>
                            <th class="px-4 py-2 cursor-pointer text-start text-theme-xs font-medium text-gray-500 dark:text-gray-400"
                                wire:click="sortBy('roles.email')">
                                Guard Name {!! $this->sortField === 'roles.email' ? ($this->sortDirection === 'asc' ? ' &#129057;' : ' &#129059;') : '' !!}
                            </th>
                            <th class="px-4 py-2 cursor-pointer text-start text-theme-xs font-medium text-gray-500 dark:text-gray-400"
                                wire:click="sortBy('roles.created_at')">
                                Created {!! $this->sortField === 'roles.created_at'
                                    ? ($this->sortDirection === 'asc'
                                        ? ' &#129057;'
                                        : ' &#129059;')
                                    : '' !!}
                            </th>
                            <th class="px-4 py-2 text-start"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Skeleton loading --}}
                        @unless ($this->ready)
                            @for ($i = 0; $i < 5; $i++)
                                <tr>
                                    @foreach (range(1, 5) as $col)
                                        <td class="px-4 py-2">
                                            <div
                                                class="h-4 {{ $col === 6 ? 'w-24' : 'w-full' }} max-w-[200px] animate-pulse bg-gray-200 dark:bg-neutral-700 rounded">
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endfor
                        @endunless

                        {{-- Data --}}
                        @if ($this->ready)
                            @forelse ($roles as $role)
                                <tr wire:key="row-{{ $role->id }}">
                                    <td class="px-4 py-2 text-theme-xs font-medium text-gray-500 dark:text-gray-400">
                                        {{ $roles->firstItem() + $loop->index }}</td>
                                    <td class="px-4 py-2 text-theme-xs font-medium text-gray-500 dark:text-gray-400">
                                        {{ $role->name }}</td>
                                    <td class="px-4 py-2 text-theme-xs font-medium text-gray-500 dark:text-gray-400">
                                        {{ $role->guard_name }}</td>
                                    <td class="px-4 py-2 text-theme-xs font-medium text-gray-500 dark:text-gray-400">
                                        {{ $role->created_at->format('d-m-Y') }}</td>
                                    <td class="px-4 py-2 text-end space-x-2">
                                        {{-- Edit --}}
                                        @can('permission edit')
                                        <x-tooltip content="Edit" position="top">
                                            <a href="{{ route('roles.edit', $role->id) }}"
                                                class="inline-flex items-center justify-center rounded-lg text-indigo-500 hover:text-indigo-600 transition">
                                                <x-fas-pen-to-square class="w-4 h-4" />
                                            </a>
                                        </x-tooltip>
                                        @endcan

                                        {{-- Delete --}}
                                        @can('permission delete')
                                        <x-tooltip content="Delete" position="top">
                                            <button type="button"
                                                x-on:click="$dispatch('confirm-delete', { id: {{ $role->id }} })"
                                                class="inline-flex items-center justify-center rounded-lg text-rose-500 hover:text-rose-600 transition">
                                                <x-fas-times class="w-4 h-4" />
                                            </button>
                                        </x-tooltip>
                                        @endcan

                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                                        Tidak ada data.
                                    </td>
                                </tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>

                {{-- Pagination --}}
                @if ($this->ready && method_exists($roles, 'links'))
                    <div class="px-4 py-2 border-t border-gray-200 dark:border-neutral-700">
                        {{ $roles->onEachSide(1)->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
