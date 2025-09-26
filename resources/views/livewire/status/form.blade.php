<div>
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="max-w-full overflow-x-auto p-4">
            {{-- Header --}}
            <div class="flex justify-between items-center">
                <h1 class="text-base font-medium text-gray-800 dark:text-white/90">
                    {{ $id ? 'Edit ' . $page_title : 'Create ' . $page_title }}
                </h1>
                <a href="{{ route('statuses.index') }}"
                    class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-white transition rounded-lg bg-orange-500 shadow-theme-xs hover:bg-orange-600">
                    <x-fas-chevron-left class="w-3 h-3" />
                    Back
                </a>
            </div>
            <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800 mt-4">
                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Status Code
                        </label>
                        <x-text-input wire:model.defer="status_code" type="number" id="status_code"
                            placeholder="Enter Status Code" :has-error="$errors->has('status_code')" />
                        <x-input-error :messages="$errors->get('status_code')" class="mt-2" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Keterangan
                        </label>
                        <x-text-input wire:model="keterangan" type="text" id="keterangan"
                            placeholder="Enter Status Information" :has-error="$errors->has('keterangan')" />
                        <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
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
