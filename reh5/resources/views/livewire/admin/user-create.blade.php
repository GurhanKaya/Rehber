<div class="p-6 max-w-3xl mx-auto bg-white dark:bg-zinc-800 rounded-xl shadow-md space-y-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('app.add_new_user_title') }}</h1>

    @if($success)
        <div class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 p-4 rounded-lg font-semibold">
            ✅ {{ __('app.user_created_success') }}
        </div>
    @endif

    @error('general')
        <div class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 p-4 rounded-lg font-semibold">
            {{ $message }}
        </div>
    @enderror

    <form wire:submit.prevent="save" class="space-y-5">
        <!-- Fotoğraf Yükleme Alanı -->
        <div class="flex items-center space-x-6">
            <div class="shrink-0">
                @if ($photo)
                    <img class="h-24 w-24 object-cover rounded-full" src="{{ $photo->temporaryUrl() }}" alt="{{ __('app.profile_photo_preview') }}">
                @else
                    <div class="h-24 w-24 rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center">
                        <span class="text-2xl font-bold text-gray-600 dark:text-gray-300">{{ strtoupper(substr($name,0,1)) }}{{ strtoupper(substr($surname,0,1)) }}</span>
                    </div>
                @endif
            </div>
            <div class="flex flex-col space-y-3 flex-1">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('app.profile_photo') }}
                    </label>
                    <input type="file" wire:model="photo" accept="image/*" aria-label="{{ __('app.choose_file') }}"
                           class="block w-full text-sm text-gray-500 dark:text-gray-300
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-blue-50 file:text-blue-700 file:cursor-pointer
                                  hover:file:bg-blue-100
                                  dark:file:bg-zinc-700 dark:file:text-white">
                </div>
                @error('photo') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <input type="text" wire:model.defer="name" placeholder="{{ __('app.name_required') }}"
                       class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white @error('name') border-red-500 @enderror" />
                @error('name') <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <input type="text" wire:model.defer="surname" placeholder="{{ __('app.surname_field') }}"
                       class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white @error('surname') border-red-500 @enderror" />
                @error('surname') <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <input type="email" wire:model.defer="email" placeholder="{{ __('app.email_required') }}"
                       class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white @error('email') border-red-500 @enderror" />
                @error('email') <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <input type="password" wire:model.defer="password" placeholder="{{ __('app.password_required') }}"
                       class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white @error('password') border-red-500 @enderror" />
                @error('password') <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <select wire:model.defer="title_id" class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white @error('title_id') border-red-500 @enderror">
                    <option value="">{{ __('app.all_titles') }}</option>
                    @foreach($titles as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('title_id') <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <select wire:model.defer="department_id" class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white @error('department_id') border-red-500 @enderror">
                    <option value="">{{ __('app.all_departments') }}</option>
                    @foreach($departments as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('department_id') <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <input type="text" wire:model.defer="phone" placeholder="{{ __('app.phone_format') }}"
                       class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white @error('phone') border-red-500 @enderror" />
                @error('phone') <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <select wire:model.defer="role"
                        class="border rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white @error('role') border-red-500 @enderror">
                    <option value="">{{ __('app.select_role') }}</option>
                    <option value="admin">{{ __('app.admin_role') }}</option>
                    <option value="personel">{{ __('app.staff_role') }}</option>
                </select>
                @error('role') <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold p-3 rounded-lg transition duration-200">
            {{ __('app.save_button') }}
        </button>
    </form>
</div>
