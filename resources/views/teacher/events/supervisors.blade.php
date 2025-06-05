<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Керівники події') }}: {{ $event->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('teacher.events.edit', $event) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Редагувати подію
                </a>
                <a href="{{ route('events.show', $event) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Назад
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Поточні керівники -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Поточні керівники</h3>

                @if($event->supervisors->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400">Немає керівників для цієї події.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ім'я</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Кількість місць</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Примітка</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Дії</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($event->supervisors as $supervisor)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $supervisor->user->full_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $supervisor->user->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $supervisor->slot_count }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $supervisor->note ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button type="button" onclick="openEditModal('{{ $supervisor->id }}', '{{ $supervisor->slot_count }}', '{{ $supervisor->note }}')" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-3">
                                                Редагувати
                                            </button>

                                            @if($event->supervisors->count() > 1)
                                                <form action="{{ route('teacher.events.supervisors.remove', [$event, $supervisor]) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" onclick="return confirm('Ви впевнені, що хочете видалити цього керівника?')">
                                                        Видалити
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Додавання нового керівника -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Додати нового керівника</h3>

                @if($availableTeachers->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400">Немає доступних викладачів для додавання.</p>
                @else
                    <form action="{{ route('teacher.events.supervisors.add', $event) }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Вибір викладача -->
                            <div>
                                <x-label for="user_id" value="{{ __('Викладач') }}" />
                                <select id="user_id" name="user_id" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
                                    <option value="">Виберіть викладача</option>
                                    @foreach($availableTeachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('user_id') == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->full_name }} ({{ $teacher->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Кількість місць -->
                            <div>
                                <x-label for="slot_count" value="{{ __('Кількість місць') }}" />
                                <x-input id="slot_count" class="block mt-1 w-full" type="number" name="slot_count" :value="old('slot_count', 5)" min="1" required />
                                @error('slot_count')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Примітка -->
                            <div>
                                <x-label for="note" value="{{ __('Примітка') }}" />
                                <x-markdown-editor
                                    name="note"
                                    :value="old('note')"
                                    placeholder="Введіть примітку для керівника"
                                    id="supervisor-note-editor"
                                />
                                @error('note')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end mt-4">
                            <x-button>
                                {{ __('Додати керівника') }}
                            </x-button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Модальне вікно для редагування керівника -->
    <div id="editSupervisorModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <div class="px-6 py-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Редагувати інформацію про керівника</h3>
            </div>

            <form id="editSupervisorForm" method="POST">
                @csrf
                @method('PUT')

                <div class="px-6 py-4">
                    <!-- Кількість місць -->
                    <div class="mb-4">
                        <x-label for="edit_slot_count" value="{{ __('Кількість місць') }}" />
                        <x-input id="edit_slot_count" class="block mt-1 w-full" type="number" name="slot_count" min="1" required />
                    </div>

                    <!-- Примітка -->
                    <div>
                        <x-label for="edit_note" value="{{ __('Примітка') }}" />
                        <x-markdown-editor
                            name="note"
                            value=""
                            placeholder="Введіть примітку для керівника"
                            id="edit-supervisor-note-editor"
                        />
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 text-right">
                    <button type="button" onclick="closeEditModal()" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 focus:bg-gray-300 dark:focus:bg-gray-500 active:bg-gray-400 dark:active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                        Скасувати
                    </button>
                    <x-button>
                        {{ __('Зберегти') }}
                    </x-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(supervisorId, slotCount, note) {
            document.getElementById('editSupervisorForm').action = "{{ route('teacher.events.supervisors.update', [$event, ':supervisorId']) }}".replace(':supervisorId', supervisorId);
            document.getElementById('edit_slot_count').value = slotCount;

            // Оновлюємо значення в EasyMDE редакторі
            const editTextarea = document.getElementById('edit-supervisor-note-editor');
            if (editTextarea && editTextarea.easymdeInstance) {
                editTextarea.easymdeInstance.value(note || '');
            } else {
                // Якщо редактор ще не ініціалізований, встановлюємо значення в textarea
                if (editTextarea) {
                    editTextarea.value = note || '';
                }
            }

            document.getElementById('editSupervisorModal').classList.remove('hidden');

            // Ініціалізуємо EasyMDE для модального вікна, якщо ще не ініціалізований
            setTimeout(function() {
                if (typeof window.initializeEasyMDE === 'function') {
                    window.initializeEasyMDE('edit-supervisor-note-editor');
                }
            }, 100);
        }

        function closeEditModal() {
            document.getElementById('editSupervisorModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
