<x-action-section>
    <x-slot name="title">
        {{ __('Видалити акаунт') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Назавжди видалити ваш акаунт.') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
            {{ __('Після видалення вашого акаунту всі його ресурси та дані будуть назавжди видалені. Перед видаленням акаунту, будь ласка, завантажте будь-які дані або інформацію, яку ви хочете зберегти.') }}
        </div>

        <div class="mt-5">
            <x-danger-button wire:click="confirmUserDeletion" wire:loading.attr="disabled">
                {{ __('Видалити акаунт') }}
            </x-danger-button>
        </div>

        <!-- Delete User Confirmation Modal -->
        <x-dialog-modal wire:model.live="confirmingUserDeletion">
            <x-slot name="title">
                {{ __('Видалити акаунт') }}
            </x-slot>

            <x-slot name="content">
                {{ __('Ви впевнені, що хочете видалити свій акаунт? Після видалення акаунту всі його ресурси та дані будуть назавжди видалені. Будь ласка, введіть ваш пароль, щоб підтвердити, що ви хочете назавжди видалити свій акаунт.') }}

                <div class="mt-4" x-data="{}" x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                    <x-input type="password" class="mt-1 block w-3/4"
                                autocomplete="current-password"
                                placeholder="{{ __('Пароль') }}"
                                x-ref="password"
                                wire:model="password"
                                wire:keydown.enter="deleteUser" />

                    <x-input-error for="password" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                    {{ __('Скасувати') }}
                </x-secondary-button>

                <x-danger-button class="ms-3" wire:click="deleteUser" wire:loading.attr="disabled">
                    {{ __('Видалити акаунт') }}
                </x-danger-button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>
