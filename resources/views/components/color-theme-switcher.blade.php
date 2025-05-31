<div class="flex flex-wrap items-center gap-2" x-data="{ activeTheme: localStorage.getItem('colorTheme') || 'ocean' }">
    <!-- Нові градієнтні теми -->
    <button
        type="button"
        class="color-theme-btn"
        :class="{ 'active': activeTheme === 'yellow-green' }"
        style="background: linear-gradient(to right, rgb(234, 179, 8), rgb(163, 230, 53), rgb(74, 222, 128));"
        data-theme="yellow-green"
        @click="activeTheme = 'yellow-green'; setColorTheme('yellow-green')"
        title="{{ __('Жовто-зелена тема') }}"
        aria-label="{{ __('Жовто-зелена тема') }}"
    ></button>

    <button
        type="button"
        class="color-theme-btn"
        :class="{ 'active': activeTheme === 'blue-pink' }"
        style="background: linear-gradient(to right, rgb(56, 189, 248), rgb(186, 230, 253), rgb(249, 168, 212));"
        data-theme="blue-pink"
        @click="activeTheme = 'blue-pink'; setColorTheme('blue-pink')"
        title="{{ __('Блакитно-рожева тема') }}"
        aria-label="{{ __('Блакитно-рожева тема') }}"
    ></button>

    <button
        type="button"
        class="color-theme-btn"
        :class="{ 'active': activeTheme === 'green-purple' }"
        style="background: linear-gradient(to right, rgb(34, 197, 94), rgb(107, 33, 168), rgb(192, 132, 252));"
        data-theme="green-purple"
        @click="activeTheme = 'green-purple'; setColorTheme('green-purple')"
        title="{{ __('Зелено-фіолетова тема') }}"
        aria-label="{{ __('Зелено-фіолетова тема') }}"
    ></button>

    <button
        type="button"
        class="color-theme-btn"
        :class="{ 'active': activeTheme === 'pastel' }"
        style="background: linear-gradient(to right, rgb(202, 239, 215), rgb(245, 191, 215), rgb(171, 201, 233));"
        data-theme="pastel"
        @click="activeTheme = 'pastel'; setColorTheme('pastel')"
        title="{{ __('Пастельна тема (caefd7-f5bfd7-abc9e9)') }}"
        aria-label="{{ __('Пастельна тема (caefd7-f5bfd7-abc9e9)') }}"
    ></button>

    <button
        type="button"
        class="color-theme-btn"
        :class="{ 'active': activeTheme === 'vibrant' }"
        style="background: linear-gradient(to right, rgb(94, 222, 239), rgb(220, 233, 86), rgb(233, 143, 192));"
        data-theme="vibrant"
        @click="activeTheme = 'vibrant'; setColorTheme('vibrant')"
        title="{{ __('Яскрава тема (5edeef-dce956-e98fc0)') }}"
        aria-label="{{ __('Яскрава тема (5edeef-dce956-e98fc0)') }}"
    ></button>

    <!-- Існуючі багатокольорові теми -->
    <button
        type="button"
        class="color-theme-btn"
        :class="{ 'active': activeTheme === 'ocean' }"
        style="background: linear-gradient(to right, rgb(59, 130, 246), rgb(6, 182, 212), rgb(20, 184, 166));"
        data-theme="ocean"
        @click="activeTheme = 'ocean'; setColorTheme('ocean')"
        title="{{ __('Синьо-бірюзово-зелена тема') }}"
        aria-label="{{ __('Синьо-бірюзово-зелена тема') }}"
    ></button>

    <button
        type="button"
        class="color-theme-btn"
        :class="{ 'active': activeTheme === 'sunset' }"
        style="background: linear-gradient(to right, rgb(244, 63, 94), rgb(249, 115, 22), rgb(245, 158, 11));"
        data-theme="sunset"
        @click="activeTheme = 'sunset'; setColorTheme('sunset')"
        title="{{ __('Червоно-оранжево-жовта тема') }}"
        aria-label="{{ __('Червоно-оранжево-жовта тема') }}"
    ></button>

    <button
        type="button"
        class="color-theme-btn"
        :class="{ 'active': activeTheme === 'neon' }"
        style="background: linear-gradient(to right, rgb(236, 72, 153), rgb(217, 70, 239), rgb(168, 85, 247));"
        data-theme="neon"
        @click="activeTheme = 'neon'; setColorTheme('neon')"
        title="{{ __('Рожево-фіолетова тема') }}"
        aria-label="{{ __('Рожево-фіолетова тема') }}"
    ></button>
</div>
