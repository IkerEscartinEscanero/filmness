import { ref } from 'vue';

const STORAGE_KEY = 'filmness-theme';

// Module-level ref so all components share the exact same reactive state
const isDarkMode = ref(true);

const applyTheme = (isDark) => {
    isDarkMode.value = isDark;
    document.documentElement.classList.toggle('dark', isDark);
    document.documentElement.classList.toggle('light', !isDark);
    document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
};

const initializeTheme = () => {
    const stored = window.localStorage.getItem(STORAGE_KEY);

    if (stored === 'dark' || stored === 'light') {
        applyTheme(stored === 'dark');
        return;
    }

    applyTheme(window.matchMedia('(prefers-color-scheme: dark)').matches);
};

const toggleTheme = () => {
    const nextIsDark = !isDarkMode.value;
    applyTheme(nextIsDark);
    window.localStorage.setItem(STORAGE_KEY, nextIsDark ? 'dark' : 'light');
};

export function useTheme() {
    return { isDarkMode, initializeTheme, toggleTheme };
}