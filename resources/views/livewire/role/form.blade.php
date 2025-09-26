<div>
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="max-w-full overflow-x-auto p-4">
            {{-- Header --}}
            <div class="flex justify-between items-center">
                <h1 class="text-base font-medium text-gray-800 dark:text-white/90">
                    {{ $roleId ? 'Edit ' . $page_title : 'Create ' . $page_title }}
                </h1>
                <a href="{{ route('roles.index') }}"
                    class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-white transition rounded-lg bg-orange-500 shadow-theme-xs hover:bg-orange-600">
                    <x-fas-chevron-left class="w-3 h-3" />
                    Back
                </a>
            </div>
            <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800 mt-4">
                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Nama Role
                        </label>
                        <x-text-input wire:model.defer="name" type="text" id="name" placeholder="Enter Name"
                            :has-error="$errors->has('name')" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Permissions
                        </label>
                        <div class="space-y-4">
                            @forelse ($groupedPermissions as $group => $permissions)
                                <div class="border p-4 mb-4">
                                    <h3 class="font-semibold mb-2 capitalize text-gray-700 dark:text-gray-400">
                                        {{ $group }}
                                    </h3>

                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                        @foreach ($permissions as $permission)
                                            @php
                                                $checkboxId =
                                                    'checkboxPermission_' . $group . '_' . Str::slug($permission);
                                            @endphp

                                            <label for="{{ $checkboxId }}"
                                                class="flex cursor-pointer items-center text-sm font-medium text-gray-700 select-none dark:text-gray-400">

                                                <div class="relative">
                                                    {{-- Checkbox asli (hidden) --}}
                                                    <input type="checkbox" id="{{ $checkboxId }}"
                                                        value="{{ $permission }}" wire:model="selectedPermissions"
                                                        class="sr-only peer" />

                                                    {{-- Kotak custom, berubah kalau peer:checked --}}
                                                    <div
                                                        class="mr-3 flex h-5 w-5 items-center justify-center rounded-md border-[1.25px]
                           border-gray-300 dark:border-gray-700
                           hover:border-brand-500 dark:hover:border-brand-500
                           peer-checked:border-brand-500 peer-checked:bg-brand-500">
                                                        <span class="opacity-0 peer-checked:opacity-100">
                                                            <svg width="14" height="14" viewBox="0 0 14 14"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M11.6666 3.5L5.24992 9.91667L2.33325 7"
                                                                    stroke="white" stroke-width="1.94437"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                </div>

                                                {{ ucwords(str_replace($group . ' ', '', $permission)) }}
                                            </label>
                                        @endforeach
                                    </div>


                                </div>
                            @empty
                                <span class="text-gray-500 text-sm">Belum ada role.</span>
                            @endforelse
                        </div>
                    </div>


                    <div class="flex items-center gap-3 px-2 mt-6 lg:justify-end">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600">
                            <x-fas-save class="w-4 h-4" />
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
