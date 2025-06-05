<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Інформація профілю') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Оновіть інформацію профілю та електронну адресу.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input type="file" id="photo" class="hidden"
                            wire:model.live="photo"
                            x-ref="photo"
                            x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <x-label for="photo" value="{{ __('Фото') }}" />

                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->full_name }}" class="rounded-full size-20 object-cover">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block rounded-full size-20 bg-cover bg-no-repeat bg-center"
                          x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.photo.click()">
                    {{ __('Вибрати нове фото') }}
                </x-secondary-button>

                @if ($this->user->profile_photo_path)
                    <x-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                        {{ __('Видалити фото') }}
                    </x-secondary-button>
                @endif

                <x-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <!-- First Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="first_name" value="{{ __('Ім`я') }}" />
            <x-input id="first_name" type="text" class="mt-1 block w-full" wire:model="state.first_name" required />
            <x-input-error for="first_name" class="mt-2" />
        </div>

        <!-- Last Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="last_name" value="{{ __('Прізвище') }}" />
            <x-input id="last_name" type="text" class="mt-1 block w-full" wire:model="state.last_name" required />
            <x-input-error for="last_name" class="mt-2" />
        </div>

        <!-- Middle Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="middle_name" value="{{ __('По батькові') }}" />
            <x-input id="middle_name" type="text" class="mt-1 block w-full" wire:model="state.middle_name" />
            <x-input-error for="middle_name" class="mt-2" />
        </div>

        <!-- Course Number (for students) -->
        @if ($this->user->hasRole('student'))
            <div class="col-span-6 sm:col-span-4">
                <x-label for="course_number" value="{{ __('Курс') }}" />
                <select id="course_number" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" wire:model="state.course_number">
                    <option value="">{{ __('Виберіть курс') }}</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
                <x-input-error for="course_number" class="mt-2" />
            </div>
        @endif

        <!-- Description -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="description" value="{{ __('Опис') }}" />
            <div wire:ignore>
                <textarea
                    id="user-description-editor"
                    name="description"
                    placeholder="Розкажіть про себе"
                    class="hidden"
                >{{ $this->state['description'] ?? '' }}</textarea>
            </div>
            <x-input-error for="description" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" required autocomplete="username" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-sm mt-2 dark:text-white">
                    {{ __('Ваша електронна адреса не підтверджена.') }}

                    <button type="button" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" wire:click.prevent="sendEmailVerification">
                        {{ __('Натисніть тут, щоб повторно надіслати лист підтвердження.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                        {{ __('Нове посилання для підтвердження надіслано на вашу електронну адресу.') }}
                    </p>
                @endif
            @endif
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Збережено.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Зберегти') }}
        </x-button>
    </x-slot>
</x-form-section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ініціалізуємо EasyMDE для опису користувача
    const textarea = document.getElementById('user-description-editor');
    if (textarea && typeof EasyMDE !== 'undefined') {
        const easymde = new EasyMDE({
            element: textarea,
            placeholder: 'Розкажіть про себе',
            spellChecker: false,
            autofocus: false,
            autosave: {
                enabled: false
            },
            status: ['lines', 'words', 'cursor'],
            toolbar: [
                'bold', 'italic', 'strikethrough', '|',
                'heading-1', 'heading-2', 'heading-3', '|',
                'quote', 'unordered-list', 'ordered-list', '|',
                'link', 'image', '|',
                'code', 'horizontal-rule', '|',
                'preview', 'side-by-side', 'fullscreen', '|',
                'guide'
            ],
            previewClass: ['prose', 'prose-sm', 'max-w-none', 'dark:prose-invert'],
            renderingConfig: {
                singleLineBreaks: false,
                codeSyntaxHighlighting: true,
            }
        });

        // Зберігаємо посилання на інстанс
        textarea.easymdeInstance = easymde;

        // Синхронізуємо з Livewire
        easymde.codemirror.on('change', function() {
            const content = easymde.value();
            @this.set('state.description', content);
        });

        // Слухаємо зміни теми
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    const isDark = document.documentElement.classList.contains('dark');
                    if (easymde.codemirror) {
                        easymde.codemirror.setOption('theme', isDark ? 'monokai' : 'default');
                    }
                }
            });
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        });
    }
});
</script>
@endpush
