@props(['project'])

@php
    $canShowChat = $project->assigned_to !== null;
    $isAdmin = auth()->check() && auth()->user()->hasRole('admin');
    $isSupervisor = $project->supervisor && $project->supervisor->user_id == auth()->id();
    $isAssignedStudent = $project->assigned_to == auth()->id();
    $canAccessChat = $isAdmin || $isSupervisor || $isAssignedStudent;
@endphp

@if($canShowChat && $canAccessChat)
    <div class="p-6 border-t border-gray-200 dark:border-gray-700"
         x-data="projectChat('{{ $project->id }}', '{{ auth()->id() }}')"
         x-init="init()">
        <style>
            /* Стилі для повідомлень */
            .message-mine {
                background: linear-gradient(135deg, rgba(var(--color-primary-50), 0.9), rgba(var(--color-primary-100), 0.9));
                border-top-left-radius: 1rem;
                border-top-right-radius: 0.25rem;
                border-bottom-left-radius: 1rem;
                border-bottom-right-radius: 1rem;
                color: rgb(var(--color-primary-800));
                border-right: 2px solid rgba(var(--gradient-button-end), 0.5);
                border-bottom: 2px solid rgba(var(--gradient-button-end), 0.3);
            }

            .dark .message-mine {
                background: linear-gradient(135deg, rgba(var(--color-primary-900), 0.9), rgba(var(--color-primary-800), 0.9));
                color: rgb(var(--color-primary-200));
                border-right: 2px solid rgba(var(--gradient-button-start), 0.5);
                border-bottom: 2px solid rgba(var(--gradient-button-start), 0.3);
            }

            .message-other {
                background: linear-gradient(135deg, rgba(229, 231, 235, 0.9), rgba(209, 213, 219, 0.9));
                border-top-left-radius: 0.25rem;
                border-top-right-radius: 1rem;
                border-bottom-left-radius: 1rem;
                border-bottom-right-radius: 1rem;
                color: rgb(31, 41, 55);
                border-left: 2px solid rgba(156, 163, 175, 0.5);
                border-bottom: 2px solid rgba(156, 163, 175, 0.3);
            }

            .dark .message-other {
                background: linear-gradient(135deg, rgba(55, 65, 81, 0.9), rgba(75, 85, 99, 0.9));
                color: rgb(229, 231, 235);
                border-left: 2px solid rgba(107, 114, 128, 0.5);
                border-bottom: 2px solid rgba(107, 114, 128, 0.3);
            }

            .empty-message-gradient, .text-gradient-primary {
                background: linear-gradient(to right, rgb(var(--gradient-button-start)), rgb(var(--gradient-button-middle)), rgb(var(--gradient-button-end)));
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
                font-weight: 500;
            }
        </style>
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Чат проекту</h2>

        <!-- Область повідомлень -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4 h-80 overflow-y-auto"
             x-ref="messagesContainer">
            <!-- Індикатор завантаження -->
            <div x-show="loading" class="flex justify-center items-center h-full">
                <svg class="animate-spin h-8 w-8 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <!-- Повідомлення -->
            <template x-if="!loading">
                <div>
                    <div x-show="messages.length === 0" class="flex flex-col items-center justify-center h-64">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                             style="stroke: url(#chatIconGradient);">
                            <defs>
                                <linearGradient id="chatIconGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="rgb(var(--gradient-button-start))" />
                                    <stop offset="50%" stop-color="rgb(var(--gradient-button-middle))" />
                                    <stop offset="100%" stop-color="rgb(var(--gradient-button-end))" />
                                </linearGradient>
                            </defs>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p class="empty-message-gradient font-medium">Немає повідомлень. Почніть розмову!</p>
                    </div>

                    <template x-for="message in messages" :key="message.id">
                        <div :class="{'flex flex-col mb-4': true, 'items-end': message.is_mine, 'items-start': !message.is_mine}">
                            <div class="flex items-center mb-1">
                                <span x-text="message.sender_name" class="text-sm font-medium"
                                      :class="message.is_mine ? 'text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300'"></span>
                                <span x-text="formatDate(message.created_at)" class="text-xs text-gray-500 dark:text-gray-400 ml-2"></span>
                                <template x-if="!message.is_mine && !message.is_read">
                                    <span class="ml-2 h-2 w-2 rounded-full" style="background: linear-gradient(to right, rgb(var(--gradient-button-start)), rgb(var(--gradient-button-end)))"></span>
                                </template>
                            </div>
                            <div class="max-w-3/4 px-4 py-2 rounded-lg shadow-sm relative"
                                 :class="message.is_mine ? 'message-mine' : 'message-other'">
                                <p x-text="message.message" class="text-sm whitespace-pre-wrap break-words"></p>

                                <!-- Статус прочитання для останнього повідомлення -->
                                <template x-if="message.is_mine && isLastMessageFromUser(message.id)">
                                    <div class="absolute bottom-1 right-2 flex items-center text-xs mt-1" style="transform: translateY(100%);">
                                        <template x-if="message.is_read">
                                            <span class="text-gradient-primary">Прочитано</span>
                                        </template>
                                        <template x-if="!message.is_read">
                                            <span class="text-gray-500 dark:text-gray-400">Надіслано</span>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <!-- Форма відправки повідомлення -->
        <form @submit.prevent="sendMessage" class="flex items-center">
            <div class="flex-grow mr-2 relative">
                <input
                    type="text"
                    x-model="newMessage"
                    placeholder="Введіть повідомлення..."
                    class="w-full input-gradient rounded-md border-transparent focus:border-transparent focus:ring focus:ring-primary-200 dark:focus:ring-primary-800 dark:bg-gray-800 dark:text-gray-200"
                    maxlength="1000"
                    @keydown.enter.exact.prevent="sendMessage"
                />
            </div>
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 btn-gradient border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    :disabled="!newMessage.trim()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                Надіслати
            </button>
        </form>
    </div>

    @push('scripts')
    <script>
        function projectChat(projectId, userId) {
            return {
                projectId: projectId,
                userId: userId,
                messages: [],
                newMessage: '',
                loading: true,
                unreadMessages: [],

                init() {
                    this.fetchMessages();
                    this.listenForMessages();

                    // Прокручування до останнього повідомлення при зміні messages
                    this.$watch('messages', () => {
                        this.$nextTick(() => {
                            this.scrollToBottom();
                            this.markMessagesAsRead();
                        });
                    });
                },

                fetchMessages() {
                    this.loading = true;
                    fetch(`/projects/${this.projectId}/messages`)
                        .then(response => response.json())
                        .then(data => {
                            this.messages = data.messages;
                            this.loading = false;
                            this.$nextTick(() => {
                                this.scrollToBottom();
                                this.markMessagesAsRead();
                            });
                        })
                        .catch(error => {
                            console.error('Помилка завантаження повідомлень:', error);
                            this.loading = false;
                        });
                },

                sendMessage() {
                    if (!this.newMessage.trim()) return;

                    fetch(`/projects/${this.projectId}/messages`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ message: this.newMessage })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Оновлюємо список повідомлень з сервера, щоб отримати останні зміни
                        this.fetchMessages();
                        this.newMessage = '';
                        this.scrollToBottom();
                    })
                    .catch(error => {
                        console.error('Помилка відправки повідомлення:', error);
                    });
                },

                listenForMessages() {
                    window.Echo.private(`project.${this.projectId}`)
                        .listen('.message.sent', (data) => {
                            // Встановлюємо правильне значення is_mine
                            data.message.is_mine = data.message.sender_id === this.userId;
                            
                            // Додаємо повідомлення, якщо воно не наше або це наше повідомлення через інший пристрій
                            if (!data.message.is_mine || data.message.sender_id === this.userId) {
                                // Додаємо повідомлення, якщо його ще немає в списку
                                if (!this.messages.some(m => m.id === data.message.id)) {
                                    this.messages.push(data.message);

                                    // Обробка нового повідомлення
                                    // Якщо вікно активне, одразу позначаємо як прочитане
                                    if (document.visibilityState === 'visible') {
                                        this.$nextTick(() => {
                                            this.markMessagesAsRead();
                                        });
                                    } else if (!data.message.is_mine) {
                                        this.unreadMessages.push(data.message.id);

                                        // Додаємо обробник для позначення повідомлень як прочитаних при поверненні на вкладку
                                        const markReadOnFocus = () => {
                                            this.markMessagesAsRead();
                                            window.removeEventListener('focus', markReadOnFocus);
                                            document.removeEventListener('visibilitychange', visibilityChangeHandler);
                                        };

                                        const visibilityChangeHandler = () => {
                                            if (document.visibilityState === 'visible') {
                                                this.markMessagesAsRead();
                                                window.removeEventListener('focus', markReadOnFocus);
                                                document.removeEventListener('visibilitychange', visibilityChangeHandler);
                                            }
                                        };

                                        window.addEventListener('focus', markReadOnFocus);
                                        document.addEventListener('visibilitychange', visibilityChangeHandler);
                                    }
                                }
                            }
                        });
                },

                markMessagesAsRead() {
                    // Знаходимо непрочитані повідомлення від інших користувачів
                    const unreadMessageIds = this.messages
                        .filter(msg => !msg.is_mine && !msg.is_read)
                        .map(msg => msg.id);

                    if (unreadMessageIds.length > 0) {
                        // Спочатку позначаємо повідомлення як прочитані локально для миттєвого відображення
                        this.messages.forEach(msg => {
                            if (unreadMessageIds.includes(msg.id)) {
                                msg.is_read = true;
                            }
                        });

                        // Потім відправляємо запит на сервер для збереження статусу
                        fetch(`/projects/${this.projectId}/messages/read`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ message_ids: unreadMessageIds })
                        })
                        .catch(error => {
                            console.error('Помилка позначення повідомлень як прочитаних:', error);
                            // Якщо виникла помилка, повертаємо статус "непрочитано"
                            this.messages.forEach(msg => {
                                if (unreadMessageIds.includes(msg.id)) {
                                    msg.is_read = false;
                                }
                            });
                        });
                    }
                },

                scrollToBottom() {
                    const container = this.$refs.messagesContainer;
                    container.scrollTop = container.scrollHeight;
                },

                formatDate(dateString) {
                    const date = new Date(dateString);
                    const now = new Date();
                    const isToday = date.toDateString() === now.toDateString();

                    if (isToday) {
                        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    } else {
                        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    }
                },

                // Перевірка, чи це останнє повідомлення від користувача
                isLastMessageFromUser(messageId) {
                    // Фільтруємо повідомлення, щоб отримати тільки ті, що відправлені поточним користувачем
                    const userMessages = this.messages.filter(msg => msg.is_mine);

                    // Якщо немає повідомлень від користувача, повертаємо false
                    if (userMessages.length === 0) {
                        return false;
                    }

                    // Отримуємо останнє повідомлення від користувача
                    const lastUserMessage = userMessages[userMessages.length - 1];

                    // Перевіряємо, чи це повідомлення є останнім від користувача
                    return lastUserMessage.id === messageId;
                }
            };
        }
    </script>
    @endpush
@endif
