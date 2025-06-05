@props(['project'])

@php
    $canShowChat = $project->assigned_to !== null;
    $isAdmin = auth()->check() && auth()->user()->hasRole('admin');
    $isSupervisor = $project->supervisor && $project->supervisor->user_id == auth()->id();
    $isAssignedStudent = $project->assigned_to == auth()->id();
    $canAccessChat = $isAdmin || $isSupervisor || $isAssignedStudent;
@endphp

@if($canShowChat && $canAccessChat)
    <div class="project-chat p-6 border-t border-gray-200 dark:border-gray-700"
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
                            <div class="max-w-3/4 px-3 py-1.5 rounded-lg shadow-sm relative"
                                 :class="message.is_mine ? 'message-mine' : 'message-other'">
                                <div x-html="message.message_html || message.message" class="text-sm whitespace-pre-wrap break-words prose prose-sm max-w-none dark:prose-invert leading-tight"></div>

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
        <form @submit.prevent="sendMessage" class="space-y-3">
            <div class="relative">
                <textarea
                    x-ref="messageEditor"
                    x-model="newMessage"
                    placeholder="Введіть повідомлення..."
                    class="hidden"
                    maxlength="1000"
                ></textarea>
            </div>
            <div class="flex justify-between items-center">
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    <kbd class="px-1 py-0.5 text-xs font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-100 dark:border-gray-500">Ctrl</kbd>
                    +
                    <kbd class="px-1 py-0.5 text-xs font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-100 dark:border-gray-500">Enter</kbd>
                    для відправки
                </div>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 btn-gradient border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        :disabled="!canSendMessage()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    Надіслати
                </button>
            </div>
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
                pollingInterval: null,

                init() {
                    console.log('Ініціалізація чату для проекту:', this.projectId, 'користувач:', this.userId);

                    this.fetchMessages();

                    // Перевіряємо доступність Echo перед підключенням
                    if (typeof window.Echo !== 'undefined') {
                        this.listenForMessages();
                    } else {
                        console.error('Echo не доступний. Перевірте підключення WebSocket.');
                        // Можна додати fallback на polling
                        this.startPolling();
                    }

                    // Ініціалізуємо редактор після завантаження компонента з затримкою
                    this.$nextTick(() => {
                        setTimeout(() => {
                            this.initializeMessageEditor();
                        }, 100);
                    });

                    // Прокручування до останнього повідомлення при зміні messages
                    this.$watch('messages', () => {
                        this.$nextTick(() => {
                            this.scrollToBottom();
                            this.markMessagesAsRead();
                        });
                    });
                },

                initializeMessageEditor() {
                    // Ініціалізуємо EasyMDE для повідомлень
                    const textarea = this.$refs.messageEditor;



                    if (!textarea) {
                        console.error('Textarea not found for chat editor');
                        return;
                    }

                    // Якщо EasyMDE недоступний, показуємо звичайне textarea
                    if (typeof EasyMDE === 'undefined') {
                        console.warn('EasyMDE not loaded, using regular textarea');
                        textarea.classList.remove('hidden');
                        textarea.classList.add('w-full', 'rounded-md', 'border-gray-300', 'dark:border-gray-700', 'dark:bg-gray-900', 'dark:text-gray-300', 'focus:border-primary-500', 'dark:focus:border-primary-600', 'focus:ring-primary-500', 'dark:focus:ring-primary-600');
                        textarea.rows = 3;

                        // Додаємо обробник для Ctrl+Enter
                        textarea.addEventListener('keydown', (e) => {
                            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                                e.preventDefault();
                                this.sendMessage();
                            }
                        });

                        // Автоматичне розширення для звичайного textarea
                        function autoResizeTextarea() {
                            textarea.style.height = 'auto';
                            const newHeight = Math.max(30, Math.min(120, textarea.scrollHeight));
                            textarea.style.height = newHeight + 'px';
                        }

                        textarea.addEventListener('input', autoResizeTextarea);
                        autoResizeTextarea();

                        return;
                    }

                    if (!textarea.easymdeInstance) {
                        const easymde = new EasyMDE({
                            element: textarea,
                            placeholder: 'Введіть повідомлення...',
                            spellChecker: false,
                            autofocus: false,
                            autosave: {
                                enabled: false
                            },
                            status: false, // Вимикаємо статус бар для компактності
                            toolbar: [
                                'bold', 'italic', '|',
                                'quote', 'unordered-list', 'ordered-list', '|',
                                'link', 'code', '|',
                                'preview'
                            ],
                            previewClass: ['prose', 'prose-sm', 'max-w-none', 'dark:prose-invert'],
                            renderingConfig: {
                                singleLineBreaks: true, // Для чату дозволяємо одинарні переноси
                                codeSyntaxHighlighting: true,
                            },
                            shortcuts: {
                                togglePreview: 'Cmd-P'
                            }
                        });

                        // Зберігаємо посилання на інстанс
                        textarea.easymdeInstance = easymde;

                        // Автоматичне розширення висоти для чату
                        function autoResizeChat() {
                            const codeMirror = easymde.codemirror;
                            const wrapper = codeMirror.getWrapperElement();
                            const scrollElement = wrapper.querySelector('.CodeMirror-scroll');
                            const sizerElement = wrapper.querySelector('.CodeMirror-sizer');

                            if (scrollElement && sizerElement) {
                                const contentHeight = sizerElement.offsetHeight;
                                const minHeight = 30;
                                const maxHeight = 120;
                                const newHeight = Math.max(minHeight, Math.min(maxHeight, contentHeight + 10));

                                scrollElement.style.height = newHeight + 'px';
                                scrollElement.style.maxHeight = maxHeight + 'px';
                                scrollElement.style.overflowY = newHeight >= maxHeight ? 'auto' : 'hidden';
                                wrapper.style.height = 'auto';
                            }
                        }

                        // Викликаємо автоматичне розширення при зміні контенту
                        easymde.codemirror.on('change', autoResizeChat);
                        easymde.codemirror.on('update', autoResizeChat);

                        // Початкове розширення
                        setTimeout(autoResizeChat, 100);

                        // Синхронізуємо з Alpine.js (двостороння синхронізація)
                        easymde.codemirror.on('change', () => {
                            this.newMessage = easymde.value();
                        });

                        // Слухаємо зміни в Alpine.js і оновлюємо EasyMDE
                        this.$watch('newMessage', (newValue) => {
                            if (easymde.value() !== newValue) {
                                easymde.value(newValue);
                            }
                        });

                        // Додаємо обробник для Ctrl+Enter для відправки
                        easymde.codemirror.setOption('extraKeys', {
                            'Ctrl-Enter': () => {
                                this.sendMessage();
                            },
                            'Cmd-Enter': () => {
                                this.sendMessage();
                            }
                        });

                        // Слухаємо зміни теми
                        const observer = new MutationObserver((mutations) => {
                            mutations.forEach((mutation) => {
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
                },

                canSendMessage() {
                    // Перевіряємо як Alpine.js значення, так і EasyMDE
                    let message = this.newMessage;
                    const textarea = this.$refs.messageEditor;
                    if (textarea && textarea.easymdeInstance) {
                        message = textarea.easymdeInstance.value();
                    }
                    return message && message.trim().length > 0;
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
                    // Отримуємо актуальне значення з EasyMDE
                    const textarea = this.$refs.messageEditor;
                    if (textarea && textarea.easymdeInstance) {
                        this.newMessage = textarea.easymdeInstance.value();
                    }

                    if (!this.newMessage.trim()) {
                        return;
                    }

                    const messageText = this.newMessage;
                    console.log('Відправка повідомлення:', messageText);

                    fetch(`/projects/${this.projectId}/messages`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ message: messageText })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Повідомлення відправлено успішно:', data);

                        // Додаємо повідомлення локально, якщо його ще немає
                        // (WebSocket може не спрацювати миттєво)
                        if (data.message && !this.messages.some(m => m.id === data.message.id)) {
                            this.messages.push(data.message);
                            this.$nextTick(() => {
                                this.scrollToBottom();
                            });
                        }

                        // Очищаємо поле вводу
                        this.newMessage = '';

                        // Очищаємо EasyMDE редактор
                        const textarea = this.$refs.messageEditor;
                        if (textarea && textarea.easymdeInstance) {
                            textarea.easymdeInstance.value('');
                            // Викликаємо автоматичне розширення після очищення
                            setTimeout(() => {
                                const codeMirror = textarea.easymdeInstance.codemirror;
                                const wrapper = codeMirror.getWrapperElement();
                                const scrollElement = wrapper.querySelector('.CodeMirror-scroll');
                                if (scrollElement) {
                                    scrollElement.style.height = '30px';
                                    scrollElement.style.overflowY = 'hidden';
                                }
                            }, 50);
                        }
                    })
                    .catch(error => {
                        console.error('Помилка відправки повідомлення:', error);
                        alert('Помилка відправки повідомлення. Спробуйте ще раз.');
                    });
                },

                listenForMessages() {
                    console.log('Підключення до WebSocket каналу:', `project.${this.projectId}`);

                    window.Echo.private(`project.${this.projectId}`)
                        .listen('.message.sent', (data) => {
                            console.log('Отримано повідомлення через WebSocket:', data);

                            // Встановлюємо правильне значення is_mine
                            data.message.is_mine = data.message.sender_id === this.userId;

                            // Додаємо повідомлення, якщо його ще немає в списку
                            if (!this.messages.some(m => m.id === data.message.id)) {
                                this.messages.push(data.message);
                                console.log('Додано нове повідомлення:', data.message);

                                // Прокручуємо до низу
                                this.$nextTick(() => {
                                    this.scrollToBottom();
                                });

                                // Обробка нового повідомлення
                                // Якщо це не наше повідомлення і вікно активне, одразу позначаємо як прочитане
                                if (!data.message.is_mine && document.visibilityState === 'visible') {
                                    this.$nextTick(() => {
                                        this.markMessagesAsRead();
                                    });
                                } else if (!data.message.is_mine) {
                                    // Якщо вікно неактивне, додаємо до списку непрочитаних
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
                            } else {
                                console.log('Повідомлення вже існує в списку:', data.message.id);
                            }
                        })
                        .listen('.messages.read', (data) => {
                            console.log('Отримано оновлення статусу прочитання:', data);

                            // Оновлюємо статус прочитання для повідомлень
                            if (data.message_ids && data.user_id !== this.userId) {
                                this.messages.forEach(msg => {
                                    if (data.message_ids.includes(msg.id) && msg.sender_id === this.userId) {
                                        msg.is_read = true;
                                        console.log(`Повідомлення ${msg.id} позначено як прочитане`);
                                    }
                                });
                            }
                        })
                        .error((error) => {
                            console.error('Помилка WebSocket з\'єднання:', error);
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
                },

                // Fallback метод для polling, якщо WebSocket не працює
                startPolling() {
                    console.log('Запуск polling режиму для оновлення повідомлень');

                    // Перевіряємо нові повідомлення кожні 3 секунди
                    this.pollingInterval = setInterval(() => {
                        this.fetchMessages();
                    }, 3000);
                },

                // Зупинка polling
                stopPolling() {
                    if (this.pollingInterval) {
                        clearInterval(this.pollingInterval);
                        this.pollingInterval = null;
                    }
                }
            };
        }
    </script>
    @endpush
@endif
